<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SyncData;
use App\Models\HealthAssessment;
use App\Models\DailyMission;
use App\Models\EducationalContent;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ════════════════════════════════════════
        // DASHBOARD ISTRI
        // ════════════════════════════════════════
        if ($user->role === 'istri') {
            return view('wife.dashboard', [
                'user'             => $user,
                'isPaired'         => $user->isPaired(),
                'partner'          => $user->getPairedPartner(),
                'fetalData'        => $user->getFetalData(),
                'pregnancyWeek'    => $user->getCurrentPregnancyWeek(),
                'latestAssessment' => $user->healthAssessments()->first(),
                'todayDiary'       => $user->diaryEntries()
                                           ->whereDate('entry_date', today())
                                           ->first(),
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
                $pregnancyWeek = $wife->getCurrentPregnancyWeek();

                $latestAssessment = HealthAssessment::where('user_id', $wife->id)
                                                    ->with('symptoms')
                                                    ->latest()
                                                    ->first();

                // Gunakan nama kolom yang sesuai migration
                $missions = DailyMission::where('user_id', $user->id)
                                        ->where('target_week', $pregnancyWeek)
                                        ->whereDate('mission_date', today())
                                        ->get();

                // Gunakan nama kolom yang sesuai migration
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