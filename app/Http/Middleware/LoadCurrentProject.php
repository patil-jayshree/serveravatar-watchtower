<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoadCurrentProject
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');

        if (! $project instanceof Project) {
            $projectId = $request->route('project');
            $project = Project::findOrFail($projectId);
        }

        // Check if user owns this project through the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403, 'You do not have access to this project.');
        }

        // Store project in request attributes for easy access
        $request->attributes->set('project', $project);

        return $next($request);
    }
}
