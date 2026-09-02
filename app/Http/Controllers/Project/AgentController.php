<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
            'agentToken' => $agentToken?->token,
            'isConnected' => $isConnected,
        ]);
    }

    /**
     * Generate a new agent token for the project.
     */
    public function generate(Request $request, Organization $organization, Project $project): RedirectResponse
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

        return redirect()->back()->with('token', $tokenData['token']);
    }

    /**
     * Regenerate the agent token.
     */
    public function regenerate(Request $request, Organization $organization, Project $project): RedirectResponse
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

        return redirect()->back()->with('token', $tokenData['token']);
    }

    /**
     * Revoke the agent token.
     */
    public function revoke(Request $request, Organization $organization, Project $project): RedirectResponse
    {
        if ($project->agentToken) {
            $project->agentToken->revoke();
        }

        return redirect()->back();
    }
}
