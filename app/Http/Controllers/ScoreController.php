<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Chartstat;
use App\Models\User;

class ScoreController extends Controller {
    public function index() {
        $user = Auth::user();
        return Inertia::render('Score/Index', [
            'user' => $user
        ]);
    }

    public function rival($name) {
        $user = Auth::user();
        $rival = User::where('name', '=', $name)->select('id', 'name', 'iidxid', 'infinitasid', 'scope')->first();
        return Inertia::render('Score/Index', [
            'user' => $user,
            'rival' => $rival
        ]);
    }
}
