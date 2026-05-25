<x-layout>
<div class="p-8 max-w-3xl mx-auto space-y-6 font-sans bg-[#F5F7FB] min-h-screen">
    
    <div>
        <h1 class="text-2xl font-black text-primary uppercase tracking-tight">⚙️ Pengaturan Profil Bunda</h1>
        <p class="text-xs text-gray-400 mt-1">Kelola data diri, informasi kehamilan, dan sinkronisasi akun Papa SatuHati</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 text-green-600 border border-green-100 rounded-2xl text-xs font-bold animate-fadeIn">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        
        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm space-y-6">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider border-b pb-2 border-gray-50">Informasi & Kehamilan Bunda</h3>
            
            <form action="{{ route('wife.settings.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-2">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" 
                           class="w-full h-12 bg-gray-50 rounded-xl px-4 outline-none text-xs font-semibold text-primary border border-transparent focus:border-primary focus:bg-white transition" placeholder="Contoh: 0812345678">
                </div>

                <div class="bg-pink-50/40 p-4 rounded-2xl border border-pink-100/60 mt-4 space-y-4">
                    <h4 class="text-xs font-bold text-pink-600 uppercase tracking-wide flex items-center gap-1.5">
                        🤰 Kalkulator Kehamilan Medis
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-2">Hari Pertama Haid Terakhir (HPHT)</label>
                            <input type="date" name="hpht" id="hpht_input" value="{{ $formattedHpht }}" onchange="hitungHPL()"
                                   class="w-full h-12 bg-white rounded-xl px-4 outline-none text-xs font-semibold text-primary border border-gray-200 focus:border-primary transition">
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-2">Hari Perkiraan Lahir (HPL)</label>
                            <input type="text" id="hpl_display" readonly placeholder="Menghitung estimasi..." 
                                   class="w-full h-12 bg-gray-100 rounded-xl px-4 outline-none text-xs font-bold text-pink-600 border border-transparent cursor-not-allowed">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 italic px-2">
                        *Data HPHT di atas otomatis diambil dari database pendaftaran Anda. Anda tetap bisa mengubahnya kembali jika terdapat kesalahan.
                    </p>
                </div>

                <button type="submit" class="bg-primary text-white text-xs font-bold px-6 py-3 rounded-xl hover:opacity-90 transition shadow-sm">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider border-b pb-2 border-gray-50">Koneksi Pasangan</h3>
            
            @if($partner)
                <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                    
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center text-xl shrink-0 shadow-sm">
                            👨
                        </div>
                        <div>
                            <p class="text-sm font-black text-deepBlue">Terhubung dengan Papa</p>
                            <p class="text-xs text-gray-500 font-semibold">{{ $partner->name }} ({{ $partner->email }})</p>
                        </div>
                    </div>

                    <form action="{{ route('wife.disconnect') }}" method="POST" onsubmit="return confirm('Apakah Bunda yakin ingin memutuskan hubungan dengan akun Papa?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-4 py-2 rounded-xl text-xs font-black transition-all duration-200">
                            Putus Koneksi
                        </button>
                    </form>

                </div>
            @else
                <div class="inline-flex items-center gap-4 p-3 rounded-2xl border-2 border-dashed border-pink-200 bg-pink-50/50">
                    <div class="text-xs">
                        <p class="text-mutedGray font-medium">Kode Pairing Anda:</p>
                        <p class="text-lg font-black text-softPink tracking-widest">{{ $user->pairing_code ?? 'SH-AV36FU' }}</p>
                    </div>
                    
                    <button type="button" onclick="salinKodeSakti('{{ $user->pairing_code ?? 'SH-AV36FU' }}')" class="p-2 bg-white rounded-xl text-softPink shadow-sm hover:scale-105 transition-all">
                        <i class="fa-solid fa-copy"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function hitungHPL() {
        const hphtVal = document.getElementById('hpht_input').value;
        if (!hphtVal) {
            document.getElementById('hpl_display').value = "Belum Mengisi HPHT";
            return;
        }

        const hphtDate = new Date(hphtVal);
        hphtDate.setDate(hphtDate.getDate() + 280); // Rumus Negele: +280 hari
        
        const opsi = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('hpl_display').value = "👶 " + hphtDate.toLocaleDateString('id-ID', opsi);
    }

    document.addEventListener("DOMContentLoaded", hitungHPL);
</script>
</x-layout>