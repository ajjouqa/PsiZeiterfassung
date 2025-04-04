<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function AdminDashboard()
    {
        return view('admin.dashboard.index');
    }

    public function AzubiDashboard()
    {
        return view('azubi.dashboard.index');
    }

    public function MitarbeiterDashboard()
    {
        return view('mitarbeiter.dashboard.index');
    }
}
