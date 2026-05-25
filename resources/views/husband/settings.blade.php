<x-layout>
<div class="p-8 max-w-3xl mx-auto space-y-6 font-sans bg-[#F5F7FB] min-h-screen">
    
    <div>
        <h1 class="text-2xl font-black text-primary uppercase tracking-tight">⚙️ Pengaturan Profil</h1>
        <p class="text-xs text-gray-400 mt-1">Kelola data diri dan sinkronisasi akun pasangan SatuHati</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 text-green-600 border border-green-100 rounded-2xl text-xs font-bold animate-fadeIn">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm space-y-6">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider border-b pb-2 border-gray-50">Informasi Papa</h3>
            
            <form action="{{ route('husband.settings.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ $user->name }}" required 
                           class="w-full h-12 bg-gray-50 rounded-xl px-4 outline-none text-xs font-semibold text-primary border border-transparent focus:border-primary focus:bg-white transition">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" required 
                           class="w-full h-12 bg-gray-50 rounded-xl px-4 outline-none text-xs font-semibold text-primary border border-transparent focus:border-primary focus:bg-white transition">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-2">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" 
                           class="w-full h-12 bg-gray-50 rounded-xl px-4 outline-none text-xs font-semibold text-primary border border-transparent focus:border-primary focus:bg-white transition" placeholder="Contoh: 0812345678">
                </div>

                <button type="submit" class="bg-primary text-white text-xs font-bold px-6 py-3 rounded-xl hover:opacity-90 transition shadow-sm">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider border-b pb-2 border-gray-50">Koneksi Pasangan</h3>
            
            @if($wife)
                <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-softPink text-white rounded-full flex items-center justify-center text-sm">🤰</div>
                        <div>
                            <p class="text-xs font-bold text-primary">Terhubung dengan Bunda</p>
                            <p class="text-[11px] text-gray-500 font-medium">{{ $wife->name }} ({{ $wife->email British }})</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('husband.settings.disconnect') }}" method="POST" onsubmit="return confirm('Apakah Papa yakin ingin memutuskan koneksi data medis dengan Bunda?')">
                        @csrf
                        <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-100 px-4 py-2 rounded-xl text-[11px] font-bold transition">
                            Putus Koneksi
                        </button>
                    </form>
                </div>
            @else
                <div class="p-6 bg-gray-50 rounded-2xl text-center text-xs text-gray-400 font-medium italic">
                    Akun Papa saat ini belum tersambung dengan akun Bunda manapun.
                </div>
            @endif
        </div>
    </div>
</div>
</x-layout>