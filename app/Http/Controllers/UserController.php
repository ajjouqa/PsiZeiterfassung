<?php

namespace App\Http\Controllers;

use App\Models\Azubi;
use App\Models\User;
use App\Models\Admin;
use App\Models\XmppUserMapping;
use DB;
use Hash;
use Illuminate\Http\Request;
use Str;

class UserController extends Controller
{
    public function admins()
    {
        $admins = Admin::all();
        return view('users.admin',compact('admins'));
    }

    public function azubis()
    {
        $azubis = Azubi::all();
        return view('users.admin',compact('azubis'));
    }

    public function mitarbeiter()
    {
        $mitarbeiter = User::all();
        return view('users.admin',compact('mitarbeiter'));
    }





    public function create()
    {
        return view('users.AddUser');
    }

    public function store(Request $request)
    {
        $validated =  $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'user_type' => 'required|in:azubi,admin,mitarbeiter',
            'password' => 'required|string|min:8', 
            'phone' => 'nullable|string|max:15', 
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);


        
        
        if ($validated['user_type'] === 'azubi') {
            $user = Azubi::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

        }elseif ($validated['user_type'] === 'admin') {

            $user = Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

        }elseif( $validated['user_type'] === 'mitarbeiter') {
            
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'User created successfully with XMPP account');
    }



}
