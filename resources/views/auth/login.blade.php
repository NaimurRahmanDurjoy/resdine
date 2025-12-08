<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Resdine') }} - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Fade-in animation */
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Floating icons animation */
        @keyframes floatIcon {
            0% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0); }
        }

        /* Moving background animation */
        .moving-bg {
            background-size: cover;
            animation: bgMove 30s infinite alternate ease-in-out;
        }

        @keyframes bgMove {
            0% { background-position: center; }
            100% { background-position: top; }
        }
    </style>
</head>

<body class="min-h-screen moving-bg"
      style="background-image: url('https://images.unsplash.com/photo-1528605248644-14dd04022da1?auto=format&fit=crop&w=1600&q=80');">

    <!-- Dark overlay -->
    <div class="min-h-screen bg-black/60 flex items-center justify-center px-4">

        <!-- Floating food icons (optional & fun!) -->
        <div class="absolute left-10 top-20 text-4xl animate-[floatIcon_3s_infinite]">🍕</div>
        <div class="absolute right-10 bottom-20 text-4xl animate-[floatIcon_4s_infinite]">🍷</div>
        <div class="absolute left-1/2 top-10 text-4xl animate-[floatIcon_5s_infinite]">🍝</div>

        <!-- Login Card -->
        <div class="w-full max-w-md bg-white/20 backdrop-blur-xl border border-white/30 rounded-2xl shadow-2xl p-8 
                    animate-[fadeInUp_0.8s_ease-out] relative overflow-hidden">

            <!-- Glow highlight -->
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-300/10 to-red-300/10 pointer-events-none"></div>

            <div class="text-center mb-6 relative">
                <h2 class="text-4xl font-extrabold text-white drop-shadow-lg">
                    🍽 Resdine Admin
                </h2>
                <p class="text-gray-200 text-sm mt-2">Welcome back! Let's get you signed in.</p>
            </div>

            @if(session('error'))
                <div class="bg-red-500/40 text-red-200 border border-red-400 p-3 rounded mb-4 text-center">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-5 relative">
                @csrf

                <!-- Email Field -->
                <div class="group">
                    <label class="block text-gray-200 mb-1">Email</label>
                    <input type="email" name="email" required
                        class="w-full p-3 rounded-lg bg-white/80 focus:bg-white transition border 
                               border-gray-300 focus:ring-4 focus:ring-yellow-400 shadow focus:shadow-yellow-300/40 outline-none
                               group-hover:shadow-lg">
                </div>

                <!-- Password Field -->
                <div class="group">
                    <label class="block text-gray-200 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full p-3 rounded-lg bg-white/80 focus:bg-white transition border 
                               border-gray-300 focus:ring-4 focus:ring-yellow-400 shadow focus:shadow-yellow-300/40 outline-none
                               group-hover:shadow-lg">
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full py-3 rounded-lg bg-gradient-to-r from-yellow-500 to-red-500 
                           hover:from-yellow-600 hover:to-red-600 text-white font-semibold shadow-lg 
                           transition transform hover:scale-[1.03] active:scale-[0.98]">
                    Login
                </button>
            </form>

            <p class="text-center text-gray-200 text-xs mt-6">
                © {{ date('Y') }} Resdine Restaurant • Admin Portal
            </p>
        </div>
    </div>

</body>

</html>
