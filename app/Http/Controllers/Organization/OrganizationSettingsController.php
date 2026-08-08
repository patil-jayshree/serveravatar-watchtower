<?php

namespace App\Http\Controllers\Organization;

use App\Actions\Organization\DeleteOrganization;
use App\Actions\Organization\UpdateLogo;
use App\Actions\Organization\UpdateOrganization;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationSettingsController extends Controller
{
    /**
     * Display the organization's settings.
     */
    public function edit(Request $request): View
    {
        $organization = $request->attributes->get('organization');

        return view('organizations.settings.edit', [
            'organization' => $organization,
        ]);
    }

    /**
     * Update the organization's settings.
     */
    public function update(UpdateOrganizationRequest $request, UpdateOrganization $action, DeleteOrganization $deleteAction): RedirectResponse
    {
        $organization = $request->attributes->get('organization');

        // Check if this is a delete request
        if ($request->has('delete')) {
            $deleteAction->execute($organization);

            return redirect()->route('organizations.index')->with('status', 'Organization deleted successfully.');
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoAction = app(UpdateLogo::class);
            $logoAction->execute($organization, $request->file('logo'));
        } elseif ($request->boolean('remove_logo')) {
            $logoAction = app(UpdateLogo::class);
            $logoAction->execute($organization, null);
        }

        // Update basic info
        $action->execute($organization, $request->only(['name']));

        return back()->with('status', 'Organization updated successfully.');
    }
}
