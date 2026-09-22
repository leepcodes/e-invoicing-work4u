<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\LoginService;
use Inertia\Response;

class LoginController extends Controller
{
    public function show(): Response
    {
        return inertia('auth/Login');
    }

    public function store(LoginRequest $request, LoginService $service)
    {
        $service->login($request->validated());

        return redirect()->route('dashboard')
            ->with('success', 'Welcome back!');
    }
}
