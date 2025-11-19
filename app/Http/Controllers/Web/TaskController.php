<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        return Inertia::render('Tasks/Index');
    }

    public function create()
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('Tasks/Create', [
            'users' => $users,
        ]);
    }

    public function show(string $id)
    {
        return Inertia::render('Tasks/Show', [
            'taskId' => $id,
        ]);
    }

    public function edit(string $id)
    {
        $users = User::select('id', 'name')->where('is_active', true)->get();

        return Inertia::render('Tasks/Edit', [
            'taskId' => $id,
            'users' => $users,
        ]);
    }
}
