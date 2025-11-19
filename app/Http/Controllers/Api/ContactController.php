<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Contact::with(['company', 'owner'])
            ->latest();

        // Permission-based filtering
        if (!$user->can('view-all-contacts')) {
            $query->where('owner_id', $user->id);
        }

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Company filter
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Owner filter
        if ($request->has('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        $contacts = $query->paginate($request->get('per_page', 15));

        return response()->json($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Contact::class);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'owner_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['owner_id'] = $validated['owner_id'] ?? $request->user()->id;

        $contact = Contact::create($validated);

        return response()->json($contact->load(['company', 'owner']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact): JsonResponse
    {
        $this->authorize('view', $contact);

        return response()->json($contact->load(['company', 'owner', 'deals', 'activities']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact): JsonResponse
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:contacts,email,' . $contact->id,
            'phone_number' => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'owner_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $contact->update($validated);

        return response()->json($contact->load(['company', 'owner']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $this->authorize('delete', $contact);

        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully'], 200);
    }
}
