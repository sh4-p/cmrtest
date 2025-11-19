<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $query = Lead::with(['assignedTo', 'convertedToContact'])
            ->latest();

        // Permission-based filtering
        if (!$user->can('view-all-leads')) {
            $query->where('assigned_to_id', $user->id);
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

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Source filter
        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        // Assigned to filter
        if ($request->has('assigned_to_id')) {
            $query->where('assigned_to_id', $request->assigned_to_id);
        }

        $leads = $query->paginate($request->get('per_page', 15));

        return LeadResource::collection($leads);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeadRequest $request): LeadResource
    {
        $this->authorize('create', Lead::class);

        $validated = $request->validated();

        $validated['status'] = $validated['status'] ?? 'New';
        $validated['assigned_to_id'] = $validated['assigned_to_id'] ?? $request->user()->id;

        $lead = Lead::create($validated);

        return LeadResource::make($lead->load('assignedTo'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead): LeadResource
    {
        $this->authorize('view', $lead);

        return LeadResource::make($lead->load(['assignedTo', 'convertedToContact', 'activities']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadRequest $request, Lead $lead): LeadResource
    {
        $this->authorize('update', $lead);

        $validated = $request->validated();

        $lead->update($validated);

        return LeadResource::make($lead->load('assignedTo'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead): JsonResponse
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully'], 200);
    }

    /**
     * Convert lead to contact
     */
    public function convert(Request $request, Lead $lead): JsonResponse
    {
        $this->authorize('update', $lead);

        if ($lead->status === 'Converted') {
            return response()->json([
                'message' => 'Lead has already been converted'
            ], 400);
        }

        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $contact = $lead->convertToContact($validated);

        return response()->json([
            'message' => 'Lead converted successfully',
            'contact' => ContactResource::make($contact->load(['company', 'owner'])),
            'lead' => LeadResource::make($lead->fresh()->load('convertedToContact'))
        ], 200);
    }
}
