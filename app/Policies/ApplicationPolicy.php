<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApplicationPolicy
{
    public function accept(User $user, Application $application)
    {
        return $user->id === $application->listing->user_id;
    }

    public function deny(User $user, Application $application)
    {
        return $user->id === $application->listing->user_id;
    }
}
