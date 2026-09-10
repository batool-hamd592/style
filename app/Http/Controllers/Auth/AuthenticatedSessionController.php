<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(
        LoginRequest $request,
        CartService $cartService,
        WishlistService $wishlistService
    ): RedirectResponse {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        // نقل سلة ومفضلة الزائر إلى حساب المستخدم
        $cartService->mergeGuestCart($user);
        $wishlistService->mergeGuestWishlist($user);

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }

    public function destroy(
        Request $request
    ): RedirectResponse {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.home');
    }
}