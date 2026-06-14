<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectTaskRequest;
use App\Http\Requests\UpdateProjectTaskRequest;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Project $project)
    {
        $this->authorize('viewAny', [ProjectTask::class, $project]);
        
        $status = in_array($request->status, ['pending', 'done']) ? $request->status : 'all';
        $order = in_array($request->order, ['asc', 'desc']) ? $request->order : 'desc';
        $search = $request->search ?? '';

        $tasks = $project->tasks()
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->whereLike('title', '%' . $search . '%'))
            ->orderBy('created_at', $order)
            ->paginate($request->per_page ?? 15);

        return response()->json(['data' => ['tasks' => $tasks]]);
    }

    public function store(StoreProjectTaskRequest $request, Project $project)
    {
        $this->authorize('create', [ProjectTask::class, $project]);

        $task = $project->tasks()
            ->create($request->validated());

        return response()->json([
            'message' => 'Task has been created!',
            'data' => ['task' => $task->fresh()]
        ], 201);
    }

    public function update(UpdateProjectTaskRequest $request, ProjectTask $projectTask)
    {
        $this->authorize('update', $projectTask);

        $projectTask->update($request->validated());

        return response()->json([
            'message' => 'Task has been updated!',
            'data' => ['task' => $projectTask->fresh()]
        ]);
    }

    public function show(ProjectTask $projectTask)
    {
        $this->authorize('view', $projectTask);

        return response()->json(['data' => ['task' => $projectTask]]);
    }

    public function destroy(ProjectTask $projectTask)
    {
        $this->authorize('delete', $projectTask);

        $projectTask->delete();

        return response()->json([
            'message' => 'Task has been deleted!',
            'data' => ['task' => $projectTask]
        ]);
    }
}
