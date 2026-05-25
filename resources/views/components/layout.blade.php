<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SatuHati - Digital Companion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        deepBlue: '#1D3557',
                        mutedGray: '#6C757D',
                        softPink: '#EC4899',
                        emergencyRed: '#C82333',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
    <style> body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; overflow-y: scroll; } </style>
</head>
<body class="antialiased">
    <div class="flex min-h-screen">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border-r border-gray-100 flex flex-col fixed h-full transition-all">
            <div class="p-6 flex items-center gap-3">
                <div class="w-12 h-12 bg-[#1D3557] rounded-[1.25rem] flex items-center justify-center text-white shadow-sm shrink-0">
                    <i class="fa-solid fa-heart text-2xl"></i>
                </div>
                <span class="font-bold text-xl text-deepBlue tracking-tight">SatuHati</span>
            </div>

            <nav class="flex-1 px-4 mt-4 space-y-2">
                <!-- Menu Dashboard -->
                <a href="/dashboard" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ (request()->is('dashboard') || request()->is('*dashboard*')) ? 'bg-slate-50 text-deepBlue font-semibold border-r-4 border-deepBlue shadow-sm' : 'text-mutedGray hover:bg-slate-50 hover:text-deepBlue' }}">
                    <i class="fa-solid fa-grip-vertical w-5"></i> Beranda
                </a>

                <!-- Menu Health Summary -->
                <a href="/health-summary" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('health-summary') ? 'bg-slate-50 text-deepBlue font-semibold border-r-4 border-deepBlue shadow-sm' : 'text-mutedGray hover:bg-slate-50 hover:text-deepBlue' }}">
                    <i class="fa-solid fa-notes-medical w-5"></i> Ringkasan Kesehatan
                </a>

                <!-- Menu Chat -->
                <a href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->is('chat*') ? 'bg-slate-50 text-deepBlue font-semibold border-r-4 border-deepBlue shadow-sm' : 'text-mutedGray hover:bg-slate-50 hover:text-deepBlue' }}">
                    <i class="fa-solid fa-comment-dots w-5"></i> Obrolan AI
                </a>
            </nav>

            <div class="p-4 space-y-4">
                <button class="w-full py-3 bg-emergencyRed text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-red-100 hover:scale-[1.02] transition-all">
                    <i class="fa-solid fa-star-of-life animate-pulse text-sm"></i> DARURAT
                </button>
                <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                    
                    <a href="{{ auth()->user()->role === 'suami' ? route('husband.settings') : route('wife.settings') }}" class="flex items-center gap-3 px-4 py-2 text-mutedGray hover:text-deepBlue transition-all">
                        <i class="fa-solid fa-gear"></i> Pengaturan
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
                        @csrf
                    </form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                    class="flex items-center gap-3 px-4 py-2 text-mutedGray hover:text-deepBlue transition-all">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar Akun
                    </a>
                </div>
            </div>
        </aside>

        <!-- CONTENT -->
        <main class="flex-1 ml-64">
            <header class="h-20 bg-white border-b border-gray-50 px-8 flex items-center justify-between sticky top-0 z-10">
                <!-- SEARCH BAR (Sudah dirapikan) -->
                <div class="relative w-96">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                    <input type="text" 
                        placeholder="Cari hasil pemeriksaan atau tips..." 
                        class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-12 pr-4 focus:ring-2 focus:ring-deepBlue/10 transition-all text-sm font-medium">
                </div>
                
                <div class="flex items-center gap-8">
                    <!-- FITUR LONCENG NOTIFIKASI (Tugas Anggota 3) -->
                    @php
                        $alerts = auth()->user()->healthAssessments()->whereIn('risk_level', ['Bahaya', 'Waspada'])->latest()->take(5)->get();
                        $alertCount = $alerts->count();
                    @endphp

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative text-gray-400 hover:text-deepBlue transition-all">
                            <i class="fa-solid fa-bell text-xl"></i>
                            @if($alertCount > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-emergencyRed text-[9px] text-white font-black flex items-center justify-center rounded-full border-2 border-white">
                                    {{ $alertCount }}
                                </span>
                            @endif
                        </button>

                        <!-- DROPDOWN NOTIFIKASI -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-4 w-80 bg-white rounded-[2rem] shadow-2xl border border-gray-100 z-50 overflow-hidden">
                            <div class="p-5 border-b border-gray-50 flex justify-between items-center bg-slate-50/50">
                                <h3 class="font-black text-deepBlue text-xs uppercase tracking-widest">Notifikasi Kesehatan</h3>
                                <span class="text-[9px] bg-deepBlue text-white px-2 py-0.5 rounded-full uppercase">{{ $alertCount }} Alert</span>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse($alerts as $alert)
                                    <a href="/health-summary" class="flex gap-4 p-4 border-b border-gray-50 hover:bg-slate-50 transition-all">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $alert->risk_level == 'Bahaya' ? 'bg-red-50 text-red-500' : 'bg-amber-50 text-amber-500' }}">
                                            <i class="fa-solid {{ $alert->risk_level == 'Bahaya' ? 'fa-triangle-exclamation' : 'fa-circle-exclamation' }}"></i>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-bold text-deepBlue leading-tight uppercase">Risiko {{ $alert->risk_level }} Terdeteksi!</p>
                                            <p class="text-[10px] text-mutedGray mt-1">{{ Str::limit($alert->notes ?? 'Segera cek ringkasan kesehatan Bunda.', 60) }}</p>
                                            <p class="text-[9px] text-gray-300 mt-2 font-bold">{{ $alert->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-10 text-center">
                                        <p class="text-xs text-mutedGray italic">Tidak ada peringatan baru.</p>
                                    </div>
                                @endforelse
                            </div>
                            <a href="/health-summary" class="block p-4 text-center text-[10px] font-black text-softPink uppercase tracking-widest hover:bg-pink-50 transition-all">
                                Lihat Semua Aktivitas
                            </a>
                        </div>
                    </div>

                    <!-- PROFILE SECTION -->
                    <div class="flex items-center gap-3 border-l pl-6 border-gray-100 text-right">
                        <div>
                            @if(auth()->user()->role === 'istri')
                                <p class="text-sm font-bold text-deepBlue leading-none">Bunda {{ explode(' ', auth()->user()->name)[0] }}</p>
                                <p class="text-[11px] text-mutedGray font-medium mt-1">{{ auth()->user()->getCurrentPregnancyWeek() }} Weeks Pregnant</p>
                            @else
                                <p class="text-sm font-bold text-deepBlue leading-none">Papa {{ explode(' ', auth()->user()->name)[0] }}</p>
                                <p class="text-[11px] text-mutedGray font-medium mt-1">Pendamping Siaga</p>
                            @endif
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background={{ auth()->user()->role === 'istri' ? 'EC4899' : '1D3557' }}&color=fff" class="w-10 h-10 rounded-xl" alt="Profile">
                    </div>
                </div>
            </header>

            <div class="p-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>