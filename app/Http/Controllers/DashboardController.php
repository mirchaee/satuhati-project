<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SyncData;
use App\Models\HealthAssessment;
use App\Models\DailyMission;
use App\Models\EducationalContent;
use App\Models\Symptom; // TAMBAHKAN INI UNTUK FIX UNDEFINED TYPE SYMPTOM

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'istri') {
            $pregnancyWeek = $user->getCurrentPregnancyWeek();
            $fetalData = $user->getFetalData(); // Mengambil array [size, weight, length]

            return view('wife.dashboard', [
                'user'           => $user,
                'pregnancy_week' => $pregnancyWeek,
                'fetalData'      => $fetalData, // Kirim ke view
                'isPaired'       => $user->isPaired(),
                // ... data lainnya (mood, water, dll)
            ]);
        }
        
        // ════════════════════════════════════════
        // DASHBOARD SUAMI
        // ════════════════════════════════════════
        $sync = SyncData::where('husband_id', $user->id)
                        ->where('status', true)
                        ->first();

        $wife             = null;
        $latestAssessment = null;
        $missions         = collect();
        $guidance         = null;
        $pregnancyWeek    = 0;

        if ($sync) {
            $wife = User::find($sync->wife_id);

            if ($wife) {
                $pregnancyWeek = method_exists($wife, 'getCurrentPregnancyWeek') ? $wife->getCurrentPregnancyWeek() : 0;

                $latestAssessment = HealthAssessment::where('user_id', $wife->id)
                                                    ->with('symptoms')
                                                    ->latest()
                                                    ->first();

                $missions = DailyMission::where('user_id', $user->id)
                                        ->where('target_week', $pregnancyWeek)
                                        ->whereDate('mission_date', today())
                                        ->get();

                $guidance = EducationalContent::where('week_start', '<=', $pregnancyWeek)
                                            ->where('week_end', '>=', $pregnancyWeek)
                                            ->where('is_active', true)
                                            ->first();
            }
        }

        return view('husband.dashboard', compact(
            'user',
            'wife',
            'latestAssessment',
            'missions',
            'guidance',
            'pregnancyWeek'
        ));
    }
}