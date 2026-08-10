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
        $projectParam = $request->route('project');

        // If already resolved to a Project (via route model binding), use it
        if ($projectParam instanceof Project) {
            $project = $projectParam;
        } else {
            // Otherwise lookup by UUID
            $project = Project::where('uuid', $projectParam)->firstOrFail();
        }

        // Eager load organization relationship
        $project->load('organization');

        // Check if user owns this project through the organization
        if ($project->organization->user_id !== Auth::id()) {
            abort(403, 'You do not have access to this project.');
        }

        // Store project in request attributes for easy access
        $request->attributes->set('project', $project);

        return $next($request);
    }
}
