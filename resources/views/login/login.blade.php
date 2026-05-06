{{-- resources/views/login/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Tracking System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen w-screen flex items-center justify-center bg-gray-100"
      style="background-image:url('{{ asset('img/bg.png') }}'); background-size:cover; background-position:center;">

    <div class="w-full max-w-xl px-4">
        {{-- Logo --}}
     <div class="flex flex-col items-center mb-6">
        <img src="{{ asset('img/ICS2-01.png') }}" alt="ICS - Impeccable Core System" class="h-24 w-auto">
    </div>

        {{-- Card --}}
        <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl border border-gray-200 p-8">
            <div class="text-center mb-6">
                <p class="text-sm text-gray-600">Welcome to</p>
                <h2 class="text-lg font-bold text-gray-900">Impeccable Core System</h2>
                <p class="mt-4 text-base font-semibold text-gray-800">LOGIN</p>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-2 rounded mb-4 text-center text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-2 rounded mb-4 text-center text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Email"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full bg-gray-200/80 border border-gray-300 rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                        autocomplete="current-password"
                        class="w-full bg-gray-200/80 border border-gray-300 rounded-md px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-gray-300"
                               {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Remember me</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-32 mx-auto block bg-gray-600 text-white py-2 rounded-md font-semibold shadow hover:bg-gray-700 transition"
                >
                    Login
                </button>
            </form>
        </div>
    </div>

</body>
</html>