<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $search = request('name');

        return User::where('name','LIKE',"$search%")
            ->take(5)
            ->pluck('name');
    }
}
