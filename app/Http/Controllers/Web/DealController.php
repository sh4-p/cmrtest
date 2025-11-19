<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\DealStage;
use App\Models\User;
use Inertia\Inertia;

class DealController extends Controller
{
    public function index()
    {
        return Inertia::render('Deals/Index');
    }

    public function create()
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();
        $contacts = Contact::select('id', 'first_name', 'last_name')->get();
        $stages = DealStage::orderBy('order')->get();

        return Inertia::render('Deals/Create', [
            'users' => $users,
            'contacts' => $contacts,
            'stages' => $stages,
        ]);
    }

    public function show(string $id)
    {
        return Inertia::render('Deals/Show', [
            'dealId' => $id,
        ]);
    }

    public function edit(string $id)
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();
        $contacts = Contact::select('id', 'first_name', 'last_name')->get();
        $stages = DealStage::orderBy('order')->get();

        return Inertia::render('Deals/Edit', [
            'dealId' => $id,
            'users' => $users,
            'contacts' => $contacts,
            'stages' => $stages,
        ]);
    }
}
