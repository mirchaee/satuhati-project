<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyMission;

class MissionController extends Controller
{
    public function complete(DailyMission $mission)
    {
        $mission->update([
            'is_completed' => true
        ]);

        return redirect()->back()->with('success', 'Misi harian berhasil diselesaikan! Papa Hebat! 🎉');
    }
}