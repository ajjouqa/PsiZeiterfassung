<?php

namespace App\Http\Controllers;

use App\Events\LoginEvent;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    //
    public function login($userId, $role)
    {
        event(new LoginEvent($userId, $role));
    }
}
