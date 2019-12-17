<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;

class LockedThreadsController extends Controller
{
    public function store(Thread $thread)
    {
        $thread->lock();
    }

    public function destroy(Thread $thread)
    {
        $thread->update(['locked' => false]);
    }
}
