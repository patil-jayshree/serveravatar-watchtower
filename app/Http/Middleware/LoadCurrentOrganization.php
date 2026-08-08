<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoadCurrentOrganization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $organization = $request->route('organization');

        if (! $organization instanceof Organization) {
            $organizationId = $request->route('organization');
            $organization = Organization::findOrFail($organizationId);
        }

        // Check if user belongs to this organization
        if (! $organization->hasMember(Auth::user())) {
            abort(403, 'You do not have access to this organization.');
        }

        // Store organization in request attributes for easy access
        $request->attributes->set('organization', $organization);

        return $next($request);
    }
}
