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
        <div class="w-16 h-16 bg-softBlue rounded-2xl mx-auto flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-primary">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>
        
        <h1 class="text-4xl font-bold text-center text-primary mt-6">Register</h1>
        <p class="text-center text-gray-400 mt-2">Buat akun SatuHati</p>

        @if ($errors->any())
            <div class="mb-4 mt-6 bg-red-100 text-red-600 p-4 rounded-xl text-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/register" method="POST" class="mt-8 space-y-5">
            @csrf
            <input type="text" name="name" required placeholder="Nama Lengkap" class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary">
            <input type="email" name="email" required placeholder="Email" class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary">
            <input type="password" name="password" required placeholder="Password" class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary">
            <input type="password" name="password_confirmation" required placeholder="Konfirmasi Password" class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary">
            <input type="text" name="phone" placeholder="Nomor Telepon (Opsional)" class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary">
            
            <select id="role" name="role" required class="w-full h-14 rounded-2xl bg-input px-6 outline-none focus:ring-2 focus:ring-primary">
                <option value="">Pilih Role</option>
                <option value="istri">Istri</option>
                <option value="suami">Suami</option>
            </select>

            <div id="wife-fields" class="hidden space-y-5 bg-slate-50 p-4 rounded-2xl border border-dashed border-gray-200">
                <div>
                    <label class="text-xs text-slate-500 font-medium block mb-1">Usia Kehamilan Saat Ini (Minggu)</label>
                    <input type="number" id="pregnancy_week" name="pregnancy_week" min="1" max="42" placeholder="Contoh: 12" class="w-full h-12 rounded-xl bg-white border border-slate-200 px-4 outline-none focus:ring-2 focus:ring-primary text-sm">
                </div>
                <div>
                    <label class="text-xs text-slate-500 font-medium block mb-1">Tanggal Hari Pertama Haid Terakhir (HPHT)</label>
                    <input type="date" id="hpht" name="hpht" class="w-full h-12 rounded-xl bg-white border border-slate-200 px-4 outline-none focus:ring-2 focus:ring-primary text-sm">
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full h-14 rounded-2xl bg-primary text-white font-medium hover:opacity-90 transition-all duration-300">
                    DAFTAR
                </button>
            </div>
        </form>

        <p class="text-center text-gray-400 text-sm mt-8">
            Sudah punya akun? <a href="/login" class="text-primary font-medium hover:underline">Login</a>
        </p>
    </div>

    <script>
        const roleSelect = document.getElementById('role');
        const wifeFields = document.getElementById('wife-fields');
        const pregInput = document.getElementById('pregnancy_week');
        const hphtInput = document.getElementById('hpht');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'istri') {
                wifeFields.classList.remove('hidden');
                pregInput.required = true;
                hphtInput.required = true;
            } else {
                wifeFields.classList.add('hidden');
                pregInput.required = false;
                hphtInput.required = false;
                pregInput.value = '';
                hphtInput.value = '';
            }
        });
    </script>
</body>
</html>