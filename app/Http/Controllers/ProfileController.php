<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user(),
            'sessions' => $this->getSessionsProperty(),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
            'social_links' => $request->social_links,
        ]);

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatars');
        }

        return back()->with('status', 'Profile updated successfully');
    }
}