@extends('layouts.store')

@section('title', 'Checkout | StyleHub')

@section('content')
    <section class="bg-gray-950 py-14 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.25em]
                      text-rose-400">
                Final step
            </p>

            <h1 class="mt-3 text-4xl font-black">
                Confirm Your Order
            </h1>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <form
            action="{{ route('checkout.store') }}"
            method="POST"
            class="grid gap-8 lg:grid-cols-3"
        >
            @csrf

            <div class="rounded-2xl bg-white p-7 shadow-sm lg:col-span-2">
                <h2 class="text-2xl font-bold">
                    Delivery Information
                </h2>

                <div class="mt-7 grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">
                            Full Name
                        </label>

                        <input
                            name="customer_name"
                            value="{{ old(
                                'customer_name',
                                auth()->user()->name
                            ) }}"
                            class="mt-2 block w-full rounded-xl border-gray-300"
                        >

                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">
                            Mobile Number
                        </label>

                        <input
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="0791234567"
                            class="mt-2 block w-full rounded-xl border-gray-300"
                        >

                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">
                            City
                        </label>

                        <select
                            name="city"
                            class="mt-2 block w-full rounded-xl border-gray-300"
                        >
                            <option value="">Choose your city</option>

                            @foreach ([
                                'Amman',
                                'Zarqa',
                                'Irbid',
                                'Aqaba',
                                'Salt',
                                'Madaba',
                                'Jerash',
                                'Ajloun',
                                'Mafraq',
                                'Karak',
                                'Tafilah',
                                'Maan',
                            ] as $city)
                                <option
                                    value="{{ $city }}"
                                    @selected(old('city') === $city)
                                >
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>

                        @error('city')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Detailed Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                            placeholder="Area, street, building number..."
                            class="mt-2 block w-full rounded-xl border-gray-300"
                        >{{ old('address') }}</textarea>

                        @error('address')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            Order Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="3"
                            placeholder="Optional delivery instructions"
                            class="mt-2 block w-full rounded-xl border-gray-300"
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <aside class="h-fit rounded-2xl bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold">Order Summary</h2>

                <div class="mt-6 max-h-80 space-y-4 overflow-y-auto">
                    @foreach ($items as $item)
                        <div class="flex justify-between gap-4 border-b
                                    border-gray-100 pb-4">
                            <div>
                                <p class="font-semibold">
                                    {{ $item['product']->name }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $item['variant']->size }}
                                    · {{ $item['variant']->color }}
                                    · Qty: {{ $item['quantity'] }}
                                </p>
                            </div>

                            <p class="whitespace-nowrap font-semibold">
                                {{ number_format(
                                    $item['subtotal'],
                                    2
                                ) }} JOD
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ number_format($subtotal, 2) }} JOD</span>
                    </div>

                    <div class="flex justify-between text-gray-600">
                        <span>Delivery</span>
                        <span>{{ number_format($deliveryFee, 2) }} JOD</span>
                    </div>

                    <div class="flex justify-between border-t border-gray-200
                                pt-4 text-xl font-bold">
                        <span>Total</span>
                        <span>{{ number_format($total, 2) }} JOD</span>
                    </div>
                </div>

                <div class="mt-5 rounded-xl bg-amber-50 p-4 text-sm
                            text-amber-800">
                    Payment method: Cash on delivery
                </div>

                <button
                    type="submit"
                    class="mt-6 w-full rounded-full bg-gray-950
                           px-6 py-3 font-semibold text-white
                           hover:bg-rose-600"
                >
                    Confirm Order
                </button>
            </aside>
        </form>
    </section>
@endsection