<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class PrivacyController extends Controller
{
    public function policy()
    {
        return view('public.privacy.policy');
    }

    public function cookies()
    {
        return view('public.privacy.cookies');
    }
}
