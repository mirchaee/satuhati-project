@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6 bg-background min-h-screen font-sans">
    
    @if(!$wife)
        <div class="bg-surface rounded-card shadow-sm p-8 text-center max-w-xl mx-auto border border-softBlue mt-12">
            <div class="w-16 h-16 bg-softBlue rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">🔒</div>
            <h2 class="text-2xl font-bold text-primary mb-2">Akun Belum Terhubung</h2>
            <p class="text-gray-500 mb-6 text-sm">Silakan masukkan Kode Unik dari aplikasi SatuHati milik Istri Anda untuk mensinkronisasikan data perkembangan janin.</p>
            <form action="{{ route('sync.pair') }}" method="POST" class="flex gap-3 max-w-md mx-auto">
                @csrf
                <input type="text" name="pairing_code" required placeholder="Contoh: SH-XXXX" class="flex-1 h-12 bg-input rounded-xl px-4 outline-none focus:ring-2 focus:ring-primary uppercase text-center font-semibold tracking-wider">
                <button type="submit" class="bg-primary text-white px-6 rounded-xl font-medium hover:opacity-90 transition">Hubungkan</button>
            </form>
        </div>
    @else
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
                                <span class="text-[10px] text-gray-400 font-medium block uppercase tracking-wider">Detak Janun Janin</span>
                                <span class="text-sm font-bold text-primary">{{ $latestAssessment->fetal_heart_rate ? $latestAssessment->fetal_heart_rate . ' bpm' : '140 bpm' }}</span>
                            </div>
                        </div>

                        <div class="p-4 bg-background rounded-2xl flex items-center gap-3">
                            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center text-lg">⚡</div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-medium block uppercase tracking-wider">Skala Nyeri</span>
                                <span class="text-sm font-bold text-primary">Skala {{ $latestAssessment->pain_scale ?? '0' }}/10</span>
                            </div>
                        </div>

                        <div class="p-4 bg-background rounded-2xl flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-lg">⚖️</div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-medium block uppercase tracking-wider">Berat Badan Ibu</span>
                                <span class="text-sm font-bold text-primary">{{ $latestAssessment->weight_kg ?? '-' }} kg</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Keluhan Gejala:</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse($latestAssessment->symptoms as $symptom)
                                <span class="bg-red-50 text-red-600 border border-red-100/50 px-3 py-1 rounded-full text-xs font-medium">
                                    ⚠️ {{ $symptom->name }}
                                </span>
                            @empty
                                <span class="bg-green-50 text-green-600 border border-green-100/50 px-3 py-1 rounded-full text-xs font-medium">
                                    😊 Kondisi prima tanpa keluhan
                                </span>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div class="p-6 bg-background rounded-2xl text-center text-xs text-gray-400">
                        Bunda belum memperbarui catatan kondisi kesehatan hari ini.
                    </div>
                @endif
                
                @if($guidance)
                    <div class="p-4 bg-softBlue rounded-2xl border border-blue-100/60 space-y-1">
                        <span class="text-[10px] font-bold text-primary uppercase tracking-wider block">💡 Edukasi Minggu Ini</span>
                        <h4 class="text-xs font-bold text-primary">{{ $guidance->title }}</h4>
                        <p class="text-[11px] text-gray-600 leading-relaxed">{{ Str::limit($guidance->content, 120) }}</p>
                    </div>
                @endif

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
@endsection