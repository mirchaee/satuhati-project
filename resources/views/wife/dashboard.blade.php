@php
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $week = $user->getCurrentPregnancyWeek();
    $fetalInfo = $user->getFetalData(); // Mengambil data size ('Alpukat', 'Mangga', dll) dari model User

    // Ambil nama buah dari database, lalu kecilkan hurufnya untuk dicocokkan (ex: "Alpukat" jadi "alpukat")
    $fetalSizeName = strtolower($fetalInfo['size'] ?? '');

    // MAPPING BERDASARKAN NAMA BUAH (Bukan nomor minggu lagi)
    $uiMapping = [
        4  => ['img' => asset('images/fetal/blueberry.jpg'),      'desc' => '...'],
        8  => ['img' => asset('images/fetal/stroberi.jpg'),       'desc' => '...'],
        12 => ['img' => asset('images/fetal/jeruk_nipis.jpg'),    'desc' => '...'],
        16 => ['img' => asset('images/fetal/alpukat.jpg'),        'desc' => '...'],
        20 => ['img' => asset('images/fetal/pisang.jpg'),         'desc' => '...'],
        24 => ['img' => asset('images/fetal/mangga.jpg'),         'desc' => '...'],
        28 => ['img' => asset('images/fetal/terong.jpg'),         'desc' => '...'],
        32 => ['img' => asset('images/fetal/kelapa.jpg'),         'desc' => '...'],
        36 => ['img' => asset('images/fetal/semangka_kecil.jpg'), 'desc' => '...'],
        40 => ['img' => asset('images/fetal/semangka.jpg'),       'desc' => '...'],
    ];

    $currentUI = null;
    foreach ($uiMapping as $w => $info) {
        if ($w <= $week) $currentUI = $info;
    }
    $currentUI = $currentUI ?? $uiMapping[4];
@endphp

