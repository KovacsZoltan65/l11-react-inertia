<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserCrudResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Jelenítse meg az erőforrás listáját.
     *
     * Ez a módszer a feladatok oldalszámozott listáját kéri le, név és állapot szerinti szűréssel,
     * és rendezni egy megadott mező és irány szerint. Tartalmazza a lekérdezési paramétereket is
     * kérés és a munkamenet sikerüzenete.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Töltse le a feladatok gyűjteményét, és töltse be a kapcsolódó projektet.
        $tasks = Task::with('project')
            // Kérésre alkalmazzon név szerinti szűrést.
            ->when(request('name'), function ($query, $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            // Kérésre alkalmazza az állapot szerinti szűrést.
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            // Rendezze a feladatokat a megadott mező és irány szerint.
            ->orderBy(request('sort_field', 'created_at'), request('sort_direction', 'desc'))
            // Lapozza át az eredményeket úgy, hogy oldalanként 10 elem legyen.
            ->paginate(10)
            // Helyezzen el 1 elemet az aktuális oldal mindkét oldalán.
            ->onEachSide(1);

        // A feladatokat erőforrásként adja vissza a kérésben használt lekérdezési paraméterekkel együtt,
        // és a munkamenet sikerüzenete.
        return inertia("Task/Index", [
            "tasks" => TaskResource::collection($tasks),
            'queryParams' => request()->query(),
            'success' => session('success'),
        ]);
        
    }

    /**
     * Mutassa meg az űrlapot az új erőforrás létrehozásához.
     *
     * Ez a módszer lekéri a projekteket és a felhasználókat az adatbázisból,
     * név szerint rendezi őket növekvő sorrendben, és átadja a
     * Feladat/Nézet létrehozása erőforrásként.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        // Az összes projekt lekérése az adatbázisból, név szerint, növekvő sorrendben.
        $projects = Project::query()
            ->orderBy('name', 'asc')
            ->get();
        
        // Az összes felhasználó lekérése az adatbázisból, név szerint, növekvő sorrendben.
        $users = User::query()
            ->orderBy('name', 'asc')
            ->get();
        
        // Erőforrásként adja át a projekteket és a felhasználókat a Feladat/Létrehozás nézetnek.
        return inertia('Task/Create', [
            'projects' => $projects, // projects resource
            'users' => $users, // users resource
        ]);
    }

    /**
     * Tároljon egy újonnan létrehozott erőforrást a tárhelyen.
     *
     * Ez a módszer egy újonnan létrehozott feladatot tárol az adatbázisban. Érvényesíti a
     * adatokat kér a StoreTaskRequest osztály használatával, majd létrehoz egy új feladatot
     * rögzítse az adatbázisban az érvényesített adatokkal.
     *
     * @param StoreTaskRequest $request The request object containing the validated data.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function store(StoreTaskRequest $request)
    {
        // Szerezze be az érvényesített adatokat a kérésből.
        $data = $request->validated();
        
        // Szerezze le a képet az adatokból.
        /** @var $image \Illuminate\Http\UploadedFile|null */
        $image = $data['image'] ?? null;
        
        // Állítsa be a Created_by és az updated_by mezőket a hitelesített felhasználó azonosítójára.
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        // Ha feltöltött egy képet, tárolja azt a „task” könyvtárban véletlenszerű névvel.
        if ($image) {
            $data['image_path'] = $image->store('task/' . Str::random(), 'public');
        }
        
        // Hozzon létre egy új feladatot az érvényesített adatokkal.
        Task::create($data);
        
        // Átirányítás a feladat indexoldalára egy sikerüzenettel.
        return to_route('task.index')
            ->with('success', 'Task was created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $tr = new TaskResource($task);
        
        return inertia('Task/Show', [
            'task' => $tr,
        ]);
    }

    /**
     * Mutassa meg az űrlapot a megadott erőforrás szerkesztéséhez.
     *
     * @param Task $task The task to be edited.
     * @return \Inertia\Response The Inertia response containing the edit view.
     */
    public function edit(Task $task)
    {
        // Név szerint rendezett projektek lekérése növekvő sorrendben.
        $projects = Project::query()->orderBy('name', 'asc')->get();
        
        // Az összes felhasználó lekérése név szerint növekvő sorrendben.
        $users = User::query()->orderBy('name', 'asc')->get();

        // Adja vissza a szerkesztési nézetet tartalmazó Inertia választ a feladattal,
        // projektek és felhasználók.
        return inertia("Task/Edit", [
            'task' => new TaskResource($task), // Task resource
            'projects' => ProjectResource::collection($projects), // Projects resource
            'users' => UserResource::collection($users), // Users resource
        ])->title('Edit Task'); // Set the title of the page
    }

    /**
     * Frissítse a megadott erőforrást a tárhelyen.
     *
     * @param UpdateTaskRequest $request The validated request data.
     * @param Task $task The task to update.
     *
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // Szerezze be az érvényesített adatokat a kérésből.
        $data = $request->validated();
        
        // Szerezze le a képet az adatokból.
        /** @var $image \Illuminate\Http\UploadedFile|null */
        $image = $data['image'] ?? null;
        
        // Ha feltöltött egy képet, tárolja azt a „feladat” könyvtárban véletlenszerű névvel.
        if ($image) {
            // Ha a feladat már rendelkezik képpel, törölje a régi képet.
            if ($task->image_path) {
                Storage::disk('public')->deleteDirectory(dirname($task->image_path));
            }
            // Tárolja az új képet véletlenszerű névvel.
            $data['image_path'] = $image->store('task/' . Str::random(), 'public');
        }
        
        // Állítsa be az updated_by mezőt a hitelesített felhasználó azonosítójára.
        $data['updated_by'] = Auth::id();
        
        // Frissítse a feladatot az érvényesített adatokkal.
        $task->update($data);

        // Átirányítás a feladat indexoldalára egy sikerüzenettel.
        return to_route('task.index')
            ->with('success', "Task {$task->name} was updated");
    }

    /**
     * Távolítsa el a megadott erőforrást a tárhelyről.
     *
     * @param Task $task The task to be deleted.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function destroy(Task $task)
    {
        // Szerezze meg a sikeres üzenethez törölni kívánt feladat nevét.
        $name = $task->name;
        
        // Törölje a feladatot az adatbázisból.
        $task->delete();
        
        // Ha a feladathoz van kép, törölje a képkönyvtárat a tárolóból.
        if ($task->image_path) {
            Storage::disk('public')->deleteDirectory(dirname($task->image_path));
        }
        
        // Átirányítás a feladat indexoldalára egy sikerüzenettel.
        return to_route('task.index')
            ->with('success', "Task \"$name\" was deleted");
    }
    
    /**
     * A hitelesített felhasználóhoz rendelt feladatok listájának lekérése.
     * A feladatok név és állapot szerint szűrhetők.
     * A feladatok meghatározott mező szerint, meghatározott irányban rendezhetők.
     *
     * @return \Inertia\Response The response containing the list of tasks.
     */
    public function myTasks()
    {
        // Szerezze meg a hitelesített felhasználót.
        $user = auth()->user();
        
        // Inicializálja a lekérdezést, hogy megkapja a felhasználóhoz rendelt feladatokat.
        $query = Task::where('assigned_user_id', $user->id);
        
        // Ha a kérés név- és állapotparaméterekkel rendelkezik, szűrje ki a feladatokat.
        if (request()->has(['name', 'status'])) {
            $query->where(function ($q) {
                $q->when(request('name'), function ($query, $name) {
                    // Szűrés név szerint.
                    $query->where('name', 'like', "%{$name}%");
                })
                ->when(request('status'), function ($query, $status) {
                    // Szűrés állapot szerint.
                    $query->where('status', $status);
                });
            });
        }
        
        // Határozza meg a rendezési mezőt és az irányt.
        $sortField = request("sort_field", 'created_at');
        $sortDirection = request("sort_direction", "desc");
        
        // A lapozott feladatok a megadott mező és irány szerint rendezve.
        $tasks = $query->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->onEachSide(1);

        // Adja vissza a feladatok listáját tartalmazó választ.
        return inertia("Task/Index", [
            "tasks" => TaskResource::collection($tasks),
            // Csak a szükséges lekérdezési paramétereket adja meg.
            'queryParams' => request()->only(['name', 'status', 'sort_field', 'sort_direction']),
            // Tartalmazza a munkamenet sikerüzenetét.
            'success' => session('success'),
        ]);
    }
}
