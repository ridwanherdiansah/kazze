<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Dashboard';
        $type_menu = 'dashboard';
        
        return view(
        'Pages.admin.dashboard', 
        compact(
            'title', 
            'type_menu',
        ));
    }
}
