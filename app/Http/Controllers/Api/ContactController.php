<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
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

        return ContactResource::collection($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactRequest $request): ContactResource
    {
        $this->authorize('create', Contact::class);

        $validated = $request->validated();

        $validated['owner_id'] = $validated['owner_id'] ?? $request->user()->id;

        $contact = Contact::create($validated);

        return ContactResource::make($contact->load(['company', 'owner']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact): ContactResource
    {
        $this->authorize('view', $contact);

        return ContactResource::make($contact->load(['company', 'owner', 'deals', 'activities']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactRequest $request, Contact $contact): ContactResource
    {
        $this->authorize('update', $contact);

        $validated = $request->validated();

        $contact->update($validated);

        return ContactResource::make($contact->load(['company', 'owner']));
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
