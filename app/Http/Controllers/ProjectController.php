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
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        // Visszaadja a projekt létrehozásához tartozó komponenst
        return inertia('Project/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectRequest $request The request object containing the validated data.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function store(StoreProjectRequest $request)
    {
        // Get the validated data from the request.
        $data = $request->validated();
        
        // Get the image from the data.
        /** @var $image \Illuminate\Http\UploadedFile|null */
        $image = $data['image'] ?? null;
        
        // If an image was uploaded, store it in the 'project' directory with a random name.
        if ($image) {
            $data['image_path'] = $image->store('project/' . Str::random(), 'public');
        }
        
        // Set the created_by and updated_by fields to the authenticated user's ID.
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        // Create a new project with the validated data.
        Project::create($data);
        
        // Redirect to the project index page with a success message.
        return to_route('project.index')
            ->with('success', 'Project was created');
    }

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
    /**
     * Show the form for editing the specified resource.
     * 
     * @param Project $project The project to edit.
     * @return \Inertia\Response The Inertia response.
     */
    public function edit(Project $project)
    {
        // Return the project as a resource, to be used in the edit form.
        return inertia('Project/Edit', [
            'project' => new ProjectResource($project),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     * 
     * @param UpdateProjectRequest $request The validated request data.
     * @param Project $project The project to update.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        // Get the validated request data.
        $data = $request->validated();
        
        // If an image was uploaded, store it in the 'project' directory with a random name.
        // If the project already has an image, delete the old image.
        $image = $data['image'] ?? null;
        if ($image) {
            if ($project->image_path) {
                // Delete the old image directory.
                Storage::disk('public')->deleteDirectory(dirname($project->image_path));
            }
            // Store the new image with a random name.
            $data['image_path'] = $image->store('project/' . Str::random(), 'public');
        }
        
        $data['updated_by'] = Auth::id();

        // Update the project with the validated data.
        $project->update($data);

        // Redirect to the project index page with a success message.
        return to_route('project.index')->with('success', "Project " . $project->name . " was updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $name = $project->name;
        $project->delete();
        
        if ($project->image_path) {
            Storage::disk('public')->deleteDirectory(dirname($project->image_path));
        }

        return to_route('project.index')
                ->with('success', "Project {$name} was deleted");
    }
}
