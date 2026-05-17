<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Routing berdasarkan role
        // View-nya dibuat oleh Anggota 2 & 3 & 4
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

        // Role: suami
        $wife           = $user->getPairedPartner();
        $wifeAssessment = $wife?->healthAssessments()->first();

        return view('husband.dashboard', [
            'user'           => $user,
            'isPaired'       => $user->isPaired(),
            'wife'           => $wife,
            'wifeAssessment' => $wifeAssessment,
            'pregnancyWeek'  => $wife?->getCurrentPregnancyWeek() ?? 0,
            'todayMissions'  => $user->dailyMissions()
                                     ->whereDate('mission_date', today())
                                     ->get(),
        ]);
    }
}