<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginLog::with('user')->latest('created_at');

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('email', 'like', "%{$search}%");
        }

        $logs = $query->paginate(50)->withQueryString();

        return view('admin.login-logs.index', compact('logs'));
    }
}
