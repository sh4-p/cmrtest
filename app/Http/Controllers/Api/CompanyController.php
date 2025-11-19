<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
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

        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Company::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['owner_id'] = $validated['owner_id'] ?? $request->user()->id;

        $company = Company::create($validated);

        return response()->json($company->load('owner'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): JsonResponse
    {
        $this->authorize('view', $company);

        return response()->json($company->load(['owner', 'contacts']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $company->update($validated);

        return response()->json($company->load('owner'));
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
