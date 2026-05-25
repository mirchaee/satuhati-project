<?php

namespace App\Http\Controllers;

use App\Models\HealthAssessment;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    // Halaman form assessment conversational UI
    // TODO Anggota 3: buat view wife.assessment
    public function index()
    {
        $symptoms = Symptom::all(); 
        return view('wife.assessment', compact('symptoms'));
    }

    // Simpan hasil assessment & hitung risiko
    // TODO Anggota 3: implementasi kalkulasi risiko
    public function store(Request $request)
    {
        $data = $request->validate([
            'mood'             => 'required|string',
            'symptoms'         => 'nullable|array',
            'symptoms.*'       => 'exists:symptoms,id',
            'pain_scale'       => 'required|integer|min:0|max:10',
            'blood_pressure'   => 'nullable|string',
            'fetal_heart_rate' => 'nullable|integer',
            'weight_kg'        => 'nullable|numeric',
            'notes'            => 'nullable|string',
            'pusing_detail'    => 'nullable|string',
        ]);

        $user = Auth::user();
        // Kalkulasi risiko — TODO Anggota 3 lengkapi logikanya
        [$riskScore, $riskLevel] = $this->calculateRisk($data);

        $assessment = $user->healthAssessments()->create([
            'mood_status'      => $data['mood'],
            'notes'            => $data['notes'] ?? null,
            'pain_scale'       => $data['pain_scale'] ?? 0,
            'weight_kg'        => $data['weight_kg'] ?? null,
            'blood_pressure'   => $data['blood_pressure'] ?? null,
            'fetal_heart_rate' => $data['fetal_heart_rate'] ?? null,
            'risk_score'       => $riskScore,
            'risk_level'       => $riskLevel,
        ]);

        // Attach symptoms ke assessment
        if (!empty($data['symptoms'])) {
            $assessment->symptoms()->attach($data['symptoms']);
        }

        // TODO Anggota 5: broadcast ke suami via Pusher/Reverb

        return redirect()->route('wife.health-summary')->with([
            'success'    => true,
            'risk_level' => $riskLevel,
            'message'    => $this->getRiskMessage($riskLevel),
        ]);
    }

    // Halaman ringkasan kesehatan
    // TODO Anggota 3: buat view wife.health-summary
    public function summary()
    {
        $user        = Auth::user();
        $assessments = $user->healthAssessments()->latest()->take(10)->get();
        $latest      = $assessments->first();

        return view('wife.health-summary', compact('assessments', 'latest', 'user'));
    }

    private function calculateRisk(array $data): array
    {
        $score = 0;
        $forceBahaya = false;

        // Scoring Mood
        $moodWeights = ['sad' => 25, 'neutral' => 10, 'happy' => 0, 'excited' => 0];
        $score += $moodWeights[$data['mood']] ?? 10;

        // Scoring Gejala & Check Critical Status
        if (!empty($data['symptoms'])) {
            $symptoms = Symptom::whereIn('id', $data['symptoms'])->get();
            foreach ($symptoms as $symptom) {
                $score += $symptom->weight;
                if ($symptom->is_critical) $forceBahaya = true;
            }
        }

        // Logika Adaptif (Preeklampsia Check dari input JS)
        if (isset($data['pusing_detail']) && $data['pusing_detail'] === 'ya_bahaya') {
            $forceBahaya = true;
            $score += 50;
        }

        // Vital Signs Scoring
        $pain = $data['pain_scale'] ?? 0;
        $fhr = $data['fetal_heart_rate'] ?? 140;

        if ($pain >= 7 || $fhr < 120 || $fhr > 160) {
            $forceBahaya = true;
        }

        // Penentuan Level
        $level = match(true) {
            $forceBahaya || $score >= 60 => 'Bahaya',
            $score >= 30 || $pain >= 4   => 'Waspada', 
            default                      => 'Aman',
        };
        return [min($score, 100), $level];
    }

    private function getRiskMessage(string $level): string
    {
        return match($level) {
            'Aman'    => 'Kondisi Bunda terpantau stabil dan aman. Sehat selalu ya! 💚',
            'Waspada' => 'Ada beberapa indikasi yang perlu diperhatikan. Jangan terlalu lelah ya, Bunda. 💛',
            'Bahaya'  => 'Peringatan! Kondisi memerlukan perhatian medis. Papa sudah kami beri notifikasi. 🚨',
        };
    }
}