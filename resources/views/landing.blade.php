<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')

    <title>SatuHati</title>
</head>

<body class="bg-background min-h-screen font-sans">

    <!-- Navbar -->
    <nav class="flex justify-between items-center px-10 py-6">

        <!-- Logo -->
        <div class="flex items-center gap-4">

            <div class="w-14 h-14 bg-softBlue rounded-2xl flex items-center justify-center">

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

            <h1 class="text-3xl font-bold text-primary">
                SatuHati
            </h1>

        </div>

        <!-- Button -->
        <div class="flex gap-4">

            <a href="/login"
               class="px-6 py-3 rounded-2xl text-primary font-medium hover:bg-white transition-all duration-300">
                Login
            </a>

            <a href="/register"
               class="bg-primary text-white px-6 py-3 rounded-2xl hover:opacity-90 transition-all duration-300">
                Register
            </a>

        </div>

    </nav>

    <!-- Hero -->
    <section class="flex flex-col items-center justify-center text-center mt-24 px-6">

        <!-- Icon -->
        <div class="w-28 h-28 bg-softBlue rounded-card flex items-center justify-center">

            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="currentColor"
                 class="w-12 h-12 text-primary">

                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                         2 5.42 4.42 3 7.5 3
                         c1.74 0 3.41.81 4.5 2.09
                         C13.09 3.81 14.76 3 16.5 3
                         19.58 3 22 5.42 22 8.5
                         c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>

        </div>

        <!-- Title -->
        <h2 class="text-6xl font-bold text-primary mt-10 leading-tight">
            Pendamping Digital <br>
            Untuk Keluarga
        </h2>

        <!-- Description -->
        <p class="text-gray-400 text-xl mt-6 max-w-3xl leading-relaxed">
            SatuHati membantu pasangan memantau kesehatan ibu hamil,
            memberikan edukasi, menjaga komunikasi keluarga,
            dan mendukung kondisi darurat secara real-time.
        </p>

        <!-- CTA -->
        <div class="flex gap-5 mt-12">

            <a href="/login"
               class="bg-primary text-white px-10 py-4 rounded-2xl
                      hover:scale-105 hover:opacity-90
                      transition-all duration-300">

                Mulai Sekarang

            </a>

        </div>

    </section>

</body>
</html>