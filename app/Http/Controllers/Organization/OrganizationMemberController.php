<?php

namespace App\Http\Controllers\Organization;

use App\Actions\Organization\AddMember;
use App\Actions\Organization\RemoveMember;
use App\Actions\Organization\UpdateMemberRole;
use App\Enums\OrganizationRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\AddMemberRequest;
use App\Http\Requests\Organization\UpdateMemberRoleRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OrganizationMemberController extends Controller
{
    /**
     * Display the organization's members.
     */
    public function index(Request $request): View
    {
        $organization = $request->attributes->get('organization');
        $members = $organization->memberships()->with('user')->get();

        return view('organizations.members.index', [
            'organization' => $organization,
            'members' => $members,
        ]);
    }

    /**
     * Add a member to the organization.
     */
    public function store(AddMemberRequest $request, AddMember $action, Organization $organization): RedirectResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $action->execute($organization, $user, OrganizationRole::Member);

        return back()->with('status', 'Member added successfully.');
    }

    /**
     * Update a member's role.
     */
    public function update(UpdateMemberRoleRequest $request, UpdateMemberRole $action, Organization $organization, User $user): RedirectResponse
    {
        $action->execute($organization, $user, OrganizationRole::from($request->role));

        return back()->with('status', 'Member role updated successfully.');
    }

    /**
     * Remove a member from the organization.
     */
    public function destroy(Request $request, Organization $organization, User $user): RedirectResponse
    {
        $action = app(RemoveMember::class);
        $action->execute($organization, $user);

        return back()->with('status', 'Member removed successfully.');
    }
}
