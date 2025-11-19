<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Global search across multiple entities
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();
        $results = [];

        // Search Leads
        if ($user->can('view-leads')) {
            $leadsQuery = Lead::query()
                ->where(function ($q) use ($query) {
                    $q->where('first_name', 'LIKE', "%{$query}%")
                      ->orWhere('last_name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('company', 'LIKE', "%{$query}%");
                });

            if (!$user->can('view-all-leads')) {
                $leadsQuery->where('assigned_to_id', $user->id);
            }

            $leads = $leadsQuery->limit(5)->get();

            foreach ($leads as $lead) {
                $results[] = [
                    'id' => $lead->id,
                    'type' => 'Lead',
                    'name' => $lead->first_name . ' ' . $lead->last_name,
                    'subtitle' => $lead->email . ($lead->company ? ' • ' . $lead->company : ''),
                ];
            }
        }

        // Search Contacts
        if ($user->can('view-contacts')) {
            $contactsQuery = Contact::query()
                ->where(function ($q) use ($query) {
                    $q->where('first_name', 'LIKE', "%{$query}%")
                      ->orWhere('last_name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                });

            if (!$user->can('view-all-contacts')) {
                $contactsQuery->where('owner_id', $user->id);
            }

            $contacts = $contactsQuery->with('company')->limit(5)->get();

            foreach ($contacts as $contact) {
                $results[] = [
                    'id' => $contact->id,
                    'type' => 'Contact',
                    'name' => $contact->first_name . ' ' . $contact->last_name,
                    'subtitle' => $contact->email . ($contact->company ? ' • ' . $contact->company->name : ''),
                ];
            }
        }

        // Search Companies
        if ($user->can('view-companies')) {
            $companiesQuery = Company::query()
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('industry', 'LIKE', "%{$query}%")
                      ->orWhere('website', 'LIKE', "%{$query}%");
                });

            if (!$user->can('view-all-companies')) {
                $companiesQuery->where('owner_id', $user->id);
            }

            $companies = $companiesQuery->limit(5)->get();

            foreach ($companies as $company) {
                $results[] = [
                    'id' => $company->id,
                    'type' => 'Company',
                    'name' => $company->name,
                    'subtitle' => $company->industry ?? $company->website,
                ];
            }
        }

        // Search Deals
        if ($user->can('view-deals')) {
            $dealsQuery = Deal::query()
                ->where('name', 'LIKE', "%{$query}%");

            if (!$user->can('view-all-deals')) {
                $dealsQuery->where('assigned_to_id', $user->id);
            }

            $deals = $dealsQuery->with('contact', 'stage')->limit(5)->get();

            foreach ($deals as $deal) {
                $results[] = [
                    'id' => $deal->id,
                    'type' => 'Deal',
                    'name' => $deal->name,
                    'subtitle' => '$' . number_format($deal->amount, 2) .
                                 ($deal->contact ? ' • ' . $deal->contact->first_name . ' ' . $deal->contact->last_name : ''),
                ];
            }
        }

        // Search Tasks
        if ($user->can('view-tasks')) {
            $tasksQuery = Task::query()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                });

            if (!$user->can('view-all-tasks')) {
                $tasksQuery->where('assigned_to_id', $user->id);
            }

            $tasks = $tasksQuery->limit(5)->get();

            foreach ($tasks as $task) {
                $results[] = [
                    'id' => $task->id,
                    'type' => 'Task',
                    'name' => $task->title,
                    'subtitle' => $task->status . ($task->due_date ? ' • Due ' . $task->due_date->format('M d, Y') : ''),
                ];
            }
        }

        return response()->json($results);
    }
}
