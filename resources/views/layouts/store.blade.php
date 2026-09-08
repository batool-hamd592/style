<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'StyleHub')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-stone-50 text-gray-900">
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white/95">
        <nav class="mx-auto flex max-w-7xl items-center justify-between
                    px-4 py-4 sm:px-6 lg:px-8">
            <a
                href="{{ route('store.home') }}"
                class="text-2xl font-black tracking-tight"
            >
                STYLE<span class="text-rose-600">HUB</span>
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a
                    href="{{ route('store.home') }}"
                    class="{{ request()->routeIs('store.home')
                        ? 'font-semibold text-rose-600'
                        : 'text-gray-600 hover:text-gray-900' }}"
                >
                    Home
                </a>

                <a
                    href="{{ route('store.products.index') }}"
                    class="{{ request()->routeIs('store.products.*')
                        ? 'font-semibold text-rose-600'
                        : 'text-gray-600 hover:text-gray-900' }}"
                >
                    Shop
                </a>

                <a
                    href="{{ route('store.products.index', [
                        'audience' => 'women'
                    ]) }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Women
                </a>

                <a
                    href="{{ route('store.products.index', [
                        'audience' => 'men'
                    ]) }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Men
                </a>

                <a
                    href="{{ route('store.products.index', [
                        'audience' => 'kids'
                    ]) }}"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Kids
                </a>
            </div>

            <div class="flex items-center gap-3">
                @php
    $cartCount = collect(session('cart', []))->sum('quantity');
@endphp

<a
    href="{{ route('cart.index') }}"
    class="relative rounded-full p-2 text-gray-700 hover:bg-gray-100"
    aria-label="Shopping cart"
>
    <span class="text-xl">🛒</span>

    @if ($cartCount > 0)
        <span class="absolute -right-1 -top-1 flex h-5 min-w-5
                     items-center justify-center rounded-full bg-rose-600
                     px-1 text-xs font-bold text-white">
            {{ $cartCount }}
        </span>
    @endif
</a>
@php
    $wishlistCount = count(session('wishlist', []));
@endphp

<a
    href="{{ route('wishlist.index') }}"
    class="relative rounded-full p-2 text-gray-700 hover:bg-gray-100"
    aria-label="Wishlist"
>
    <span class="text-xl">♡</span>

    @if ($wishlistCount > 0)
        <span class="absolute -right-1 -top-1 flex h-5 min-w-5
                     items-center justify-center rounded-full bg-rose-600
                     px-1 text-xs font-bold text-white">
            {{ $wishlistCount }}
        </span>
    @endif
</a>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="text-sm font-semibold text-gray-700"
                        >
                            Admin
                        </a>
                    @else
                        <a
                            href="{{ route('orders.index') }}"
                            class="text-sm font-semibold text-gray-700"
                        >
                         My Orders
                        </a>
                    @endif
                @else
                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-semibold text-gray-700"
                    >
                        Login
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="rounded-full bg-gray-900 px-4 py-2
                               text-sm font-semibold text-white"
                    >
                        Register
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mt-20 bg-gray-950 text-gray-300">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12
                    sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <p class="text-2xl font-black text-white">
                    STYLE<span class="text-rose-500">HUB</span>
                </p>

                <p class="mt-3 text-sm text-gray-400">
                    Modern fashion for women, men and kids.
                </p>
            </div>

            <div>
                <h3 class="font-semibold text-white">Shop</h3>

                <div class="mt-3 space-y-2 text-sm">
                    <a class="block hover:text-white"
                       href="{{ route('store.products.index') }}">
                        All Products
                    </a>

                    <a class="block hover:text-white"
                       href="{{ route('store.products.index', [
                           'audience' => 'women'
                       ]) }}">
                        Women
                    </a>

                    <a class="block hover:text-white"
                       href="{{ route('store.products.index', [
                           'audience' => 'men'
                       ]) }}">
                        Men
                    </a>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-white">Payment</h3>

                <p class="mt-3 text-sm text-gray-400">
                    Secure cash on delivery across Jordan.
                </p>
            </div>
        </div>

        <div class="border-t border-gray-800 py-5 text-center
                    text-sm text-gray-500">
            © {{ date('Y') }} StyleHub. All rights reserved.
        </div>
    </footer>
</body>
</html>