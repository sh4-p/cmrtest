<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        return Inertia::render('Activities/Index');
    }

    public function create()
    {
        return Inertia::render('Activities/Create');
    }

    public function show(string $id)
    {
        return Inertia::render('Activities/Show', [
            'activityId' => $id,
        ]);
    }

    public function edit(string $id)
    {
        return Inertia::render('Activities/Edit', [
            'activityId' => $id,
        ]);
    }
}
