<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
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
        
        $completedCount   = 0;
        $totalCount       = 0;
        $progressPercent  = 0;

        if ($sync) {
            $wife = User::find($sync->wife_id);

            if ($wife) {
                $pregnancyWeek = method_exists($wife, 'getCurrentPregnancyWeek') ? $wife->getCurrentPregnancyWeek() : 0;

                $latestAssessment = HealthAssessment::where('user_id', $wife->id)
                                                    ->with('symptoms')
                                                    ->latest()
                                                    ->first();

                $today = \Carbon\Carbon::today();

                $hasMissionsToday = DailyMission::where('user_id', $user->id)
                                    ->whereDate('created_at', $today)
                                    ->exists();

                if (!$hasMissionsToday) {
                    if ($pregnancyWeek <= 12) {
                        $pool = [
                            ['title' => 'Siapkan teh jahe hangat atau air putih untuk meredakan mual Bunda', 'points' => 15],
                            ['title' => 'Ingatkan Bunda untuk meminum suplemen Asam Folat harian', 'points' => 20],
                            ['title' => 'Ambil alih tugas dapur yang memicu bau menyengat agar Bunda tidak mual', 'points' => 25],
                            ['title' => 'Pastikan makanan Bunda kaya nutrisi dan hindari daging setengah matang', 'points' => 20],
                        ];
                    } elseif ($pregnancyWeek <= 27) {
                        $pool = [
                            ['title' => 'Elus perut Bunda dan ajak janin mengobrol selama 5 menit', 'points' => 15],
                            ['title' => 'Bantu pijat lembut area pinggang atau punggung Bunda yang mulai pegal', 'points' => 20],
                            ['title' => 'Ajak Bunda jalan pagi ringan selama 15 menit untuk memperlancar sirkulasi', 'points' => 15],
                            ['title' => 'Temani Bunda mendengarkan musik klasik atau instrumen relaksasi', 'points' => 10],
                        ];
                    } else {
                        $pool = [
                            ['title' => 'Cek bersama Bunda kelengkapan tas hospital bag untuk persalinan', 'points' => 30],
                            ['title' => 'Bantu posisikan bantal penyangga ekstra agar tidur malam Bunda lebih nyaman', 'points' => 20],
                            ['title' => 'Latih bersama teknik pernapasan untuk persiapan persalinan nanti', 'points' => 25],
                            ['title' => 'Simpan nomor darurat dokter kandungan atau bidan di kontak cepat HP Papa', 'points' => 20],
                        ];
                    }

                    $selectedMissions = collect($pool)->random(min(3, count($pool)));

                    foreach ($selectedMissions as $m) {
                        DailyMission::create([
                            'user_id'      => $user->id,
                            'title'        => $m['title'],
                            'points'       => $m['points'],
                            'is_completed' => false,
                            'target_week'  => $pregnancyWeek,
                            'mission_date' => today(),       
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                }
                $missions = DailyMission::where('user_id', $user->id)
                                        ->whereDate('mission_date', today())
                                        ->get();

                $completedCount  = $missions->where('is_completed', true)->count();
                $totalCount      = $missions->count();
                $progressPercent = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;

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
            'pregnancyWeek',
            'completedCount',
            'totalCount',
            'progressPercent'
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
    public function allMissions()
    {
        $user = Auth::user();
        
        $missions = DailyMission::where('user_id', $user->id)
                                ->latest()
                                ->get();

        return view('husband.missions', compact('missions'));
    }
}
