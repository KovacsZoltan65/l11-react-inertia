<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a paginated listing of the projects.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Hozzon létre egy lekérdezést a projektek lekéréséhez
        $query = Project::query()
            // Kérésre alkalmazzon név szerinti szűrést
            ->when(request('name'), function ($q, $name) {
                return $q->where('name', 'like', '%' . $name . '%');
            })
            // Kérésre alkalmazza az állapot szerinti szűrést
            ->when(request('status'), function ($q, $status) {
                return $q->where('status', $status);
            })
            // Rendezze a projekteket a megadott mező és irány szerint
            ->orderBy(request('sort_field', 'created_at'), request('sort_direction', 'desc'))
            // Lapozza át az eredményeket úgy, hogy oldalanként 10 elem legyen
            ->paginate(10)
            // Helyezzen el 1 elemet az aktuális oldal mindkét oldalán
            ->onEachSide(1);

        // Adja vissza a projekteket erőforrásként, a kérésben használt lekérdezési paraméterekkel együtt,
        // és a munkamenet sikerüzenete.
        return inertia("Project/Index", [
            "projects" => ProjectResource::collection($query),
            // Ha a queryParams nincs megadva, állítsa be üres objektumra
            'queryParams' => request()->query() ?: null,
            // Ha nem ad meg sikert, állítsa nullára
            'success' => session('success', null),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Project/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        
        /** @var $mage \Illuminate\Http\UploadedFile */
        $image = $data['image'] ?? null;
        if ($image) {
            $data['image_path'] = $image->store('project/' . Str::random(), 'public');
        }
        
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        Project::create($data);
        
        return to_route('project.index')
            ->with('success', 'Project was created');
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
