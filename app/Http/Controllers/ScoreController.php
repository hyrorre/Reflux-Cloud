<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Chartstat;

class ScoreController extends Controller {
    public function index() {
        $user = Auth::user();
        return Inertia::render('Score/Index', [
            'user' => $user
        ]);
    }
}
