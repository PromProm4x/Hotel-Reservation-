<?php

namespace App\Http\Controllers;

use App\Models\RoomType;

class HomeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::query()
            ->active()
            ->orderBy('base_price')
            ->get();

        return view('home', compact('roomTypes'));
    }
}
