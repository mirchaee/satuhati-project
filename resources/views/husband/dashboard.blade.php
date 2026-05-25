<x-layout>
<div class="p-8 space-y-6 bg-[#F8FAFC] min-h-[80vh] flex items-center justify-center font-sans">
    
    @if(!$wife)
        <div class="bg-white rounded-[32px] p-12 text-center w-full max-w-xl border border-gray-100 shadow-sm flex flex-col items-center justify-center animate-fadeIn">
            
            <div class="w-16 h-16 bg-[#DCEAFE] text-[#1D3557] rounded-full flex items-center justify-center mb-6 text-2xl shadow-sm shrink-0">
                🔒
            </div>
            
            <h2 class="text-2xl font-black text-[#1D3557] mb-3 uppercase tracking-tight">Akun Belum Terhubung</h2>
            <p class="text-gray-400 mb-6 text-sm max-w-sm leading-relaxed font-medium">
                Silakan masukkan Kode Unik dari aplikasi SatuHati milik Istri Anda untuk mensinkronisasikan data perkembangan janin.
            </p>

            @error('pairing_code')
                <div class="w-full max-w-md mb-6 p-4 bg-red-50 text-red-600 border border-red-100 rounded-2xl text-xs font-bold text-center animate-fadeIn">
                    ❌ {{ $message }}
                </div>
            @enderror
            
            <form action="{{ route('sync.pair') }}" method="POST" class="w-full max-w-md flex items-center gap-3">
                @csrf
                <div class="relative flex-1">
                    <input type="text" name="pairing_code" required placeholder="CONTOH: SH-XXXX" 
                           value="{{ old('pairing_code') }}"
                           class="w-full h-14 bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 outline-none focus:border-[#1D3557] focus:bg-white uppercase text-center font-black tracking-widest text-[#1D3557] text-sm transition-all placeholder:text-gray-300 placeholder:font-normal shadow-inner">
                </div>
                
                <button type="submit" class="h-14 bg-[#1D3557] text-white px-8 rounded-2xl font-bold hover:bg-[#294A6D] hover:shadow-lg hover:shadow-blue-900/20 active:scale-95 transition-all duration-300 tracking-wide text-sm whitespace-nowrap shrink-0 flex items-center justify-center">
                    Hubungkan
                </button>
            </form>
        </div>
    @else
        <div class="w-full space-y-6 text-[#1D3557] animate-fadeIn self-start">
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-[#1D3557]">Halo, Ayah! 👋</h1>
                    <p class="text-gray-400 text-sm font-medium mt-1">
                        Dukung perjalanan Bunda <span class="text-[#1D3557] font-bold">{{ $wife->name }}</span> dengan penuh kasih hari ini.
                    </p>
                </div>
                
                <div class="bg-white px-6 py-4 rounded-[24px] border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-10 h-10 bg-[#DCEAFE] rounded-xl flex items-center justify-center text-lg shadow-inner">
                        📅
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Usia Kehamilan</span>
                        <span class="text-lg font-black text-[#1D3557]">{{ $pregnancyWeek }} Minggu</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-black uppercase tracking-wider text-[#1D3557]">Status Bunda</h3>
                            <span class="bg-red-500 text-white text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest animate-pulse flex items-center gap-1">
                                <span class="w-1 h-1 bg-white rounded-full"></span> Live
                            </span>
                        </div>

                        <div class="p-5 bg-slate-50 rounded-2xl flex items-center gap-4 border border-slate-100">
                            <div class="w-12 h-12 bg-pink-50 text-pink-500 rounded-full flex items-center justify-center text-xl shrink-0 shadow-sm">
                                🤰
                            </div>
                            <div class="flex-1">
                                <span class="text-[10px] text-gray-400 font-bold uppercase block">Kondisi Saat Ini:</span>
                                <h4 class="text-base font-black text-[#1D3557]">
                                    {{ $latestAssessment->notes ?? 'Mual Ringan (Morning Sickness)' }}
                                </h4>
                            </div>
                            <button class="h-10 bg-[#1D3557] hover:bg-[#294A6D] text-white text-xs font-bold px-4 rounded-xl transition-all shadow-sm flex items-center gap-1.5 active:scale-95">
                                <i class="fa-solid fa-heart"></i>
                                <span>Tanya Kabarnya</span>
                            </button>
                        </div>

                        <div class="p-5 bg-[#DCEAFE]/30 rounded-2xl border-l-4 border-[#1D3557] space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-[#1D3557] flex items-center gap-1.5">
                                <i class="fa-solid fa-lightbulb"></i> Saran Dukungan
                            </span>
                            <p class="text-xs text-gray-600 italic leading-relaxed font-medium">
                                "Istri sedang mual, coba buatkan teh jahe hangat atau pijat lembut telapak tangannya untuk membantu relaksasi."
                            </p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-50 pb-3">
                            <div>
                                <h3 class="text-sm font-black uppercase tracking-wider text-[#1D3557]">Misi Harian Ayah</h3>
                                <p class="text-[11px] text-gray-400 mt-0.5 font-medium">Selesaikan tugas harian untuk menjaga kenyamanan Bunda</p>
                            </div>
                            
                            <span id="mission-counter-text" class="text-xs font-black bg-[#DCEAFE] text-[#1D3557] px-4 py-1.5 rounded-full shadow-sm tracking-wide">
                                {{ $completedCount ?? 0 }}/{{ $totalCount ?? 0 }} Selesai
                            </span>
                        </div>

                        <div class="space-y-3 pr-1 max-h-[260px] overflow-y-auto scrollbar-thin" id="mission-list">
                            @forelse($missions as $mission)
                                <div class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/40 rounded-2xl border border-slate-100/60 transition-all duration-300 {{ $mission->is_completed ? 'opacity-40' : '' }}" data-id="{{ $mission->id }}">
                                    <div class="flex items-center gap-4 w-full">
                                        <button class="btn-checkbox w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all shrink-0 {{ $mission->is_completed ? 'bg-[#1D3557] border-[#1D3557] text-white' : 'border-gray-300 bg-white hover:border-[#1D3557]' }}" {{ $mission->is_completed ? 'disabled' : '' }}>
                                            @if($mission->is_completed)
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            @endif
                                        </button>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-[#1D3557] {{ $mission->is_completed ? 'line-through text-gray-400' : '' }}">{{ $mission->title }}</p>
                                            <p class="text-[9px] text-gray-400 font-bold mt-0.5 uppercase tracking-wider">+{{ $mission->points }} Poin</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 text-center py-12 italic">Belum ada misi aktif untuk hari ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <div class="relative rounded-[32px] overflow-hidden bg-gradient-to-br from-[#1D3557] to-[#294A6D] p-6 text-white h-[220px] flex flex-col justify-end shadow-md group">
                        <div class="absolute inset-0 opacity-15 mix-blend-overlay bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1551836022-d5d88e9218df?q=80&w=500');"></div>
                        
                        <div class="relative z-10 space-y-2">
                            <span class="bg-white/20 text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md backdrop-blur-md">Panduan Baru</span>
                            <h3 class="text-base font-black leading-snug">Panduan Ayah Siaga menghadapi Morning Sickness</h3>
                            <a href="#" class="text-[11px] font-bold text-blue-200 flex items-center gap-1 group-hover:text-white transition-all pt-1">
                                <span>Baca Selengkapnya</span>
                                <i class="fa-solid fa-arrow-right text-[9px] transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 text-[#C82333]">
                            <i class="fa-solid fa-triangle-exclamation text-base animate-bounce"></i>
                            <h3 class="text-sm font-black uppercase tracking-wider">Darurat</h3>
                        </div>
                        <p class="text-xs text-gray-400 font-medium leading-relaxed">
                            Gunakan tombol jika Bunda mengalami pendarahan atau kondisi darurat medis lainnya.
                        </p>
                        
                        <div class="space-y-2.5 pt-1">
                            <button class="w-full h-12 bg-[#C82333] hover:bg-red-700 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-md shadow-red-100 transition-all active:scale-[0.98]">
                                <i class="fa-solid fa-phone"></i> Hubungi Dokter
                            </button>
                            <button class="w-full h-12 bg-white hover:bg-slate-50 text-[#C82333] border-2 border-[#C82333] rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                                <i class="fa-solid fa-map-location-dot"></i> Cari Faskes
                            </button>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-slate-50 to-gray-50/50 p-5 rounded-2xl border border-gray-100 text-center">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Kata Hari Ini</span>
                        <p class="text-xs text-[#1D3557] font-medium italic leading-relaxed">
                            "Menjadi ayah dimulai dari momen kecil penuh perhatian yang kamu berikan pada Bunda setiap harinya."
                        </p>
                    </div>

                </div>

            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const missionList = document.getElementById('mission-list');
        if (!missionList) return;

        missionList.addEventListener('click', async function (e) {
            const button = e.target.closest('.btn-checkbox');
            if (button && !button.disabled) {
                const card = button.closest('[data-id]');
                const id = card.getAttribute('data-id');

                button.disabled = true;
                button.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin text-[10px]"></i>';

                try {
                    const response = await fetch(`/missions/${id}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const result = await response.json();

                    if (result.success) {
                        card.classList.add('opacity-40');
                        const textElement = card.querySelector('p.text-xs');
                        if (textElement) textElement.classList.add('line-through', 'text-gray-400');
                        
                        button.classList.remove('border-gray-300', 'bg-white', 'hover:border-[#1D3557]');
                        button.classList.add('bg-[#1D3557]', 'border-[#1D3557]', 'text-white');
                        button.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';

                        const allMissions = document.querySelectorAll('#mission-list [data-id]');
                        const totalCount = allMissions.length;
                        
                        let completedCount = 0;
                        allMissions.forEach(m => {
                            if (m.querySelector('.btn-checkbox').classList.contains('bg-[#1D3557]')) {
                                completedCount++;
                            }
                        });

                        const counterBadge = document.getElementById('mission-counter-text');
                        if (counterBadge) {
                            counterBadge.textContent = `${completedCount}/${totalCount} Selesai`;
                        }

                    } else {
                        alert('Gagal memperbarui status misi: ' + (result.message || 'Error internal.'));
                        button.disabled = false;
                        button.innerHTML = '';
                    }
                } catch (error) {
                    console.error('Error AJAX:', error);
                    alert('Koneksi terputus atau server bermasalah.');
                    button.disabled = false;
                    button.innerHTML = '';
                }
            }
        });
    });
</script>
</x-layout>