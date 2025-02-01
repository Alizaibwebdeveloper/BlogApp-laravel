<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $data = [
            'PageTitle' => 'Dashboard'
        ];
        return view('back.layout.pages.dashboard', $data);
    }
}
