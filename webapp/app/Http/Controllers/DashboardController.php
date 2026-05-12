<?php

namespace App\Http\Controllers;


class DashboardController extends Controller
{
    public function showDashboard()
    {
        return view('dashboard.index');
    }

    public function showConfiguration()
    {
        return view('dashboard.configuration');
    }
}
