<x-layout>
    <div class="max-w-3xl mx-auto pb-20">
        <!-- HEADER FORM -->
        <div class="flex items-center justify-between mb-8">
            <a href="/dashboard" class="flex items-center gap-2 text-mutedGray hover:text-deepBlue transition-all font-semibold">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div class="text-right">
                <h2 class="text-2xl font-black text-deepBlue uppercase tracking-tight">Cek Kesehatan</h2>
                <p class="text-[10px] text-mutedGray uppercase tracking-[0.2em] font-bold">Daily Assessment</p>
            </div>
        </div>

        <!-- FORM UTAMA -->
        <form action="/assessment" method="POST" class="space-y-6" id="assessmentForm">
            @csrf
            
            <!-- STEP 1: MOOD (Conversational UI) -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm transition-all hover:shadow-md">
                <div class="flex gap-5 mb-8">
                    <div class="w-14 h-14 bg-pink-50 rounded-2xl flex items-center justify-center text-softPink text-2xl shadow-sm shadow-pink-100">
                        <i class="fa-solid fa-face-smile-wink"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-deepBlue">Bagaimana perasaan Bunda hari ini?</h3>
                        <p class="text-sm text-mutedGray italic mt-1 italic">"Ceritakan sedikit mood Bunda ke SatuHati ya..."</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach([
                        ['emoji' => '😔', 'label' => 'Sedih', 'val' => 'sad'],
                        ['emoji' => '💖', 'label' => 'Senang', 'val' => 'happy'],
                        ['emoji' => '😐', 'label' => 'Biasa', 'val' => 'neutral'],
                        ['emoji' => '🥳', 'label' => 'Bersemangat', 'val' => 'excited']
                    ] as $mood)
                    <label class="cursor-pointer group">
                        <input type="radio" name="mood" value="{{ $mood['val'] }}" class="hidden peer" required>
                        <div class="p-6 rounded-[2rem] bg-slate-50 border-2 border-transparent peer-checked:border-softPink peer-checked:bg-pink-50 text-center transition-all group-hover:bg-slate-100 group-hover:scale-105">
                            <span class="text-4xl block mb-3">{{ $mood['emoji'] }}</span>
                            <span class="text-[10px] font-black text-mutedGray uppercase tracking-widest">{{ $mood['label'] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- STEP 2: GEJALA & LOGIKA ADAPTIF (Tugas Utama Anggota 3) -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex gap-5 mb-8">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 text-2xl shadow-sm shadow-blue-100">
                        <i class="fa-solid fa-hand-holding-heart"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-deepBlue">Apakah ada keluhan fisik?</h3>
                        <p class="text-sm text-mutedGray italic mt-1 italic">"Klik pada gejala yang Bunda rasakan..."</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 mb-6">
                    @foreach($symptoms as $s)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="symptoms[]" value="{{ $s->id }}" onchange="checkSymptomLogic(this)" class="hidden peer">
                        <div class="px-6 py-3 rounded-full bg-slate-50 border border-slate-200 text-sm font-bold text-deepBlue peer-checked:bg-deepBlue peer-checked:text-white peer-checked:border-deepBlue transition-all hover:bg-slate-100">
                            {{ $s->name }}
                        </div>
                    </label>
                    @endforeach
                </div>

                <!-- PERTANYAAN LANJUTAN ADAPTIF (Hanya muncul jika "Pusing" dicentang) -->
                <div id="adaptive-question" class="hidden mt-6 p-6 bg-red-50 rounded-[2rem] border border-red-100 animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-lg shadow-sm">🤖</div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-deepBlue leading-relaxed italic">
                                "Bunda merasa pusing? Apakah disertai pandangan yang kabur atau nyeri ulu hati yang hebat?"
                            </p>
                            <div class="flex gap-3 mt-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="pusing_detail" value="ya_bahaya" class="hidden peer">
                                    <div class="py-3 text-center bg-white rounded-2xl text-xs font-bold text-emergencyRed border border-red-100 peer-checked:bg-emergencyRed peer-checked:text-white transition-all shadow-sm">Ya, Kabur/Nyeri</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="pusing_detail" value="tidak" class="hidden peer">
                                    <div class="py-3 text-center bg-white rounded-2xl text-xs font-bold text-mutedGray border border-gray-100 peer-checked:bg-deepBlue peer-checked:text-white transition-all shadow-sm">Tidak Ada</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: SKALA NYERI (Interactive Slider) -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-xl font-black text-deepBlue">Skala Nyeri</h3>
                        <p class="text-xs text-mutedGray font-medium uppercase mt-1 tracking-widest">Pain Scale Monitoring</p>
                    </div>
                    <div id="painDisplay" class="w-20 h-20 rounded-3xl bg-slate-50 flex items-center justify-center border-4 border-white shadow-inner">
                        <span class="text-3xl font-black text-deepBlue">0</span>
                    </div>
                </div>
                
                <input type="range" name="pain_scale" min="0" max="10" value="0" 
                       class="w-full h-3 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-softPink"
                       id="painSlider">
                
                <div class="flex justify-between mt-5 text-[10px] font-black text-mutedGray uppercase tracking-tighter">
                    <span class="text-green-500">Nyaman</span>
                    <span>Ringan</span>
                    <span>Sedang</span>
                    <span class="text-emergencyRed">Hebat (Bahaya)</span>
                </div>
            </div>

            <!-- STEP 4: PARAMETER MEDIS (Vital Signs) -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex gap-5 mb-8">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 text-2xl shadow-sm shadow-emerald-100">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-deepBlue">Parameter Medis Utama</h3>
                        <p class="text-sm text-mutedGray mt-1 italic">Data ini akan langsung terkirim ke Dashboard Papa.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-mutedGray uppercase ml-4 tracking-widest">Tekanan Darah (mmHg)</label>
                        <input type="text" name="blood_pressure" placeholder="Cth: 120/80" 
                               class="w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] py-4 px-6 focus:border-deepBlue/20 focus:bg-white outline-none transition-all font-bold text-deepBlue shadow-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-mutedGray uppercase ml-4 tracking-widest">Detak Janin (bpm)</label>
                        <input type="number" name="fetal_heart_rate" placeholder="Cth: 140" 
                               class="w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] py-4 px-6 focus:border-deepBlue/20 focus:bg-white outline-none transition-all font-bold text-deepBlue shadow-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-mutedGray uppercase ml-4 tracking-widest">Berat Bunda (Kg)</label>
                        <input type="number" step="0.1" name="weight_kg" placeholder="Cth: 62.5" 
                               class="w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] py-4 px-6 focus:border-deepBlue/20 focus:bg-white outline-none transition-all font-bold text-deepBlue shadow-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-mutedGray uppercase ml-4 tracking-widest">Denyut Nadi Bunda</label>
                        <input type="number" name="maternal_pulse" placeholder="Cth: 80" 
                               class="w-full bg-slate-50 border-2 border-transparent rounded-[1.5rem] py-4 px-6 focus:border-deepBlue/20 focus:bg-white outline-none transition-all font-bold text-deepBlue shadow-sm">
                    </div>
                </div>
            </div>

            <!-- STEP 5: NOTES -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="text-xl font-black text-deepBlue mb-4">Catatan Tambahan</h3>
                <textarea name="notes" rows="4" placeholder="Bunda bisa curhat atau tulis keluhan lain di sini..." 
                          class="w-full bg-slate-50 border-none rounded-[1.5rem] py-4 px-6 focus:ring-2 focus:ring-deepBlue/10 transition-all italic text-sm text-deepBlue"></textarea>
            </div>

            <!-- SUBMIT ACTION -->
            <div class="pt-6 pb-20 text-center">
                <button type="submit" class="w-full md:w-auto px-16 py-6 bg-deepBlue text-white rounded-[2rem] font-black text-lg hover:scale-105 transition-all shadow-2xl shadow-blue-900/30 active:scale-95 flex items-center justify-center gap-3 mx-auto">
                    SIMPAN & ANALISIS <i class="fa-solid fa-arrow-right"></i>
                </button>
                <p class="text-[10px] text-mutedGray mt-6 font-bold uppercase tracking-[0.3em]">AI Expert System Integration</p>
            </div>
        </form>
    </div>

    <!-- JAVASCRIPT: LOGIKA ADAPTIF & INTERAKTIF (Tugas Anggota 3) -->
    <script>
        // 1. Logika Adaptif Pertanyaan Lanjutan
        function checkSymptomLogic(checkbox) {
            const adaptiveBox = document.getElementById('adaptive-question');
            
            if (checkbox.value == "3") { 
                if (checkbox.checked) {
                    adaptiveBox.classList.remove('hidden');
                } else {
                    adaptiveBox.classList.add('hidden');
                }
            }
        }

        // 2. Logika Real-time Slider Value
        const slider = document.getElementById('painSlider');
        const display = document.getElementById('painDisplay');
        if(slider) {
            slider.oninput = function() {
                display.querySelector('span').innerText = this.value;
            }
        }
    </script>
</x-layout>