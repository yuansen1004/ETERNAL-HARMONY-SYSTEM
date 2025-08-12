<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function showLoginForm(){
        return view('auth.login');
    }

    public function register(Request $request)
    {
        // Only staff can register new users
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can register new users.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'in:staff,agent'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        $userName = $user->name;

        return redirect()->route('adminStaff')->with('success', '"' . $userName . '" registered successfully.');
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))){
            $request->session()->regenerate();

            if (Auth::user()->role === 'agent'){
                return redirect()->intended('/dashboard');
            }
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Wrong email or password.',
        ])->onlyInput('email');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function adminStaff()
    {
        // Only staff can access user management
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            abort(403, 'Only staff can access user management.');
        }

        $users = User::all();
        return view('adminStaff', compact('users'));
    }

    public function destroy(User $user)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff' || Auth::user()->id === $user->id) {
            abort(403, 'Unauthorized action or cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        return Redirect::back()->with('success', '"' . $userName . '" deleted successfully.');
    }
}