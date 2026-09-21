<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GudangController extends Controller
{
    /**
     * Request Perubahan Data
     */
    public function requests()
    {
        return view('gudang.requests');
    }
}
