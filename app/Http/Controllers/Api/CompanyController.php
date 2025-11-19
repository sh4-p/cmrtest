<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $query = Company::with(['owner', 'contacts'])
            ->latest();

        // Permission-based filtering
        if (!$user->can('view-all-companies')) {
            $query->where('owner_id', $user->id);
        }

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('industry', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%");
            });
        }

        // Industry filter
        if ($request->has('industry')) {
            $query->where('industry', $request->industry);
        }

        // Owner filter
        if ($request->has('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        $companies = $query->paginate($request->get('per_page', 15));

        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request): CompanyResource
    {
        $this->authorize('create', Company::class);

        $validated = $request->validated();

        $validated['owner_id'] = $validated['owner_id'] ?? $request->user()->id;

        $company = Company::create($validated);

        return CompanyResource::make($company->load('owner'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): CompanyResource
    {
        $this->authorize('view', $company);

        return CompanyResource::make($company->load(['owner', 'contacts']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company): CompanyResource
    {
        $this->authorize('update', $company);

        $validated = $request->validated();

        $company->update($validated);

        return CompanyResource::make($company->load('owner'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): JsonResponse
    {
        $this->authorize('delete', $company);

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully'], 200);
    }
}
