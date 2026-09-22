<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService): Response
    {
        return Inertia::render('Dashboard', [
            'user' => Auth::user(),
            'dashboard' => $dashboardService->index(),
        ]);
    }
}
