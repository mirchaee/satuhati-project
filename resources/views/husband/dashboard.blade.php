<x-layout>
<div class="p-8 space-y-6 bg-background min-h-[80vh] flex items-center justify-center font-sans">
    
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
        <div class="w-full space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-primary tracking-tight">Halo, Papa {{ $user->name }}! 👋</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Pendamping setia untuk Bunda <span class="text-primary font-semibold">{{ $wife->name }}</span></p>
                </div>
                <div class="bg-softBlue px-6 py-2.5 rounded-2xl border border-blue-100 flex items-center gap-3">
                    <span class="text-xs font-medium text-primary uppercase tracking-wider block">Usia Kehamilan:</span>
                    <span class="text-xl font-bold text-primary">{{ $pregnancyWeek }} Minggu</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-surface p-6 rounded-card border border-gray-100/80 space-y-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-bold text-primary">🎯 Misi Harian</h2>
                            <p class="text-xs text-gray-400">Selesaikan tugas harian untuk menjaga kenyamanan Bunda</p>
                        </div>
                        <a href="{{ route('missions.index') }}" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
                    </div>
                    <div class="w-full bg-input h-2 rounded-full overflow-hidden">
                        <div class="bg-primary h-full rounded-full transition-all duration-500" style="width: 40%"></div>
                    </div>
                    <div class="space-y-3 pt-2" id="mission-list">
                        @forelse($missions as $mission)
                            <div class="flex items-center justify-between p-4 bg-background rounded-2xl border border-gray-50/50 transition-all duration-300 {{ $mission->is_completed ? 'opacity-40' : '' }}" data-id="{{ $mission->id }}">
                                <div class="flex items-center gap-4">
                                    <button class="btn-checkbox w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all {{ $mission->is_completed ? 'border-green-500 bg-green-500 text-white' : 'border-gray-300 bg-white hover:border-primary' }}" {{ $mission->is_completed ? 'disabled' : '' }}>
                                        @if($mission->is_completed)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @endif
                                    </button>
                                    <div>
                                        <p class="text-sm font-semibold text-primary {{ $mission->is_completed ? 'line-through' : '' }}">{{ $mission->title }}</p>
                                        <p class="text-xs text-gray-400">+{{ $mission->points }} Poin</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-6">Belum ada misi aktif untuk hari ini.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-surface p-6 rounded-card border border-gray-100/80 space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-primary">🤰 Kondisi Ibu</h2>
                        <p class="text-xs text-gray-400">Data pemantauan kesehatan berkala yang diisi oleh Bunda</p>
                    </div>
                    @if($latestAssessment)
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-background rounded-2xl flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-lg">💓</div>
                                <div>
                                    <span class="text-[10px] text-gray-400 font-medium block uppercase tracking-wider">Tekanan Darah</span>
                                    <span class="text-sm font-bold text-primary">{{ $latestAssessment->blood_pressure ?? 'Normal' }}</span>
                                </div>
                            </div>
                            <div class="p-4 bg-background rounded-2xl flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-lg">❤️</div>
                                <div>
                                    <span class="text-[10px] text-gray-400 font-medium block uppercase tracking-wider">Detak Janin</span>
                                    <span class="text-sm font-bold text-primary">{{ $latestAssessment->fetal_heart_rate ? $latestAssessment->fetal_heart_rate . ' bpm' : '140 bpm' }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-6 bg-background rounded-2xl text-center text-xs text-gray-400">
                            Bunda belum memperbarui catatan kondisi kesehatan hari ini.
                        </div>
                    @endif
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
                button.innerHTML = '...';

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
                        card.querySelector('.text-sm').classList.add('line-through');
                        button.classList.add('bg-green-500', 'border-green-500', 'text-white');
                        button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>`;
                    } else {
                        alert('Gagal memperbarui status misi.');
                        button.disabled = false;
                        button.innerHTML = '';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    button.disabled = false;
                    button.innerHTML = '';
                }
            }
        });
    });
</script>
</x-layout>