<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of admins.
     */
    public function index()
    {
        return view('admin.admins.index');
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        // Logic to store admin
    }

    /**
     * Display the specified admin.
     */
    public function show(string $id)
    {
        return view('admin.admins.show', compact('id'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(string $id)
    {
        return view('admin.admins.edit', compact('id'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, string $id)
    {
        // Logic to update admin
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(string $id)
    {
        // Logic to delete admin
    }
}
