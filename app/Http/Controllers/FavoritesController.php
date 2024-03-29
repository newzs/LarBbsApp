<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Reply $reply)
    {
        $reply->favorite();

        return back();//删除不要添加，会有bug
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }
}
