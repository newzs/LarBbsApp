<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Activity;

class ProfilesController extends Controller {
    public function show(User $user)
    {
        return view('profiles.show', [
            'profileUser' => $user,
            'activities' =>  Activity::feed($user)
        ]);
    }
}
