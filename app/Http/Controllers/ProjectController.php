<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);
        
        $order = in_array($request->order, ['asc', 'desc']) ? $request->order : 'desc';
        $search = $request->search ?? '';

        $projects = $request->user()
            ->projects()
            ->when($search !== '', fn ($q) => $q->whereLike('name', '%' . $search . '%'))
            ->orderBy('created_at', $order)
            ->paginate($request->per_page ?? 15);
        
        return response()->json(['data' => ['projects' => $projects]]);
    }

    public function store(StoreProjectRequest $request)
    {
        $project = $request->user()
            ->projects()
            ->create($request->validated());
        
        return response()->json([
            'message' => 'Project has been created!',
            'data' => ['project' => $project->fresh()]
        ], 201);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return response()->json([
            'message' => 'Project has been updated!',
            'data' => ['project' => $project->fresh()]
        ]);
    }
    
    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $tasksCount = $project->tasks()->count();
        
        return response()->json(['data' => [
            'project' => $project,
            'tasks_count' => $tasksCount
        ]]);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Project has been deleted!',
            'data' => ['project' => $project]
        ]);
    }
}
