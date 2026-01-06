<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the user profile.
     */
    public function profile()
    {
        return view('user.profile');
    }

    /**
     * Update the user profile.
     */
    public function updateProfile(Request $request)
    {
        // Logic to update user profile
    }

    /**
     * Display user settings.
     */
    public function settings()
    {
        return view('user.settings');
    }

    /**
     * Update user settings.
     */
    public function updateSettings(Request $request)
    {
        // Logic to update user settings
    }
}
