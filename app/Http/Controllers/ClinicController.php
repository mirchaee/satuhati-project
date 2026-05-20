<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClinicController extends Controller
{
    // Halaman rekomendasi faskes
    // TODO Anggota 5: integrasikan Google Maps API
    public function index()
    {
        return view('shared.clinics');
    }
}