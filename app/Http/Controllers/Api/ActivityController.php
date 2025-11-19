<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityRequest;
use AppHttpResourcesActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use IlluminateHttpResourcesJsonAnonymousResourceCollection;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
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

        return ActivityResource::collection($activities);
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
            ActivityResource::collection($activities)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActivityRequest $request): ActivityResource
    {
        $validated = $request->validated();

        $validated['user_id'] = $request->user()->id;
        $validated['activity_date'] = $validated['activity_date'] ?? now();

        $activity = Activity::create($validated);

        return ActivityResource::make($activity->load(['user', 'subject']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): ActivityResource
    {
        return ActivityResource::make($activity->load(['user', 'subject']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ActivityRequest $request, Activity $activity): ActivityResource
    {
        $validated = $request->validated();

        $activity->update($validated);

        return ActivityResource::make($activity->load(['user', 'subject']));
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
