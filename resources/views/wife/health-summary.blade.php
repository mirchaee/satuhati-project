@php
    $latest = $assessments->first();
    $previous = $assessments->skip(1)->first();

    // Logika Perubahan Berat Badan
    $weightDiff = 0;
    if($latest && $previous) {
        $weightDiff = $latest->weight_kg - $previous->weight_kg;
    }

    // Logika Status Tekanan Darah (Sederhana)
    $bpStatus = 'Normal';
    if($latest && $latest->blood_pressure) {
        $systolic = (int) explode('/', $latest->blood_pressure)[0];
        if($systolic >= 140) $bpStatus = 'Tinggi';
        elseif($systolic <= 90) $bpStatus = 'Rendah';
    }

    // Data untuk Grafik (Ambil 7 data terakhir)
    $chartData = $assessments->take(7)->reverse();
    
    $chartWeights = $chartData->map(fn($a) => (float) $a->weight_kg)->values()->toArray();
    
    $chartLabels = $chartData->map(fn($a) => $a->created_at->format('d/m'))->values()->toArray();
@endphp

<x-layout>
    <div class="space-y-8">
    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-deepBlue uppercase tracking-tight">Ringkasan Medis</h1>
            <p class="text-xs text-mutedGray font-medium">Pantau tren kesehatan Bunda secara berkala</p>
        </div>
        <div class="flex gap-3">
            <a href="/assessment" class="px-6 py-3 bg-deepBlue text-white rounded-2xl font-bold text-sm shadow-lg shadow-blue-100 hover:scale-105 transition-all">
                <i class="fa-solid fa-plus mr-2"></i> Cek Baru
            </a>
        </div>
    </div>

    <!-- 3 KOTAK VITAL (BB, BP, JANIN) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Berat Badan -->
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 text-xl">
                    <i class="fa-solid fa-weight-scale"></i>
                </div>
                <h3 class="text-xs font-black text-mutedGray uppercase tracking-widest">Berat Badan</h3>
            </div>
            <p class="text-3xl font-black text-deepBlue">{{ $latest->weight_kg ?? '-' }} <span class="text-sm font-medium text-mutedGray">kg</span></p>
            <p class="mt-2 text-[11px] font-bold {{ $weightDiff >= 0 ? 'text-green-500' : 'text-red-500' }}">
                <i class="fa-solid {{ $weightDiff >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1"></i>
                {{ abs($weightDiff) }} kg dibanding bulan lalu
            </p>
        </div>

        <!-- Tekanan Darah -->
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-500 text-xl">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <h3 class="text-xs font-black text-mutedGray uppercase tracking-widest">Tekanan Darah</h3>
            </div>
            <p class="text-3xl font-black text-deepBlue">{{ $latest->blood_pressure ?? '-' }} <span class="text-sm font-medium text-mutedGray">mmHg</span></p>
            <p class="mt-2 text-[11px] font-bold {{ $bpStatus == 'Normal' ? 'text-green-500' : 'text-emergencyRed' }}">
                Status: {{ $bpStatus }}
            </p>
        </div>

        <!-- Detak Jantung Janin -->
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-pink-50 rounded-2xl flex items-center justify-center text-softPink text-xl">
                    <i class="fa-solid fa-baby"></i>
                </div>
                <h3 class="text-xs font-black text-mutedGray uppercase tracking-widest">Detak Janin</h3>
            </div>
            <p class="text-3xl font-black text-deepBlue">{{ $latest->fetal_heart_rate ?? '-' }} <span class="text-sm font-medium text-mutedGray">bpm</span></p>
            <p class="mt-2 text-[11px] font-bold {{ ($latest->fetal_heart_rate >= 120 && $latest->fetal_heart_rate <= 160) ? 'text-green-500' : 'text-emergencyRed' }}">
                {{ ($latest->fetal_heart_rate >= 120 && $latest->fetal_heart_rate <= 160) ? 'Detak Normal' : 'Waspada/Tidak Normal' }}
            </p>
        </div>
    </div>

    <!-- GRAFIK TREN & KONSULTASI -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- GRAFIK TREN BB -->
        <div class="lg:col-span-8 bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <h3 class="font-black text-deepBlue text-sm uppercase tracking-widest mb-4">Tren Berat Badan Bunda</h3>
            <p class="text-[10px] text-mutedGray mb-6 italic">*Grafik akan terbentuk otomatis setelah Bunda rutin mengisi jurnal.</p>
            <div class="h-64">
                <canvas id="weightChart"></canvas>
            </div>
        </div>

        <!-- CARD KONSULTASI AI -->
        <div class="lg:col-span-4 bg-gradient-to-br from-softPink to-rose-600 p-8 rounded-[2.5rem] text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <!-- Badge AI -->
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[9px] font-black uppercase tracking-widest mb-4 inline-block">AI Companion</span>
                
                <h3 class="text-2xl font-black mb-2">Tanya SatuHati AI</h3>
                <p class="text-xs text-white/80 leading-relaxed mb-8">Bunda merasa cemas atau bingung dengan gejala yang muncul? Asisten AI kami siap menemani 24/7.</p>
                
                <div class="space-y-3 mb-8">
                    <div class="flex items-center gap-3 p-3 bg-white/10 rounded-2xl border border-white/10">
                        <i class="fa-solid fa-comment-nodes text-sm"></i>
                        <p class="text-[10px] font-medium italic">"Apakah mual di pagi hari itu normal?"</p>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-white/10 rounded-2xl border border-white/10">
                        <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                        <p class="text-[10px] font-medium italic">"Tips agar janin tetap aktif..."</p>
                    </div>
                </div>

                <!-- Tombol mengarah ke halaman Chat (Tugas Anggota 5) -->
                <a href="/chat" class="block w-full py-4 bg-white text-softPink rounded-2xl text-center font-black text-sm hover:scale-105 transition-all shadow-lg shadow-rose-900/20 uppercase tracking-widest">
                    Ngobrol Sekarang
                </a>
            </div>
            
            <!-- Dekorasi Robot/Heart Icon -->
            <i class="fa-solid fa-robot absolute -right-4 -bottom-4 text-9xl opacity-10 -rotate-12"></i>
        </div>
    </div>

    <!-- TABEL RIWAYAT (Last 10 Records) -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-deepBlue">Jurnal Pemeriksaan Mandiri</h3>
            <span class="text-xs font-medium text-mutedGray italic">Menampilkan 10 pemeriksaan terakhir</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-[10px] font-black text-mutedGray uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-black text-mutedGray uppercase tracking-wider">Mood</th>
                        <th class="px-6 py-4 text-[10px] font-black text-mutedGray uppercase tracking-wider">Gejala Terdeteksi</th>
                        <th class="px-6 py-4 text-[10px] font-black text-mutedGray uppercase tracking-wider text-center">Skala Nyeri</th>
                        <th class="px-6 py-4 text-[10px] font-black text-mutedGray uppercase tracking-wider">Tingkat Risiko</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($assessments ?? [] as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-8 py-6">
                            @if($item->created_at)
                                <p class="text-sm font-black text-deepBlue">
                                    {{ $item->created_at->isoFormat('D MMMM YYYY') }}
                                </p>
                                <p class="text-[11px] text-mutedGray font-medium italic">
                                    Jam {{ $item->created_at->format('H:i') }} WIB
                                </p>
                            @else
                                <p class="text-sm text-mutedGray italic">Tanggal tidak tercatat</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xl">
                            {{ $item->mood_emoji ?? '💖' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($item->symptoms_list ?? ['Sehat'] as $symp)
                                    <span class="px-2 py-0.5 bg-slate-100 text-deepBlue text-[10px] font-bold rounded-md border border-slate-200">
                                        {{ $symp }}
                                    </span>
                                @empty
                                    <span class="text-green-500 text-[10px] font-bold">Prima</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-deepBlue">
                                {{ $item->pain_scale ?? 0 }}/10
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase border
                                {{ $item->risk_level == 'Bahaya' ? 'bg-red-50 text-red-600 border-red-100' : 
                                   ($item->risk_level == 'Waspada' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-green-50 text-green-600 border-green-100') }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->risk_level == 'Bahaya' ? 'bg-red-600' : ($item->risk_level == 'Waspada' ? 'bg-amber-600' : 'bg-green-600') }}"></span>
                                {{ $item->risk_level ?? 'Aman' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <!-- Placeholder jika data kosong -->
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-mutedGray italic text-sm">
                            <i class="fa-solid fa-folder-open text-3xl mb-3 block opacity-20"></i>
                            Belum ada riwayat pemeriksaan. Mulai cek kesehatan hari ini!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('weightChart');
        
        if (ctx) {
            // Ambil data dari PHP
            const labels = @json($chartLabels);
            const dataWeights = @json($chartWeights);

            console.log("Labels:", labels); // Untuk ngecek di Inspect browser
            console.log("Data:", dataWeights);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Berat Badan Bunda (kg)',
                        data: dataWeights,
                        borderColor: '#EC4899',
                        backgroundColor: 'rgba(236, 72, 153, 0.1)',
                        borderWidth: 4,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#EC4899',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        } else {
            console.error("Elemen weightChart tidak ditemukan!");
        }
    });
    </script>
</x-layout>