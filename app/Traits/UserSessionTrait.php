<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait UserSessionTrait
{
    public function getCurrentUser()
    {
        return Auth::user();
    }

    public function isFreelancer()
    {
        $user = $this->getCurrentUser();
        return $user && $user->activeRole() === 'freelancer';
    }

    public function isCliente()
    {
        $user = $this->getCurrentUser();
        return $user && $user->activeRole() === 'cliente';
    }

    public function isAdmin()
    {
        $user = $this->getCurrentUser();
        return $user && $user->activeRole() === 'admin';
    }
}
