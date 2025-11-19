<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get counts based on user permissions
        $stats = [
            'leads' => [
                'total' => Lead::when(
                    !$user->can('view-all-leads'),
                    fn($query) => $query->where('assigned_to_id', $user->id)
                )->count(),
                'new' => Lead::where('status', 'New')
                    ->when(
                        !$user->can('view-all-leads'),
                        fn($query) => $query->where('assigned_to_id', $user->id)
                    )->count(),
                'converted' => Lead::where('status', 'Converted')
                    ->when(
                        !$user->can('view-all-leads'),
                        fn($query) => $query->where('assigned_to_id', $user->id)
                    )->count(),
            ],
            'contacts' => [
                'total' => Contact::when(
                    !$user->can('view-all-contacts'),
                    fn($query) => $query->where('owner_id', $user->id)
                )->count(),
            ],
            'companies' => [
                'total' => Company::when(
                    !$user->can('view-all-companies'),
                    fn($query) => $query->where('owner_id', $user->id)
                )->count(),
            ],
            'deals' => [
                'total' => Deal::when(
                    !$user->can('view-all-deals'),
                    fn($query) => $query->where('assigned_to_id', $user->id)
                )->count(),
                'active' => Deal::whereHas('stage', function ($query) {
                    $query->whereNotIn('name', ['Won', 'Lost']);
                })->when(
                    !$user->can('view-all-deals'),
                    fn($query) => $query->where('assigned_to_id', $user->id)
                )->count(),
                'total_value' => Deal::when(
                    !$user->can('view-all-deals'),
                    fn($query) => $query->where('assigned_to_id', $user->id)
                )->sum('amount'),
            ],
            'tasks' => [
                'total' => Task::when(
                    !$user->can('view-all-tasks'),
                    fn($query) => $query->where('assigned_to_id', $user->id)
                )->count(),
                'pending' => Task::where('status', 'Pending')
                    ->when(
                        !$user->can('view-all-tasks'),
                        fn($query) => $query->where('assigned_to_id', $user->id)
                    )->count(),
                'overdue' => Task::where('due_date', '<', now())
                    ->where('status', '!=', 'Completed')
                    ->when(
                        !$user->can('view-all-tasks'),
                        fn($query) => $query->where('assigned_to_id', $user->id)
                    )->count(),
            ],
        ];

        return response()->json($stats);
    }
}
