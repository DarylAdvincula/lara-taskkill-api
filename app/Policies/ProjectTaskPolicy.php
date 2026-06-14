<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;

class ProjectTaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user, Project $project)
    {
        return $user->id === $project->user_id;
    }
    
    public function create(User $user, Project $project)
    {
        return $user->id === $project->user_id;
    }

    public function view(User $user, ProjectTask $projectTask)
    {
        return $user->id === $projectTask->project->user_id;
    }

    public function update(User $user, ProjectTask $projectTask)
    {
        return $user->id === $projectTask->project->user_id;
    }

    public function delete(User $user, ProjectTask $projectTask)
    {
        return $user->id === $projectTask->project->user_id;
    }
}
