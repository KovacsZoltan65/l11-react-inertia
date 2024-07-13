<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This method retrieves a paginated list of tasks, with the ability to filter by name and status,
     * and sort by a specified field and direction. It also includes the query parameters used in the
     * request, and the success message from the session.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Retrieve a collection of tasks, eager loading the associated project.
        $tasks = Task::with('project')
            // Apply filtering by name if requested.
            ->when(request('name'), function ($query, $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            // Apply filtering by status if requested.
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            // Order the tasks by the specified field and direction.
            ->orderBy(request('sort_field', 'created_at'), request('sort_direction', 'desc'))
            // Paginate the results, with 10 items per page.
            ->paginate(10)
            // Include 1 item on each side of the current page.
            ->onEachSide(1);

        // Return the tasks as a resource, along with the query parameters used in the request,
        // and the success message from the session.
        return inertia("Task/Index", [
            "tasks" => TaskResource::collection($tasks),
            'queryParams' => request()->query(),
            'success' => session('success'),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index_orig()
    {
        $query = Task::query();
        
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');
        
        if(request('name')){
            $query->where('name', 'like', '%' . request('name') . '%');
        }
        if(request('status')){
            $query->where('status', request('status'));
        }
        
        $tasks = $query->orderBy($sortField, $sortDirection)
                ->paginate(10)
                ->onEachSide(1);
        
        return inertia("Task/Index", [
            "tasks" => TaskResource::collection($tasks),
            'queryParams' => request()->query() ?: null,
            'success' => session('success'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return inertia('Task/Show', [
            'task' => new TaskResource($task),
            'queryParams' => request()->query()?: null,
            'success' => session('success'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
