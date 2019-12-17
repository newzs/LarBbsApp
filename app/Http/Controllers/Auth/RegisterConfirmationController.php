<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterConfirmationController extends Controller
{
    public function index()
    {
        try {
            User::where('confirmation_token',request('token'))
                ->firstOrFail()
                ->confirm();
        }catch (\Exception $e) {
            return redirect(route('threads'))
                ->with('flash','Unknown token.');
        }

        return redirect('/threads')
            ->with('flash','Your account is now confirmed! You may post to the forum.');
    }
}
