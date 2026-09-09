<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('main.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
       // dd('store');
        $request->authenticate();
       // dd($request->user()->role);
        $request->session()->regenerate();
        //return to_route('adminPage');
        if($request->user()->role){
                if($request->user()->role == 'admin'){
                return to_route('adminPage');
            }

            if($request->user()->role == 'cashier'){
                return to_route('salePage');
            }
        }
        else{
           return redirect()->route('max_login');
        }
        
        //return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
