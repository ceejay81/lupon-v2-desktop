<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showForm(): \Illuminate\View\View
    {
        return view('auth.forgot-password');
    }

    public function checkEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function reset(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::where('email', $request->email)->first()
            ->update(['password' => Hash::make($request->password)]);

        return redirect()->route('login')->with('status', 'Password reset successfully. You can now log in.');
    }
}
