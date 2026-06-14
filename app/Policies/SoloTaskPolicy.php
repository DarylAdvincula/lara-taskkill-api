<?php

namespace App\Policies;

use App\Models\SoloTask;
use App\Models\User;

class SoloTaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        return true;
    }
    
    public function view(User $user, SoloTask $task)
    {
        return $user->id === $task->user_id;
    }

    public function update(User $user, SoloTask $task)
    {
        return $user->id === $task->user_id;
    }

    public function delete(User $user, SoloTask $task)
    {
        return $user->id === $task->user_id;
    }
}