<x-layout>
    <!-- TOP HEADER -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-black text-deepBlue">Halo, Bunda {{ explode(' ', $user->name)[0] }}</h1>
            
            <div class="mt-2">
                @if($isPaired)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-pink-50 text-softPink text-xs font-bold border border-pink-100">
                        <i class="fa-solid fa-heart"></i> Terhubung dengan Suami
                    </span>
                @else
                    <div class="inline-flex items-center gap-4 p-3 rounded-2xl border-2 border-dashed border-pink-200 bg-pink-50/50">
                        <div class="text-xs">
                            <p class="text-mutedGray font-medium">Kode Pairing Anda:</p>
                            <p class="text-lg font-black text-softPink tracking-widest">{{ $user->pairing_code ?? 'SH-AV36FU' }}</p>
                        </div>
                        
                        <button type="button" onclick="salinKodeSakti('{{ $user->pairing_code ?? 'SH-AV36FU' }}')" class="p-2 bg-white rounded-xl text-softPink shadow-sm hover:scale-105 transition-all">
                            <i class="fa-solid fa-copy"></i>
                        </button>

                        <script>
                        function salinKodeSakti(teks) {
                            if (navigator.clipboard && window.isSecureContext) {
                                navigator.clipboard.writeText(teks).then(() => {
                                    alert('Kode disalin!');
                                }).catch(() => {
                                    salinManualTrik(teks);
                                });
                            } else {
                                salinManualTrik(teks);
                            }
                        }

                        function salinManualTrik(teks) {
                            let textArea = document.createElement("textarea");
                            textArea.value = teks;
                            textArea.style.position = "fixed";
                            textArea.style.left = "-9999px";
                            document.body.appendChild(textArea);
                            textArea.select();
                            try {
                                document.execCommand('copy');
                                alert('Kode disalin!');
                            } catch (err) {
                                alert('Gagal menyalin otomatis, kode Anda: ' + teks);
                            }
                            document.body.removeChild(textArea);
                        }
                        </script>
                    </div>
                @endif
            </div>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-mutedGray uppercase tracking-widest">Today's Date</p>
            <p class="text-xl font-bold text-deepBlue">{{ now()->format('l, M d') }}</p>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-12 gap-6">
        <!-- JANIN HERO WIDGET -->
        <div class="col-span-8">
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 rounded-[2.5rem] p-10 border border-white shadow-sm h-full flex items-center">
                <div class="relative z-10 w-1/2">
                    <span class="px-3 py-1 bg-white rounded-full text-[10px] font-bold text-deepBlue mb-4 inline-block shadow-sm">MINGGU KE-{{ $week }}</span>
                    
                    <!-- UKURAN JANIN DARI MODEL USER -->
                    <h2 class="text-5xl font-black text-deepBlue leading-tight mb-4 italic uppercase">Janin seukuran <br><span class="text-softPink underline decoration-pink-200">{{ $fetalInfo['size'] }}</span></h2>
                    
                    <p class="text-deepBlue/70 leading-relaxed mb-8 text-sm italic">{{ $currentUI['desc'] }}</p>
                    
                    <div class="flex gap-4 items-center mb-8">
                        <div class="px-4 py-2 bg-white/60 rounded-xl border border-white text-center">
                            <p class="text-[9px] font-bold text-mutedGray uppercase">Berat</p>
                            <p class="text-sm font-black text-deepBlue">{{ $fetalInfo['weight'] }}</p>
                        </div>
                        <div class="px-4 py-2 bg-white/60 rounded-xl border border-white text-center">
                            <p class="text-[9px] font-bold text-mutedGray uppercase">Panjang</p>
                            <p class="text-sm font-black text-deepBlue">{{ $fetalInfo['length'] }}</p>
                        </div>
                    </div>

                    <a href="/assessment" class="inline-flex items-center gap-3 px-8 py-4 bg-deepBlue text-white rounded-2xl font-bold hover:scale-105 transition-all shadow-lg shadow-blue-900/20">
                        <i class="fa-solid fa-stethoscope"></i> CEK KESEHATAN HARI INI
                    </a>
                </div>

                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-[350px] pr-10">
                    <img src="{{ $currentUI['img'] }}" class="w-[280px] h-[280px] object-cover rounded-[2rem] shadow-2xl rotate-3 border-8 border-white mx-auto" alt="Buah Janin">
                </div>
            </div>
        </div>

        <!-- MOOD & QUICK STATS -->
        <div class="col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <h3 class="font-bold text-deepBlue mb-4">Suasana Hati Bunda</h3>
                @php
                    $latestAssessment = Auth::user()->healthAssessments()->latest()->first();
                    $savedMood = $latestAssessment->mood_status ?? 'happy';
                    
                    $moodMap = [
                        'sad'     => ['emoji' => '😔', 'color' => 'bg-blue-50 border-blue-200'],
                        'happy'   => ['emoji' => '💖', 'color' => 'bg-pink-50 border-softPink'],
                        'neutral' => ['emoji' => '😐', 'color' => 'bg-slate-100 border-slate-300'],
                        'excited' => ['emoji' => '🥳', 'color' => 'bg-yellow-50 border-yellow-300'],
                    ];
                @endphp
                <div class="flex justify-between">
                    @foreach($moodMap as $key => $m)
                        <div class="w-12 h-12 flex items-center justify-center text-2xl rounded-2xl border transition-all 
                            {{ $savedMood == $key ? $m['color'] . ' ring-2 ring-offset-2' : 'bg-slate-50 border-transparent opacity-30 grayscale' }}">
                            {{ $m['emoji'] }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm" 
                     x-data="{ 
                        target: 2500, 
                        current: 0, 
                        inputKustom: '',
                        tambahAir(jumlah) {
                            this.current = Math.min(this.target, this.current + parseInt(jumlah));
                        },
                        resetAir() {
                            this.current = 0;
                        }
                     }">
                    
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="flex items-center gap-2 text-blue-500 mb-1">
                                <i class="fa-solid fa-droplet text-lg animate-bounce"></i>
                                <span class="text-[10px] font-black text-mutedGray uppercase tracking-wider">Konsumsi Air</span>
                            </div>
                            <p class="text-2xl font-black text-deepBlue">
                                <span x-text="(current / 1000).toFixed(1)">1.2</span> 
                                <span class="text-xs text-mutedGray font-normal">/ <span x-text="(target / 1000).toFixed(1)">2.5</span>L</span>
                            </p>
                        </div>
                        <button @click="resetAir()" class="text-[10px] text-gray-300 hover:text-red-400 font-bold uppercase transition-all">Reset</button>
                    </div>

                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mb-4">
                        <div class="bg-blue-400 h-full transition-all duration-500" :style="`width: ${(current / target) * 100}%`"></div>
                    </div>

                    <p class="text-[10px] font-bold text-deepBlue/50 uppercase mb-2">Pilih Ukuran Gelas:</p>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <button @click="tambahAir(200)" class="p-2 bg-slate-50 hover:bg-blue-50 hover:text-blue-500 rounded-xl text-center border border-slate-100 hover:border-blue-200 transition-all">
                            <p class="text-xs font-bold">200 ml</p>
                            <p class="text-[9px] text-mutedGray">Gelas Kecil</p>
                        </button>
                        <button @click="tambahAir(250)" class="p-2 bg-slate-50 hover:bg-blue-50 hover:text-blue-500 rounded-xl text-center border border-slate-100 hover:border-blue-200 transition-all">
                            <p class="text-xs font-bold">250 ml</p>
                            <p class="text-[9px] text-mutedGray">Gelas Belimbing</p>
                        </button>
                        <button @click="tambahAir(600)" class="p-2 bg-slate-50 hover:bg-blue-50 hover:text-blue-500 rounded-xl text-center border border-slate-100 hover:border-blue-200 transition-all">
                            <p class="text-xs font-bold">600 ml</p>
                            <p class="text-[9px] text-mutedGray">Botol Sedang</p>
                        </button>
                    </div>

                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="number" 
                                   x-model="inputKustom"
                                   placeholder="Misal: 100" 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-xl py-2 pl-4 pr-10 text-xs font-medium focus:ring-2 focus:ring-blue-400/20 focus:bg-white transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">ml</span>
                        </div>
                        <button @click="if(inputKustom > 0) { tambahAir(inputKustom); inputKustom = ''; }" 
                                class="px-4 bg-blue-500 text-white font-bold text-xs rounded-xl hover:bg-blue-600 transition-all shadow-md shadow-blue-500/10 flex items-center justify-center">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-[2rem] border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-400 text-xl shrink-0">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-mutedGray uppercase">Status Nutrisi Harian</p>
                        <p class="text-lg font-black text-deepBlue">Tercukupi Sangat Baik</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROGRESS BAR -->
        <div class="col-span-12 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
            <h3 class="text-xl font-bold text-deepBlue mb-6">Weekly Progress</h3>
            
            <div class="relative pt-4 pb-16">
                <div class="absolute top-5 w-full h-2 bg-slate-100 -translate-y-1/2 rounded-full"></div>
                @php
                    // Menghitung persentase progres (Minggu saat ini / 40 minggu)
                    $currentWeek = Auth::user()->getCurrentPregnancyWeek();
                    $progressPercent = ($currentWeek / 40) * 100;
                @endphp
                <div class="absolute top-5 h-2 bg-deepBlue -translate-y-1/2 rounded-full transition-all duration-1000" 
                    style="width: {{ $progressPercent }}%"></div>
                <div class="absolute top-5 -translate-x-1/2 -translate-y-1/2" 
                    style="left: {{ $progressPercent }}%">
                    <div class="w-6 h-6 bg-white border-4 border-deepBlue rounded-full shadow-lg"></div>
                </div>
                <div class="flex justify-between text-xs font-black text-mutedGray mt-8 uppercase">
                    <span>Minggu 1</span>
                    <span class="text-deepBlue">Minggu {{ $week }} (Today)</span>
                    <span>Minggu 40</span>
                </div>
            </div>
        </div>
    </div>
</x-layout>