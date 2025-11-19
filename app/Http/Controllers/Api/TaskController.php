<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use AppHttpResourcesTaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use IlluminateHttpResourcesJsonAnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $query = Task::with(['assignedTo', 'relatedTo'])
            ->latest();

        // Permission-based filtering
        if (!$user->can('view-all-tasks')) {
            $query->where('assigned_to_id', $user->id);
        }

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Due date filter
        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        // Overdue filter
        if ($request->has('overdue') && $request->overdue) {
            $query->overdue();
        }

        // Assigned to filter
        if ($request->has('assigned_to_id')) {
            $query->where('assigned_to_id', $request->assigned_to_id);
        }

        $tasks = $query->paginate($request->get('per_page', 15));

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): TaskResource
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();

        $validated['status'] = $validated['status'] ?? 'Pending';
        $validated['priority'] = $validated['priority'] ?? 'Medium';
        $validated['assigned_to_id'] = $validated['assigned_to_id'] ?? $request->user()->id;

        $task = Task::create($validated);

        return TaskResource::make($task->load(['assignedTo', 'relatedTo']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        return TaskResource::make($task->load(['assignedTo', 'relatedTo']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        $task->update($validated);

        return TaskResource::make($task->load(['assignedTo', 'relatedTo']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }

    /**
     * Mark task as completed
     */
    public function complete(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task->markAsCompleted();

        return response()->json([
            'message' => 'Task marked as completed',
            'task' => $task->fresh()->load(['assignedTo', 'relatedTo'])
        ]);
    }
}
