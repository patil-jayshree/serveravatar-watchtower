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

        // Check if user belongs to this organization
        $membership = $user->memberOf()
            ->where('organization_id', $organizationId)
            ->first();

        if (! $membership) {
            return redirect()->back()->with('error', 'You do not belong to this organization.');
        }

        // Store the selected organization ID in session
        session(['selected_organization_id' => $organizationId]);

        return redirect()->route('organizations.show', $organizationId);
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
        $membership = \App\Models\OrganizationMembership::where('user_id', $userId)
            ->orderBy('role')
            ->first();

        if ($membership) {
            session(['selected_organization_id' => $membership->organization_id]);
            return $membership->organization_id;
        }

        return null;
    }
}
