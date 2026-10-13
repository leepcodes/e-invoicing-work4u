<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Country;

class LandingController extends Controller
{
    public function index(): Response
    {
        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'code',
                'name',
                'phone_code',
            ]);

        return inertia('landing/Index', [
            'countries' => $countries,
        ]);
    }
}
