<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyController extends Controller
{
    // Trigger emergency alert
    // TODO Anggota 5: integrasikan dengan FCM/Pusher
    public function trigger(Request $request)
    {
        $user    = Auth::user();
        $partner = $user->getPairedPartner();

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pasangan yang terhubung.',
            ], 400);
        }

        // Tandai assessment terakhir sebagai emergency
        $latest = $user->healthAssessments()->first();
        if ($latest) {
            $latest->update(['is_emergency' => true]);
        }

        // TODO Anggota 5: broadcast(new EmergencyTriggered($user, $partner->id));

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi darurat telah dikirim ke suami.',
        ]);
    }
}