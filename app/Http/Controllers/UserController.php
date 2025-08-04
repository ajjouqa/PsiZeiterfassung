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
        $admins = Admin::query()
            ->with('xmppUserMapping')
            ->get();
        
        return view('users.admin',compact('admins'));
    }

    public function azubis()
    {
        $azubis = Azubi::query()
            ->with('xmppUserMapping')
            ->get();

        return view('users.azubi',compact('azubis'));
    }

    public function mitarbeiter()
    {
        $mitarbeiters = User::query()
            ->with('xmppUserMapping')
            ->get();
        return view('users.mitarbeiter',compact('mitarbeiters'));
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


        try{
            DB::beginTransaction();

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
            DB::commit();
            session()->flash('success', 'User created successfully');
            return redirect()->route('admin.dashboard')->with('success', 'User created successfully with XMPP account');
        }catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
        
        
       

    }


    



}
