<?php

namespace App\Http\Controllers;

use App\Models\DailyMission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function index()
    {
        $missions = Auth::user()
                        ->dailyMissions()
                        ->whereDate('mission_date', today())
                        ->get();

        return view('husband.missions', compact('missions'));
    }

    public function complete($id)
    {
        $mission = DailyMission::where('id', $id)
                               ->where('user_id', Auth::id())
                               ->first();

        if (!$mission) {
            return response()->json([
                'success' => false,
                'message' => 'Misi tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        $mission->update([
            'is_completed' => true,
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Misi berhasil diselesaikan! Mantap Papa.'
        ]);
    }
}