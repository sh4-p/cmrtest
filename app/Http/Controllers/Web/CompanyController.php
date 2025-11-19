<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        return Inertia::render('Companies/Index');
    }

    public function create()
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('Companies/Create', [
            'users' => $users,
        ]);
    }

    public function show(string $id)
    {
        return Inertia::render('Companies/Show', [
            'companyId' => $id,
        ]);
    }

    public function edit(string $id)
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('Companies/Edit', [
            'companyId' => $id,
            'users' => $users,
        ]);
    }
}
