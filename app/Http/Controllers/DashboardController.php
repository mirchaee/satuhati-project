<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SyncData;
use App\Models\HealthAssessment;
use App\Models\DailyMission;
use App\Models\EducationalContent;
use App\Models\Symptom; 

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'istri') {
            $pregnancyWeek = $user->getCurrentPregnancyWeek();
            $fetalData = $user->getFetalData(); 

            return view('wife.dashboard', [
                'user'          => $user,
                'week'          => $pregnancyWeek,
                'fetalInfo'     => $fetalData,    
                'isPaired'      => $user->isPaired(),
                'pregnancyWeek' => $pregnancyWeek,
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
    public function settings()
    {
        $user = Auth::user();
        
        $sync = \Illuminate\Support\Facades\DB::table('sync_data')
            ->where('husband_id', $user->id)
            ->first();
        
        $wife = null;
        if ($sync) {
            $wife = \App\Models\User::find($sync->wife_id);
        }

        return view('husband.settings', compact('user', 'wife'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
        ]);

        $user->update($data);

        return redirect()->route('husband.settings')->with('success', 'Profil Papa berhasil diperbarui!');
    }

    public function disconnectWife()
    {
        $user = Auth::user();
        
        \Illuminate\Support\Facades\DB::table('sync_data')
            ->where('husband_id', $user->id)
            ->delete();

        return redirect()->route('dashboard')->with('success', 'Hubungan akun berhasil diputuskan.');
    }
}