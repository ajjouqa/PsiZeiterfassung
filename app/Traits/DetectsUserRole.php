<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait DetectsUserRole
{
    public function detectUserRole(): ?string
    {
        if (Auth::guard('admin')->check()) {
            return 'admin';
        } elseif (Auth::guard('azubi')->check()) {
            return 'azubi';
        } elseif (Auth::guard('web')->check()) {
            return 'mitarbeiter';
        }

        return null;
    }
}