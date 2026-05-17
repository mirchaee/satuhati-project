<?php
// app/Models/SyncData.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SyncData extends Model
{
    protected $table = 'sync_data';

    protected $fillable = [
        'wife_id', 'husband_id',
        'pairing_code', 'status', 'paired_at',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'paired_at' => 'datetime',
    ];

    // ════════════════════════════════════════
    //  RELATIONSHIPS
    // ════════════════════════════════════════

    public function wife()
    {
        return $this->belongsTo(User::class, 'wife_id');
    }

    public function husband()
    {
        return $this->belongsTo(User::class, 'husband_id');
    }

    // ════════════════════════════════════════
    //  STATIC HELPERS
    // ════════════════════════════════════════

    // Generate kode unik 6 karakter: "SH-XXXX"
    public static function generateCode(): string
    {
        do {
            // Format: SH-AB12 (2 huruf + 2 angka)
            $code = 'SH-'
                . strtoupper(Str::random(2))
                . rand(10, 99);
        } while (self::where('pairing_code', $code)->exists());

        return $code;
    }
}