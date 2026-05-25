<?php

namespace App\Http\Controllers;

use App\Models\DailyMission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function complete($id)
    {
        try {
            $mission = DailyMission::where('id', $id)
                                   ->where('user_id', Auth::id())
                                   ->first();

            if (!$mission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Misi tidak ditemukan.'
                ], 404);
            }

            if ($mission->is_completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Misi sudah selesai sebelumnya.'
                ], 422);
            }

            $mission->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Misi berhasil diselesaikan! Mantap Papa. 💪'
            ]);

        } catch (\Throwable $e) {
            // Selalu return JSON, tidak pernah HTML
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server.',
                'debug'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
