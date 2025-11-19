<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class LeadController extends Controller
{
    /**
     * Display a listing of the leads.
     */
    public function index()
    {
        return Inertia::render('Leads/Index');
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('Leads/Create', [
            'users' => $users,
        ]);
    }

    /**
     * Display the specified lead.
     */
    public function show(string $id)
    {
        return Inertia::render('Leads/Show', [
            'leadId' => $id,
        ]);
    }

    /**
     * Show the form for editing the specified lead.
     */
    public function edit(string $id)
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('Leads/Edit', [
            'leadId' => $id,
            'users' => $users,
        ]);
    }
}
