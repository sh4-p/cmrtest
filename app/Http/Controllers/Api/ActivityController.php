<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Activity::with(['user', 'subject'])
            ->latest('activity_date');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by subject
        if ($request->has('subject_type') && $request->has('subject_id')) {
            $query->where('subject_type', $request->subject_type)
                ->where('subject_id', $request->subject_id);
        }

        $activities = $query->paginate($request->get('per_page', 15));

        return response()->json($activities);
    }

    /**
     * Get recent activities
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $activities = Activity::with(['user', 'subject'])
            ->latest('activity_date')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $activities
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'type' => 'required|string|in:Call,Meeting,Email,Note',
            'subject_type' => 'required|string',
            'subject_id' => 'required|integer',
            'activity_date' => 'nullable|date',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['activity_date'] = $validated['activity_date'] ?? now();

        $activity = Activity::create($validated);

        return response()->json($activity->load(['user', 'subject']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): JsonResponse
    {
        return response()->json($activity->load(['user', 'subject']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|in:Call,Meeting,Email,Note',
            'activity_date' => 'sometimes|nullable|date',
        ]);

        $activity->update($validated);

        return response()->json($activity->load(['user', 'subject']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity): JsonResponse
    {
        $activity->delete();

        return response()->json(['message' => 'Activity deleted successfully'], 200);
    }
}
