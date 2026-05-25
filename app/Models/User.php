<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable,HasFactory;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'pregnancy_week', 'hpht',
        'phone', 'avatar', 'pairing_code',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'hpht'              => 'date',
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if ($user->role === 'istri') {
                $user->pairing_code = 'SH-' . strtoupper(Str::random(6));
            }
        });
    }
    // ════════════════════════════════════════
    //  RELATIONSHIPS
    // ════════════════════════════════════════

    public function healthAssessments()
    {
        return $this->hasMany(HealthAssessment::class)->latest();
    }

    public function diaryEntries()
    {
        return $this->hasMany(DiaryEntry::class)->latest();
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function dailyMissions()
    {
        return $this->hasMany(DailyMission::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    // Sync record yang dimiliki user ini
    public function syncRecord()
    {
        if ($this->role === 'istri') {
            return $this->hasOne(SyncData::class, 'wife_id');
        }
        return $this->hasOne(SyncData::class, 'husband_id');
    }

    // ════════════════════════════════════════
    //  HELPER METHODS
    // ════════════════════════════════════════

    // Ambil pasangan yang sudah ter-pair
    public function getPairedPartner(): ?User
    {
        $sync = $this->syncRecord;
        if (!$sync || !$sync->status) return null;

        $partnerId = $this->role === 'istri'
            ? $sync->husband_id
            : $sync->wife_id;

        return User::find($partnerId);
    }

    // Cek apakah sudah paired
    public function isPaired(): bool
    {
        return $this->syncRecord?->status === true;
    }

    // Hitung minggu kehamilan dari HPHT
   public function getCurrentPregnancyWeek(): int
    {
        if ($this->hpht) {
            $weeks = (int) $this->hpht->diffInWeeks(now());
            return min($weeks, 42); // maksimal 42 minggu
        }
        return $this->pregnancy_week ?? 0;
    }

    // Data janin berdasarkan minggu kehamilan
    public function getFetalData(): array
    {
        $week = $this->getCurrentPregnancyWeek();

        $data = [
            4  => ['size' => 'Bluberi',    'weight' => '0.5g',  'length' => '0.2cm'],
            8  => ['size' => 'Stroberi',     'weight' => '1g', 'length' => '1.6cm'],
            12 => ['size' => 'Jeruk Nipis',  'weight' => '14g',   'length' => '5.4cm'],
            16 => ['size' => 'Alpukat',      'weight' => '100g',  'length' => '11.6cm'],
            20 => ['size' => 'Pisang',       'weight' => '300g',  'length' => '16.4cm'],
            24 => ['size' => 'Mangga',       'weight' => '600g',  'length' => '30cm'],
            28 => ['size' => 'Terong',       'weight' => '1kg',   'length' => '37cm'],
            32 => ['size' => 'Kelapa',       'weight' => '1.7kg', 'length' => '42cm'],
            36 => ['size' => 'Semangka Kecil','weight'=> '2.6kg', 'length' => '47cm'],
            40 => ['size' => 'Semangka',     'weight' => '3.4kg', 'length' => '51cm'],
        ];

        // Ambil data minggu yang paling dekat (tidak melebihi minggu saat ini)
        $closest = null;
        foreach ($data as $w => $info) {
            if ($w <= $week) $closest = $info;
        }

        return $closest ?? ['size' => 'Sangat kecil', 'weight' => '<1g', 'length' => '<1cm'];
    }

    public function getPairingCode(): ?string
{
    return $this->syncRecord?->pairing_code;
}
}