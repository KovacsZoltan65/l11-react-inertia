<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCrudResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * A felhasználók oldalszámozott listájának megjelenítése.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Hozzon létre egy lekérdezést a felhasználók lekéréséhez opcionális szűréssel és rendezéssel.
        $query = User::query()
            // Kérésre alkalmazzon név szerinti szűrőt.
            ->when(request('name'), function($q, $name){
                return $q->where('name', 'LIKE', '%' . $name . '%');
            })
            // Kérésre alkalmazzon szűrőt e-mailben.
            ->when(request('email'), function($q, $email){
                return $q->where('email', 'LIKE', '%' . $email . '%');
            })
            // Rendezze a felhasználókat a megadott mező és irány szerint.
            ->orderBy(
                request('sort_field', 'created_at'), 
                request('sort_direction', 'desc')
            )
            // Lapozza át az eredményeket úgy, hogy oldalanként 10 elem legyen.
            ->paginate(10)
            // Helyezzen el 1 elemet az aktuális oldal mindkét oldalán.
            ->onEachSide(1);
        
        // Erőforrásként adja vissza a felhasználókat a kérésben használt lekérdezési paraméterekkel együtt,
        // és a munkamenet sikerüzenete.
        return inertia('User/Index', [
            'users' => UserCrudResource::collection($query),
            // Ha a queryParams nincs megadva, állítsa nullra.
            'queryParams' => request()->query() ?: null,
            // Ha a sikerüzenet nincs megadva, állítsa nullára.
            'success' => session('success', null)
        ]);
    }

    /**
     * Mutassa meg az űrlapot az új erőforrás létrehozásához.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        // Ez a metódus beolvassa a felhasználók listáját az Inertia keretrendszer segítségével.
        // Az inertia függvény első argumentuma a megjelenítendő template neve, a második pedig a
        // template adatstruktúrája, ami a modelleket és más adatokat tartalmazza, amiket a template
        // használ a megjelenítéshez. A template a `resources/js/Pages/User/Create.jsx` fájlban található.
        return inertia('User/Create');
    }

    /**
     * Tároljon egy újonnan létrehozott erőforrást a tárhelyen.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $data['email_verified_at'] = time();
        $data['password'] = bcrypt($data['password']);
        
        User::create($data);

        return to_route('user.index')
            ->with('success', 'User was created');
    }

    /**
     * Jelenítse meg a megadott erőforrást.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Mutassa meg az űrlapot a megadott erőforrás szerkesztéséhez.
     *
     * @param User $user The user to be edited.
     *
     * @return \Inertia\Response The rendered component.
     */
    public function edit(User $user)
    {
        // Ez a módszer a szerkesztési űrlapot tehetetlenséggel jeleníti meg.
        // Az első argumentum a sablon neve, a második a neki átadandó adat.
        // A sablon a `resources/js/Pages/User/Edit.jsx` fájlban található.
        return inertia('User/Edit', [
            'user' => new UserCrudResource($user)
        ]);
    }

    /**
     * Frissítse a megadott erőforrást a tárhelyen.
     *
     * @param UpdateUserRequest $request The validated request data.
     * @param User $user The user to update.
     *
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Szerezze meg az érvényesített kérésadatokat.
        $data = $request->validated();
        
        // Állítsa be az e-mail-cím ellenőrzésének dátumát.
        $data['email_verified_at'] = time();
        
        // Ellenőrizze, hogy adott-e jelszót.
        $password = $data['password'] ?? null;
        
        // Ha jelszót adtak meg, akkor azt kivonatolja.
        if ($password) {
            $data['password'] = bcrypt($password);
        } else {
            // Ha nem ad meg jelszót, távolítsa el az adatok közül.
            unset($data['password']);
        }
        
        // Frissítse a felhasználót az érvényesített adatokkal.
        $user->update($data);

        // Átirányítás a felhasználói indexoldalra egy sikerüzenettel.
        return to_route('user.index')
            ->with('success', "User \"$user->name\" was updated");
    }

    /**
     * Törölje a megadott felhasználót a tárhelyről.
     *
     * @param User $user The user to be deleted.
     *
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function destroy(User $user)
    {
        // Kérje le a törölni kívánt felhasználó nevét.
        $name = $user->name;
        
        // Törölje a felhasználót a tárhelyről.
        $user->delete();
        
        // Átirányítás a felhasználói indexoldalra egy sikerüzenettel.
        return to_route('user.index')
            ->with('success', "User {$name} was deleted");
    }
}
