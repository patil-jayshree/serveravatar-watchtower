<?php

namespace App\Http\Controllers\Organization;

use App\Actions\Organization\CreateOrganization;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the user's organizations.
     */
    public function index(): View
    {
        $user = Auth::user();
        $organizations = $user->memberOf()->with('organization')->get()->pluck('organization');

        return view('organizations.index', [
            'organizations' => $organizations,
        ]);
    }

    /**
     * Display the organization's overview.
     */
    public function show(Request $request): View
    {
        $organization = $request->attributes->get('organization');

        return view('organizations.show', [
            'organization' => $organization,
        ]);
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create(): View
    {
        return view('organizations.create');
    }

    /**
     * Store a newly created organization.
     */
    public function store(CreateOrganizationRequest $request, CreateOrganization $action): RedirectResponse
    {
        $organization = $action->execute(Auth::user(), $request->validated());

        return redirect()->route('organizations.show', $organization)->with('status', 'Organization created successfully.');
    }
}
