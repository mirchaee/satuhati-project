<?php

namespace App\Http\Controllers;

use App\Models\DailyMission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    // Tampilkan misi harian suami
    // TODO Anggota 4: buat view husband.missions
    public function index()
    {
        $missions = Auth::user()
                        ->dailyMissions()
                        ->whereDate('mission_date', today())
                        ->get();

        return view('husband.missions', compact('missions'));
    }

    // Tandai misi selesai
    public function complete(DailyMission $mission)
    {
        // Pastikan misi milik user yang login
        if ($mission->user_id !== Auth::id()) {
            abort(403);
        }

        $mission->update(['is_completed' => true]);

        return response()->json(['success' => true]);
    }
}