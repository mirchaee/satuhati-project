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
        $symptoms = Symptom::all(); // daftar gejala untuk pilihan
        return view('wife.assessment', compact('symptoms'));
    }

    // Simpan hasil assessment & hitung risiko
    // TODO Anggota 3: implementasi kalkulasi risiko
    public function store(Request $request)
    {
        $data = $request->validate([
            'mood_status'      => 'required|string|max:50',
            'symptoms'         => 'nullable|array',
            'symptoms.*'       => 'exists:symptoms,id',
            'pain_scale'       => 'nullable|integer|min:1|max:10',
            'notes'            => 'nullable|string|max:500',
            'weight_kg'        => 'nullable|numeric',
            'blood_pressure'   => 'nullable|string|max:10',
            'fetal_heart_rate' => 'nullable|integer',
        ]);

        // Kalkulasi risiko — TODO Anggota 3 lengkapi logikanya
        [$riskScore, $riskLevel] = $this->calculateRisk($data);

        $assessment = Auth::user()->healthAssessments()->create([
            'mood_status'      => $data['mood_status'],
            'notes'            => $data['notes'] ?? null,
            'pain_scale'       => $data['pain_scale'] ?? null,
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

        return response()->json([
            'success'    => true,
            'risk_level' => $riskLevel,
            'risk_score' => $riskScore,
            'message'    => $this->getRiskMessage($riskLevel),
        ]);
    }

    // Halaman ringkasan kesehatan
    // TODO Anggota 3: buat view wife.health-summary
    public function summary()
    {
        $user        = Auth::user();
        $assessments = $user->healthAssessments()->take(10)->get();
        $latest      = $assessments->first();

        return view('wife.health-summary', compact('assessments', 'latest', 'user'));
    }

    private function calculateRisk(array $data): array
    {
        $score = 0;

        // Mood scoring
        $moodScores = [
            'Senang' => 0,  'Baik'  => 0,
            'Biasa'  => 10, 'Cemas' => 20,
            'Lelah'  => 15, 'Sedih' => 25,
            'Panik'  => 35,
        ];
        $score += $moodScores[$data['mood_status']] ?? 10;

        // Symptom scoring — ambil bobot dari DB
        if (!empty($data['symptoms'])) {
            $symptoms = Symptom::whereIn('id', $data['symptoms'])->get();
            foreach ($symptoms as $symptom) {
                $score += $symptom->weight;
                if ($symptom->is_critical) $score += 20;
            }
        }

        // Pain scale scoring
        $pain = $data['pain_scale'] ?? 0;
        if ($pain >= 7)      $score += 30;
        elseif ($pain >= 4)  $score += 15;

        $level = match(true) {
            $score >= 60 => 'Bahaya',
            $score >= 30 => 'Waspada',
            default      => 'Aman',
        };

        return [min($score, 100), $level];
    }

    private function getRiskMessage(string $level): string
    {
        return match($level) {
            'Aman'    => 'Kondisimu baik-baik saja hari ini! Tetap jaga kesehatan ya, Bunda. 💚',
            'Waspada' => 'Ada beberapa gejala yang perlu diperhatikan. Istirahat yang cukup dan informasikan ke suami. 💛',
            'Bahaya'  => 'Suami dan fasilitas kesehatan terdekat sudah diberitahu. Segera hubungi dokter! 🚨',
        };
    }
}