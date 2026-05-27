<?php

namespace App\Http\Controllers;

use App\Models\Faskes;

class FaskesController extends Controller
{
    public function index()
    {
        $faskes = Faskes::all();

        return view('shared.faskes', compact('faskes'));
    }
}