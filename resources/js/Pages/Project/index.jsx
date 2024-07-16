import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router } from "@inertiajs/react";
import TableHeading from "@/Components/TableHeading";
import Pagination from "@/Components/Pagination";
import TextInput from "@/Components/TextInput";

import {PROJECT_STATUS_TEXT_MAP, PROJECT_STATUS_CLASS_MAP} from "@/constants.jsx";
import SelectInput from "@/Components/SelectInput";

export default function Index({ auth, projects, queryParams = null, success }) {

    /**
     * Ha a queryParams nincs megadva, állítsa be üres objektumra.
     * Erre azért van szükség, mert a komponens a következővel lesz meghívva
     * a kellékek, amelyeket az útvonalból átadnak neki, és ha a
     * az útvonal nem adja meg a queryParams-t, ez definiálatlan lesz.
     */
    queryParams = queryParams || {};

    /**
     * Kezeli a keresési beviteli mező változási eseményét.
     * Ha az érték nem üres, akkor hozzáadja az értéket a queryParams objektumhoz.
     * Ha az érték üres, törli a kulcsot a queryParams objektumból.
     * Végül egy GET kérést ad a project.index útvonalhoz a frissített queryParams segítségével.
     *
     * @param {string} name - The name of the field being searched.
     * @param {string} value - The value being searched.
     */
    const searchFieldChanged = (name, value) => {
        // Ha az érték nem üres, adja hozzá a queryParams objektumhoz a megadott névvel.
        if(value){
            queryParams[name] = value;
        }
        // Ha az érték üres, törölje a kulcsot a queryParams objektumból.
        else{
            delete queryParams[name];
        }

        // Készítsen GET kérést a project.index útvonalhoz a frissített queryParams segítségével.
        router.get(route("project.index", queryParams));
    };

    /**
     * Kezeli a rendezési mező változási eseményét.
     * Ha a mező megegyezik az aktuális rendezési mezővel,
     * a rendezés irányát váltja. Ellenkező esetben beáll
     * a rendezési mező és az új mező iránya.
     * Végül egy GET kérést ad a project.index útvonalhoz a frissített queryParams segítségével.
     *
     * @param {string} name - The name of the sort field.
     */
    const sortChanged = (name) => {
        // Mentse el az aktuális rendezési mezőt és irányt a queryParams objektumból.
        const currentSortField = queryParams.sort_field;
        const currentSortDirection = queryParams.sort_direction;

        // Ha a rendezési mező megegyezik az aktuális rendezési mezővel,
        // a rendezés irányának váltása. Ellenkező esetben állítsa be a rendezési mezőt és az irányt az új mezőre.
        queryParams.sort_field = name;
        queryParams.sort_direction = currentSortField === name && currentSortDirection === 'asc' ? 'desc' : 'asc';

        // Készítsen GET kérést a project.index útvonalhoz a frissített queryParams segítségével.
        router.get(route("project.index", queryParams));
    };

    const deleteProject = (project) => {
        if (!window.confirm("Are you sure you want to delete the project?")) {
            return;
        }
        router.delete(route("project.destroy", project.id));
    };
    /**
     * Kezeli a keresési mezők billentyűlenyomásának eseményét.
     * Ha a lenyomott billentyű 'Enter', akkor meghívja a searchFieldChanged függvényt.
     *
     * @param {string} name - The name of the field being searched.
     * @param {object} e - The key press event object.
     */
    const onKeyPress = (name, e) => {
        // Ha a lenyomott billentyű nem „Enter”, ne tegyen semmit.
        if(e.key !== 'Enter') return;

        // Hívja meg a searchFieldChanged függvényt a mező nevével és célértékével.
        searchFieldChanged(name, e.target.value);
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
            <div className="flex justify-between items-center">
                <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >Projects</h2>
                <Link href={route('project.create')} 
                    className="bg-emerald-500 py-1 px-3 text-white rounded shadow transition-all hover:bg-emerald-600"
                >Add new</Link>
            </div>
                
            }
        >

            <Head title="Projects" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">

                {/* SUCCESS */}
                {success && (
                    <div className="bg-emerald-500 py-2 px-4 text-white rounded mb-4">
                        {success}
                    </div>
                )}

                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="overflow-auto">
                                <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b-2 border-gray-500">
                                        
                                        <tr className="text-nowrap">

                                            <TableHeading
                                                name="id"
                                                sort_field={queryParams.sort_field}
                                                sort_direction={queryParams.sort_direction}
                                                sortChanged={sortChanged}>ID</TableHeading>

                                            <th className="px-3 py-3">Image</th>

                                            <TableHeading
                                                name="name"
                                                sort_field={queryParams.sort_field}
                                                sort_direction={queryParams.sort_direction}
                                                sortChanged={sortChanged}>Name</TableHeading>

                                            <TableHeading
                                                name="status"
                                                sort_field={queryParams.sort_field}
                                                sort_direction={queryParams.sort_direction}
                                                sortChanged={sortChanged}>Status</TableHeading>

                                            <TableHeading
                                                name="created_at"
                                                sort_field={queryParams.sort_field}
                                                sort_direction={queryParams.sort_direction}
                                                sortChanged={sortChanged}>Create Date</TableHeading>

                                            <TableHeading
                                                name="due_date"
                                                sort_field={queryParams.sort_field}
                                                sort_direction={queryParams.sort_direction}
                                                sortChanged={sortChanged}>Due Date</TableHeading>

                                            <th className="px-3 py-3">Created By</th>
                                            <th className="px-3 py-3 text-right">Actions</th>

                                        </tr>
                                        
                                    </thead>
                                    <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b-2 border-gray-500">
                                        <tr className="text-nowrap">
                                            <th className="px-3 py-3"></th>
                                            <th className="px-3 py-3"></th>
                                            <th className="px-3 py-3">
                                                <TextInput className="w-full" 
                                                    defaultValue={queryParams.name}
                                                    placeholder="Project Name"
                                                    onBlur={e => searchFieldChanged('name', e.target.value)}
                                                    onKeyPress={e => onKeyPress('name', e)}/>
                                            </th>
                                            <th className="px-3 py-3">
                                                <SelectInput className="w-full" 
                                                    defaultValue={queryParams.status}
                                                    onChange={(e) => searchFieldChanged('status', e.target.value)}>
                                                    <option value="">Select Status</option>
                                                    <option value="pending">Pending</option>
                                                    <option value="in_progress">In Progress</option>
                                                    <option value="completed">Completed</option>
                                                </SelectInput>
                                            </th>
                                            <th className="px-3 py-3"></th>
                                            <th className="px-3 py-3"></th>
                                            <th className="px-3 py-3 text-right"></th>
                                            <th className="px-3 py-3 text-right"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {projects.data.map((project) => (
                                            <tr
                                                className="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                                key={project.id}
                                            >
                                                {/* ID */}
                                                <td className="px-3 py-2">{project.id}</td>
                                                
                                                {/* Image */}
                                                <td className="px-3 py-2">
                                                    <img src={project.image_path} style={{ width: 60 }} />
                                                </td>

                                                {/* Name */}
                                                <th className="px-3 py-2 text-gray-100 text-nowrap hover:underline">
                                                    <Link href={route("project.show", project.id)}>
                                                        {project.name}
                                                    </Link>
                                                </th>

                                                {/* Status */}
                                                <td className="px-3 py-2">
                                                    <span
                                                        className={
                                                            "px-2 py-1 rounded text-white " +
                                                            PROJECT_STATUS_CLASS_MAP[project.status]
                                                        }
                                                    >
                                                        {PROJECT_STATUS_TEXT_MAP[project.status]}
                                                    </span>
                                                </td>
                                                
                                                {/* Created Date */}
                                                <td className="px-3 py-2 text-nowrap">
                                                    {project.created_at}
                                                </td>
                                                {/* Due Date */}
                                                <td className="px-3 py-2 text-nowrap">
                                                    {project.due_date}
                                                </td>
                                                {/* Created By */}
                                                <td className="px-3 py-2">{project.createdBy.name}</td>
                                                {/* Actions */}
                                                <td className="px-3 py-2 text-nowrap">
                                                    <Link
                                                        href={route("project.edit", project.id)}
                                                        className="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-1"
                                                    >
                                                        Edit
                                                    </Link>
                                                    <button
                                                        onClick={(e) => deleteProject(project)}
                                                        className="font-medium text-red-600 dark:text-red-500 hover:underline mx-1"
                                                    >
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <Pagination links={projects.meta.links} />

                        </div>
                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}