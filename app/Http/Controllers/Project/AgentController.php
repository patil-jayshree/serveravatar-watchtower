<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class AgentController extends Controller
{
    /**
     * Display the agent setup page for a project.
     */
    public function show(Request $request, Organization $organization, Project $project)
    {
        // Check if project has an active agent token
        $agentToken = $project->agentToken;
        $isConnected = $project->is_connected;

        // Raw token passed as query param only on first generation (not stored in DB)
        $rawToken = $request->query('token');
        $justGenerated = ! empty($rawToken);

        // Show raw token only immediately after generation, otherwise show masked token
        $agentTokenValue = $justGenerated
            ? $rawToken
            : ($agentToken?->masked_token ?? null);

        return Inertia::render('Projects/Agent', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'logo_url' => $organization->logo_url,
            ],
            'project' => [
                'id' => $project->id,
                'uuid' => $project->uuid,
                'name' => $project->name,
                'is_connected' => $project->is_connected,
                'last_connected_at' => $project->last_connected_at,
            ],
            'agentToken' => $agentTokenValue,
            'rawToken' => $rawToken,
            'justGenerated' => $justGenerated,
            'isConnected' => $isConnected,
        ]);
    }

    /**
     * Generate a new agent token for the project.
     */
    public function generate(Request $request, Organization $organization, Project $project): JsonResponse
    {
        // Revoke existing token if any
        if ($project->agentToken) {
            $project->agentToken->revoke();
        }

        // Generate token using model method
        $tokenData = \App\Models\AgentToken::generateToken();

        // Create new token
        $token = \App\Models\AgentToken::create([
            'project_id' => $project->id,
            'token_prefix' => $tokenData['prefix'],
            'token_hash' => $tokenData['hash'],
            'status' => \App\Enums\Agent\AgentTokenStatus::Active,
        ]);

        // Return JSON response for AJAX handling (no page reload, no scroll reset)
        return response()->json([
            'token' => $tokenData['token'],
            'masked' => $token->masked_token,
        ]);
    }

    /**
     * Regenerate the agent token.
     */
    public function regenerate(Request $request, Organization $organization, Project $project): JsonResponse
    {
        // Revoke existing token
        if ($project->agentToken) {
            $project->agentToken->revoke();
        }

        // Generate token using model method
        $tokenData = \App\Models\AgentToken::generateToken();

        // Create new token
        $token = \App\Models\AgentToken::create([
            'project_id' => $project->id,
            'token_prefix' => $tokenData['prefix'],
            'token_hash' => $tokenData['hash'],
            'status' => \App\Enums\Agent\AgentTokenStatus::Active,
        ]);

        // Return JSON response for AJAX handling (no page reload, no scroll reset)
        return response()->json([
            'token' => $tokenData['token'],
            'masked' => $token->masked_token,
        ]);
    }

    /**
     * Revoke the agent token.
     */
    public function revoke(Request $request, Organization $organization, Project $project): JsonResponse
    {
        if ($project->agentToken) {
            $project->agentToken->revoke();
        }

        // Return JSON response for AJAX handling (no page reload, no scroll reset)
        return response()->json(['revoked' => true]);
    }
}
