<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $users = User::query()
            ->orderBy('email')
            ->get(['id', 'name', 'email', 'role']);

        return view('admin.index', compact('users'));
    }

    public function grantRedactor(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        User::query()
            ->where('id', $data['user_id'])
            ->where('role', '!=', 'Admin')
            ->update(['role' => 'Redactor']);

        return redirect()->route('admin.index');
    }

    public function revokeRedactor(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        User::query()
            ->where('id', $data['user_id'])
            ->where('role', 'Redactor')
            ->update(['role' => 'User']);

        return redirect()->route('admin.index');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'Admin', 403);
    }
}
