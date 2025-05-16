<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        
        return view('staff');
    }
}
