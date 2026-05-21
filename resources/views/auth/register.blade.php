<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')

    <title>Register - SatuHati</title>
</head>

<body class="bg-background min-h-screen flex items-center justify-center font-sans">

    <div class="bg-surface w-[420px] rounded-card shadow-xl px-12 py-10">

        <!-- Logo -->
        <div class="w-16 h-16 bg-softBlue rounded-2xl mx-auto flex items-center justify-center">

            <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="w-7 h-7 text-primary">

                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                    2 5.42 4.42 3 7.5 3
                    c1.74 0 3.41.81 4.5 2.09
                    C13.09 3.81 14.76 3 16.5 3
                    19.58 3 22 5.42 22 8.5
                    c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>

        </div>

        <!-- Title -->
        <h1 class="text-4xl font-bold text-center text-primary mt-6">
            Register
        </h1>

        <p class="text-center text-gray-400 mt-2">
            Buat akun SatuHati
        </p>

        <!-- Error -->
        @if ($errors->any())
            <div class="mb-4 mt-6 bg-red-100 text-red-600 p-4 rounded-xl text-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form action="/register" method="POST" class="mt-8 space-y-5">

            @csrf

            <input
                type="text"
                name="name"
                required
                placeholder="Nama Lengkap"
                class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary"
            >

            <input
                type="email"
                name="email"
                required
                placeholder="Email"
                class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary"
            >

            <input
                type="password"
                name="password"
                required
                placeholder="Password"
                class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary"
            >

            <input
                type="password"
                name="password_confirmation"
                required
                placeholder="Konfirmasi Password"
                class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary"
            >

            <input
                type="text"
                name="phone"
                required
                placeholder="Nomor Telepon"
                class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary"
            >

            <select
                id="role"
                name="role"
                required
                class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary">

                <option value="">Pilih Role</option>
                <option value="istri">Istri</option>
                <option value="suami">Suami</option>

            </select>

            <!-- Button -->
            <div class="mt-8">

                <button
                    type="submit"
                    class="w-full h-14 rounded-2xl bg-primary text-white font-medium hover:opacity-90 transition-all duration-300">

                    DAFTAR

                </button>

            </div>

        </form>

        <!-- Login Link -->
        <p class="text-center text-gray-400 text-sm mt-8">

            Sudah punya akun?

            <a href="/login"
               class="text-primary font-medium hover:underline">

                Login

            </a>

        </p>

    </div>

</body>
</html>
