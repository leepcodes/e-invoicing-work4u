<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RegisterService;
use App\Http\Requests\Auth\RegisterRequest;
use Inertia\Response;
class RegisterController extends Controller
{
    public function show(): Response
    {
        return inertia('auth/Register');
    }
    public function store(RegisterRequest $request, RegisterService $service){

        $service->register($request->validated());
        return redirect()->route('login')
        ->with('success', 'Account created successfully. Please log in.');
    }
}
