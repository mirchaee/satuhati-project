<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Istri</title>
</head>
<body>

    <h1>Dashboard Istri</h1>

    <p>Halo, {{ $user->name }}</p>

    <a href="/logout"
        onclick="return confirm('Yakin ingin logout?')"
        class="text-sm text-gray-400 hover:text-red-500 transition">

            Keluar

    </a>
    
</body>
</html>