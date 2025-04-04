<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function admins()
    {
        $admins = \App\Models\Admin::all();
        return view('users.admin',compact('admins'));
    }

    public function azubis()
    {
        $azubis = \App\Models\Azubi::all();
        return view('users.admin',compact('azubis'));
    }

    public function mitarbeiter()
    {
        $mitarbeiter = \App\Models\User::all();
        return view('users.admin',compact('mitarbeiter'));
    }

}
