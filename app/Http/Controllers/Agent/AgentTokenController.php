<?php

declare(strict_types=1);

namespace App\Http\Controllers\Agent;

use App\Actions\Agent\GenerateAgentToken;
use App\Actions\Agent\RegenerateAgentToken;
use App\Actions\Agent\RevokeAgentToken;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgentTokenController extends Controller
{
    /**
     * Display the agent token page.
     */
    public function show(Request $request): View|JsonResponse
    {
        $project = $request->attributes->get('project');
        $token = $project->agentToken;
        $rawToken = $request->query('token');
        $justGenerated = ! empty($rawToken);

        // Note: Authorization is handled by LoadCurrentProject middleware

        // If requesting JSON (AJAX), return JSON
        if ($request->expectsJson()) {
            if (! $token) {
                return response()->json([
                    'status' => 'not_generated',
                    'masked' => null,
                ]);
            }

            return response()->json([
                'status' => $token->status->value,
                'masked' => $token->masked_token,
                'created_at' => $token->created_at->toIso8601String(),
                'revoked_at' => $token->revoked_at?->toIso8601String(),
            ]);
        }

        // Return React page
        return Inertia::render('Projects/Agent', [
            'organization' => [
                'id' => $project->organization->id,
                'name' => $project->organization->name,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'agentToken' => $token,
            'rawToken' => $rawToken,
            'justGenerated' => $justGenerated,
        ]);
    }

    /**
     * Generate a new agent token.
     */
    public function store(Request $request, GenerateAgentToken $action): JsonResponse
    {
        $project = $request->attributes->get('project');

        // Note: Authorization is handled by LoadCurrentProject middleware
        // Gate::authorize('createAgentToken', $project);

        $result = $action->execute($project);

        if ($result['token'] === null) {
            // Token already exists
            return response()->json([
                'status' => $result['status'],
                'masked' => $result['masked'],
                'token' => null,
                'message' => 'An active token already exists.',
            ], 200);
        }

        return response()->json([
            'status' => $result['status'],
            'masked' => $result['masked'],
            'token' => $result['token'],
            'message' => 'Token generated successfully. Store this token securely — it will not be shown again.',
        ], 201);
    }

    /**
     * Regenerate the agent token.
     */
    public function update(Request $request, RegenerateAgentToken $action): JsonResponse
    {
        $project = $request->attributes->get('project');

        // Note: Authorization is handled by LoadCurrentProject middleware

        $result = $action->execute($project);

        return response()->json([
            'status' => $result['status'],
            'masked' => $result['masked'],
            'token' => $result['token'],
            'message' => 'Token regenerated successfully. Store this token securely — it will not be shown again.',
        ]);
    }

    /**
     * Revoke the agent token.
     */
    public function destroy(Request $request, RevokeAgentToken $action): JsonResponse
    {
        $project = $request->attributes->get('project');

        // Note: Authorization is handled by LoadCurrentProject middleware

        $action->execute($project);

        return response()->json([
            'status' => 'revoked',
            'message' => 'Token revoked successfully.',
        ]);
    }
}
