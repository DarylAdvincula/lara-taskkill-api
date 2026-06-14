<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSoloTaskRequest;
use App\Http\Requests\UpdateSoloTaskRequest;
use App\Models\SoloTask;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SoloTaskController extends Controller
{
    use AuthorizesRequests;
    
    public function index(Request $request)
    {
        $this->authorize('viewAny', SoloTask::class);

        $status = in_array($request->status, ['pending', 'done']) ? $request->status : 'all';
        $order = in_array($request->order, ['asc', 'desc']) ? $request->order : 'desc';
        $search = $request->search ?? '';

        $soloTasks = $request->user()
            ->soloTasks()
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->whereLike('title', '%' . $search . '%'))
            ->orderBy('created_at', $order)
            ->paginate($request->per_page ?? 15);

        return response()->json(['data' => ['tasks' => $soloTasks]]);
    }

    public function store(StoreSoloTaskRequest $request)
    {
        $soloTask = $request->user()
            ->soloTasks()
            ->create($request->validated());
        
        return response()->json([
            'message' => 'Task has been created!',
            'data' => ['task' => $soloTask->fresh()]
        ], 201);
    }

    public function update(UpdateSoloTaskRequest $request, SoloTask $soloTask)
    {
        $this->authorize('update', $soloTask);

        $soloTask->update($request->validated());
        
        return response()->json([
            'message' => 'Task has been updated!',
            'data' => ['task' => $soloTask->fresh()]
        ]);
    }

    public function show(SoloTask $soloTask)
    {
        $this->authorize('view', $soloTask);

        return response()->json(['data' => ['task' => $soloTask]]);
    }

    public function destroy(SoloTask $soloTask)
    {
        $this->authorize('delete', $soloTask);
        
        $soloTask->delete();

        return response()->json([
            'message' => 'Task has been deleted!',
            'data' => ['task' => $soloTask]
        ]);
    }
}
