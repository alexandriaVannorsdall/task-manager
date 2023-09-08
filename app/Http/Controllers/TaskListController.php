<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Resources\TaskListResource;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'sort_by' => ['nullable', 'in:created_at,updated_at,title'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
            'search' => ['nullable', 'string'],
        ]);

        $query = TaskList::where('user_id', Auth::id());

        if ($request->search ?? false) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $paginated = $query
            ->orderBy($request->sort_by ?? $this->sort_by, $request->sort_dir ?? $this->sort_dir)
            ->paginate($this->itemsPerPage);

        return $this->success([
            'task_lists' => TaskListResource::collection($paginated),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'lastPage' => $paginated->lastPage(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string']
        ]);

        $taskList = new TaskList();
        $taskList->user_id = Auth::id();
        $taskList->title = $request->title;
        $taskList->save();

        return $this->success([
            'task_list' => new TaskListResource($taskList),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param TaskList $taskList
     * @return JsonResponse
     */
    public function show(TaskList $taskList): JsonResponse
    {
        abort_unless($taskList->user_id === Auth::id(), 403);

        return $this->success([
            'task_list' => new TaskListResource($taskList),
        ]);
    }

    /**
     * Load all the associated tasks.
     *
     * @param TaskList $taskList
     * @return JsonResponse
     */
    public function tasks(TaskList $taskList): JsonResponse
    {
        abort_unless($taskList->user_id === Auth::id(), 403);

        return $this->success([
            'tasks' => TaskResource::collection($taskList->tasks),
        ]);
    }

    /**
     * Add a specific task to a specific task list.
     *
     * @param Request $request
     * @param TaskList $taskList
     * @return JsonResponse
     */
    public function addTask(Request $request, TaskList $taskList): JsonResponse
    {
        abort_unless($taskList->user_id === Auth::id(), 403);

        $request->validate([
            'title' => ['required', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        $task = new Task();
        $task->user_id = Auth::id();
        $task->task_list_id = $taskList->id;
        $task->status = TaskStatus::ToDo->value;
        $task->title = $request->title;
        $task->details = $request->details;
        $task->save();

        return $this->success([
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param TaskList $taskList
     * @return JsonResponse
     */
    public function update(Request $request, TaskList $taskList): JsonResponse
    {
        abort_unless($taskList->user_id === Auth::id(), 403);

        $request->validate([
            'title' => ['required', 'string']
        ]);

        $taskList->title = $request->title;
        $taskList->save();

        return $this->success([
            'task_list' => new TaskListResource($taskList),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TaskList $taskList
     * @return JsonResponse
     */
    public function destroy(TaskList $taskList): JsonResponse
    {
        abort_unless($taskList->user_id === Auth::id(), 403);

        $taskList->delete();

        return $this->success();
    }
}
