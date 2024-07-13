<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    /**
     * Display a paginated listing of the projects.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Create a query to retrieve projects
        $query = Project::query()
            // Apply filtering by name if requested
            ->when(request('name'), function ($q, $name) {
                return $q->where('name', 'like', '%' . $name . '%');
            })
            // Apply filtering by status if requested
            ->when(request('status'), function ($q, $status) {
                return $q->where('status', $status);
            })
            // Order the projects by the specified field and direction
            ->orderBy(request('sort_field', 'created_at'), request('sort_direction', 'desc'))
            // Paginate the results, with 10 items per page
            ->paginate(10)
            // Include 1 item on each side of the current page
            ->onEachSide(1);

        // Return the projects as a resource, along with the query parameters used in the request,
        // and the success message from the session.
        return inertia("Project/Index", [
            "projects" => ProjectResource::collection($query),
            // If queryParams is not provided, set it to an empty object
            'queryParams' => request()->query() ?: null,
            // If success is not provided, set it to null
            'success' => session('success', null),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index_old()
    {
        $query = Project::query();
        
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'desc');
        
        if(request('name')){
            $query->where('name', 'like', '%' . request('name') . '%');
        }
        if(request('status')){
            $query->where('status', request('status'));
        }
        
        $projects = $query->orderBy($sortField, $sortDirection)
                ->paginate(10)
                ->onEachSide(1);
        
        return inertia("Project/Index", [
            "projects" => ProjectResource::collection($projects),
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
    public function store(StoreProjectRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified project and its associated tasks.
     *
     * @param Project $project The project to display.
     * @return \Inertia\Response The Inertia response.
     */
    /**
     * Display the specified project and its associated tasks.
     *
     * @param Project $project The project to display.
     * @return \Inertia\Response The Inertia response.
     */
    public function show(Project $project)
    {
        // Retrieve a collection of tasks for the project, eager loading the associated project.
        // Apply filtering by name if requested.
        $tasks = $project->tasks()
                ->when(request('name'), function ($query, $name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                })
                // Apply filtering by status if requested.
                ->when(request('status'), function ($query, $status) {
                    return $query->where('status', $status);
                })
                // Order the tasks by the specified field and direction.
                ->orderBy(request('sort_field', 'created_at'), request('sort_direction', 'desc'))
                // Paginate the results, with 10 items per page.
                ->paginate(10)
                // Include 1 item on each side of the current page.
                ->onEachSide(1);
        
        // Return the project and tasks as resources, along with the query parameters used in the request.
        return inertia('Project/Show', [
            // The project to display.
            'project' => new ProjectResource($project),
            // The tasks associated with the project, with query parameters used in the request.
            'tasks' => TaskResource::collection($tasks),
            // The query parameters used in the request, or null if not provided.
            'queryParams' => request()->query() ?: null,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
