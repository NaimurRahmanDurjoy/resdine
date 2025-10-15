<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Developer Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-center">DevAdmin Login</h2>
        @if(session('error'))
            <div class="mb-4 text-red-600">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('devAdmin.login.submit') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-gray-700 mb-2">Email/UserName</label>
                <input type="email" name="email" id="email" required autofocus
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
                       value="{{ old('email') }}">
                @error('email')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring">
                @error('password')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
                Login
            </button>
        </form>
    </div>
</body>
</html>