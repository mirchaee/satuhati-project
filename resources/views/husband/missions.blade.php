@extends('layouts.app')

@section('content')
<div class="p-8 max-w-4xl mx-auto space-y-6 bg-background min-h-screen font-sans">
    <div class="flex items-center justify-between bg-surface p-6 rounded-card border border-gray-100/80">
        <div>
            <h1 class="text-2xl font-bold text-primary tracking-tight">🎯 Daftar Misi Harian Papa</h1>
            <p class="text-xs text-gray-400 mt-1">Selesaikan misi kecil setiap hari untuk memberikan dukungan terbaik bagi Bunda.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
            ⬅️ Kembali ke Dasbor
        </a>
    </div>

    <div class="bg-surface p-6 rounded-card border border-gray-100/80 space-y-4" id="full-mission-box">
        @forelse($missions as $mission)
            <div class="flex items-center justify-between p-5 bg-background rounded-2xl border border-gray-50/50 transition-all duration-300 {{ $mission->is_completed ? 'opacity-40' : '' }}" data-id="{{ $mission->id }}">
                <div class="flex items-center gap-5">
                    <button class="btn-checkbox w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all shrink-0 {{ $mission->is_completed ? 'border-green-500 bg-green-500 text-white' : 'border-gray-300 bg-white hover:border-primary' }}" {{ $mission->is_completed ? 'disabled' : '' }}>
                        @if($mission->is_completed)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @endif
                    </button>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-primary tracking-wide mission-title {{ $mission->is_completed ? 'line-through' : '' }}">{{ $mission->title }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $mission->description ?? 'Lakukan aktivitas ini demi kenyamanan psikologis dan fisik Bunda hari ini.' }}</p>
                        <span class="inline-block mt-2 bg-softBlue text-primary text-[10px] font-bold px-2 py-0.5 rounded-md">
                            🎁 +{{ $mission->points }} Poin Suami Siaga
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-400 space-y-2">
                <span class="text-4xl block">🎉</span>
                <p class="text-xs">Hari ini tidak ada daftar misi aktif yang tersedia atau semua sudah tuntas dipenuhi!</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const missionContainer = document.getElementById('full-mission-box');
        if (!missionContainer) return;

        missionContainer.addEventListener('click', async function (e) {
            const button = e.target.closest('.btn-checkbox');
            if (button && !button.disabled) {
                const card = button.closest('[data-id]');
                const id = card.getAttribute('data-id');

                button.disabled = true;
                button.innerHTML = '...';

                try {
                    const res = await fetch(`/missions/${id}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();

                    if (data.success) {
                        card.classList.add('opacity-40');
                        card.querySelector('.mission-title').classList.add('line-through');
                        button.classList.add('bg-green-500', 'border-green-500', 'text-white');
                        button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>`;
                    } else {
                        alert('Terjadi kendala dalam merubah status.');
                        button.disabled = false;
                        button.innerHTML = '';
                    }
                } catch (err) {
                    console.error('Error:', err);
                    button.disabled = false;
                    button.innerHTML = '';
                }
            }
        });
    });
</script>
@endsection