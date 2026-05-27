<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class EmergencyController extends Controller
{
    public function trigger(Request $request)
    {
        $user = auth()->user();

        $partner = $user->getPairedPartner();

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Pasangan tidak ditemukan'
            ], 404);
        }

        Notification::create([
            'user_id' => $partner->id,
            'title' => '🚨 Emergency Alert',
            'message' => $user->name . ' membutuhkan bantuan darurat!',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi darurat telah dikirim ke suami.'
        ]);
    }
}
