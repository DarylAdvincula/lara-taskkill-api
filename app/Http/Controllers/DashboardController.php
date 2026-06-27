<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pendingTasksCount = $request->user()
            ->soloTasks()
            ->where('status', 'pending')
            ->count();
        $completedTasksCount = $request->user()
            ->soloTasks()
            ->where('status', 'done')
            ->count();
        $projectsCount = $request->user()
            ->projects()
            ->count();
        $recentTasks = $request->user()
            ->soloTasks()
            ->latest()
            ->take(5)
            ->get();
        $recentProjects = $request->user()
            ->projects()
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'data' => [
                'pending_tasks_count' => $pendingTasksCount,
                'completed_tasks_count' => $completedTasksCount,
                'projects_count' => $projectsCount,
                'total_tasks_count' => $pendingTasksCount + $completedTasksCount,
                'recent_tasks' => $recentTasks,
                'recent_projects' => $recentProjects
            ]
        ]);
    }
}
