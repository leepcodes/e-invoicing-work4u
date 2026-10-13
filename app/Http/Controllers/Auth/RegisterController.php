<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RegisterService;
use App\Http\Requests\Auth\RegisterRequest;
use Inertia\Response;
use App\Models\Country;

class RegisterController extends Controller
{
    public function show(): Response
    {
        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'phone_code',
            ]);

        return inertia('auth/Register', [
            'countries' => $countries,
        ]);
    }

    public function store(RegisterRequest $request, RegisterService $service){

        $service->register($request->validated());
        return redirect()->route('login')
        ->with('success', 'Account created successfully. Please log in.');
    }
}
