<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class NursePolicy
{
    // policy: somente usuários com profile_id 3 podem gerenciar qualquer registro de enfermeiro
    public function manage(User $user)
    {
        return $user->profile_id == 3;
    }
}
