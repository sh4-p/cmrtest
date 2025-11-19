<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Deal::with(['contact', 'stage', 'assignedTo'])
            ->latest();

        // Permission-based filtering
        if (!$user->can('view-all-deals')) {
            $query->where('assigned_to_id', $user->id);
        }

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Stage filter
        if ($request->has('stage_id')) {
            $query->where('deal_stage_id', $request->stage_id);
        }

        // Assigned to filter
        if ($request->has('assigned_to_id')) {
            $query->where('assigned_to_id', $request->assigned_to_id);
        }

        // Closing date range filter
        if ($request->has('closing_date_from')) {
            $query->where('closing_date', '>=', $request->closing_date_from);
        }
        if ($request->has('closing_date_to')) {
            $query->where('closing_date', '<=', $request->closing_date_to);
        }

        $deals = $query->paginate($request->get('per_page', 15));

        return response()->json($deals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Deal::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'deal_stage_id' => 'required|exists:deal_stages,id',
            'amount' => 'required|numeric|min:0',
            'closing_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'assigned_to_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $validated['assigned_to_id'] = $validated['assigned_to_id'] ?? $request->user()->id;
        $validated['probability'] = $validated['probability'] ?? 50;

        $deal = Deal::create($validated);

        return response()->json($deal->load(['contact', 'stage', 'assignedTo']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Deal $deal): JsonResponse
    {
        $this->authorize('view', $deal);

        return response()->json($deal->load(['contact', 'stage', 'assignedTo', 'activities']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deal $deal): JsonResponse
    {
        $this->authorize('update', $deal);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_id' => 'sometimes|required|exists:contacts,id',
            'deal_stage_id' => 'sometimes|required|exists:deal_stages,id',
            'amount' => 'sometimes|required|numeric|min:0',
            'closing_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'assigned_to_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $deal->update($validated);

        return response()->json($deal->load(['contact', 'stage', 'assignedTo']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deal $deal): JsonResponse
    {
        $this->authorize('delete', $deal);

        $deal->delete();

        return response()->json(['message' => 'Deal deleted successfully'], 200);
    }

    /**
     * Update deal stage
     */
    public function updateStage(Request $request, Deal $deal): JsonResponse
    {
        $this->authorize('update', $deal);

        $validated = $request->validate([
            'deal_stage_id' => 'required|exists:deal_stages,id',
        ]);

        $oldStage = $deal->stage;
        $deal->update($validated);
        $newStage = $deal->fresh()->stage;

        // Log activity
        Activity::create([
            'description' => "Deal stage changed from '{$oldStage->name}' to '{$newStage->name}'",
            'type' => 'Note',
            'user_id' => $request->user()->id,
            'subject_type' => Deal::class,
            'subject_id' => $deal->id,
            'activity_date' => now(),
        ]);

        return response()->json([
            'message' => 'Deal stage updated successfully',
            'deal' => $deal->load(['contact', 'stage', 'assignedTo'])
        ]);
    }
}
