<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contacts/Index');
    }

    public function create()
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();
        $companies = Company::select('id', 'name')->get();

        return Inertia::render('Contacts/Create', [
            'users' => $users,
            'companies' => $companies,
        ]);
    }

    public function show(string $id)
    {
        return Inertia::render('Contacts/Show', [
            'contactId' => $id,
        ]);
    }

    public function edit(string $id)
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();
        $companies = Company::select('id', 'name')->get();

        return Inertia::render('Contacts/Edit', [
            'contactId' => $id,
            'users' => $users,
            'companies' => $companies,
        ]);
    }
}
