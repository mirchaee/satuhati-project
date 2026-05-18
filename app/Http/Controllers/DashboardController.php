<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        if ($user->role === 'istri') {
            return view('wife.dashboard'); 
        }

        // ════════════════════════════════════════
        // LOGIKA UNTUK SUAMI 
        // ════════════════════════════════════════
        $sync = SyncData::where('husband_id', $user->id)
                        ->where('status', true)
                        ->first();

        $wife = null;
        $latestAssessment = null;
        $missions = collect();
        $guidance = null;

        if ($sync) {
            $wife = User::find($sync->wife_id);

            if ($wife) {
                $latestAssessment = HealthAssessment::where('user_id', $wife->id)
                                                    ->with('symptoms')
                                                    ->latest()
                                                    ->first();

                $missions = DailyMission::where('week', $wife->pregnancy_week)->get();

                $guidance = EducationalContent::where('min_age_weeks', '<=', $wife->pregnancy_week)
                                              ->where('max_age_weeks', '>=', $wife->pregnancy_week)
                                              ->first();
            }
        }

        return view('husband.dashboard', compact('wife', 'latestAssessment', 'missions', 'guidance'));
    }
}