<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwitchOrganizationController extends Controller
{
    /**
     * Switch to a different organization.
     */
    public function switch(Request $request, string $organizationId): RedirectResponse
    {
        $user = Auth::user();

        // Find the organization
        $organization = \App\Models\Organization::findOrFail($organizationId);

        // Check if user owns this organization
        if ($organization->user_id !== $user->id) {
            return redirect()->back()->with('error', 'You do not own this organization.');
        }

        // Store the selected organization ID in session
        session(['selected_organization_id' => $organization->id]);

        return redirect()->route('organizations.show', $organization);
    }

    /**
     * Get the currently selected organization ID.
     */
    public static function getSelectedOrganizationId(): ?int
    {
        return session('selected_organization_id');
    }

    /**
     * Select a default organization for the user.
     */
    public static function selectDefaultOrganization(int $userId): ?int
    {
        $organization = \App\Models\Organization::where('user_id', $userId)->first();

        if ($organization) {
            session(['selected_organization_id' => $organization->id]);
            return $organization->id;
        }

        return null;
    }
}
