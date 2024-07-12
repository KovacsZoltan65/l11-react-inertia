import { Link, router } from "@inertiajs/react";
import TableHeading from "@/Components/TableHeading";
import Pagination from "@/Components/Pagination";
import TextInput from "@/Components/TextInput";

import {TASK_STATUS_TEXT_MAP, TASK_STATUS_CLASS_MAP} from "@/constants.jsx";
import SelectInput from "@/Components/SelectInput";

export default function TasksTable({
    tasks, 
    queryParams = null, 
    hideProjectColumns = false 
}) {

    queryParams = queryParams || {};

    /**
     * Handles the change event of the search input field.
     * If the value is not empty, it adds the value to the queryParams object.
     * If the value is empty, it deletes the key from the queryParams object.
     * Finally, it makes a GET request to the task.index route with the updated queryParams.
     *
     * @param {string} name - The name of the field being searched.
     * @param {string} value - The value being searched.
     */
    const searchFieldChanged = (name, value) => {
        // If the value is not empty, add it to the queryParams object with the given name.
        if(value){
            queryParams[name] = value;
        }
        // If the value is empty, delete the key from the queryParams object.
        else{
            delete queryParams[name];
        }

        // Make a GET request to the task.index route with the updated queryParams.
        router.get(route("task.index", queryParams));
    };

    /**
     * Handles the change event of the sort field.
     * If the field is the same as the current sort field,
     * it toggles the sort direction. Otherwise, it sets
     * the sort field and direction to the new field.
     * Finally, it makes a GET request to the task.index route with the updated queryParams.
     *
     * @param {string} name - The name of the sort field.
     */
    const sortChanged = (name) => {
        // Save the current sort field and direction from the queryParams object.
        const currentSortField = queryParams.sort_field;
        const currentSortDirection = queryParams.sort_direction;

        // If the sort field is the same as the current sort field,
        // toggle the sort direction. Otherwise, set the sort field and direction to the new field.
        queryParams.sort_field = name;
        queryParams.sort_direction = currentSortField === name && currentSortDirection === 'asc' ? 'desc' : 'asc';

        // Make a GET request to the task.index route with the updated queryParams.
        router.get(route("task.index", queryParams));
    };

    /**
     * Handles the key press event for search fields.
     * If the pressed key is 'Enter', it calls the searchFieldChanged function.
     *
     * @param {string} name - The name of the field being searched.
     * @param {object} e - The key press event object.
     */
    const onKeyPress = (name, e) => {
        // If the pressed key is not 'Enter', do nothing.
        if(e.key !== 'Enter') return;

        // Call the searchFieldChanged function with the field name and target value.
        searchFieldChanged(name, e.target.value);
    };

    return(
        <>
            <div className="overflow-auto">
                <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b-2 border-gray-500">
                                            
                        <tr className="text-nowrap">

                            {/* ID */}
                            <TableHeading
                                name="id"
                                sort_field={queryParams.sort_field}
                                sort_direction={queryParams.sort_direction}
                                sortChanged={sortChanged}>ID</TableHeading>
                            
                            {/* Image */}
                            <th className="px-3 py-3">Image</th>

                            {/* Project Name */}
                            {!hideProjectColumns && (
                                <th className="px-3 py-3">Project Name</th>
                            )}

                            {/* Project Name */}
                            <TableHeading
                                name="name"
                                sort_field={queryParams.sort_field}
                                sort_direction={queryParams.sort_direction}
                                sortChanged={sortChanged}>Task Name</TableHeading>

                            {/* Status */}
                            <TableHeading
                                name="status"
                                sort_field={queryParams.sort_field}
                                sort_direction={queryParams.sort_direction}
                                sortChanged={sortChanged}>Status</TableHeading>

                            {/* Created Date */}
                            <TableHeading
                                name="created_at"
                                sort_field={queryParams.sort_field}
                                sort_direction={queryParams.sort_direction}
                                sortChanged={sortChanged}>Create Date</TableHeading>

                            {/* Due Date */}
                            <TableHeading
                                name="due_date"
                                sort_field={queryParams.sort_field}
                                sort_direction={queryParams.sort_direction}
                                sortChanged={sortChanged}>Due Date</TableHeading>

                            {/* Created By */}
                            <th className="px-3 py-3">Created By</th>

                            {/* Actions */}
                            <th className="px-3 py-3 text-right">Actions</th>

                        </tr>
                                            
                    </thead>
                    <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b-2 border-gray-500">
                        <tr className="text-nowrap">
                            <th className="px-3 py-3"></th>
                            <th className="px-3 py-3"></th>
                            
                            {!hideProjectColumns && <th className="px-3 py-3"></th>}

                            <th className="px-3 py-3">
                                <TextInput className="w-full" 
                                    defaultValue={queryParams.name}
                                    placeholder="Task Name"
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
                        {tasks.data.map((task) => (
                            <tr
                                className="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                key={task.id}
                            >
                                {/* ID */}
                                <td className="px-3 py-2">{task.id}</td>
                                {/* Image */}
                                <td className="px-3 py-2">
                                    <img src={task.image_path} style={{ width: 60 }} />
                                </td>
                                
                                {/* Project Name */}
                                {!hideProjectColumns && <td className="px-3 py-2">{task.project.name}</td>}

                                {/* Task Name */}
                                <th className="px-3 py-2 text-gray-100 text-nowrap hover:underline">
                                    <Link href={route("task.show", task.id)}>
                                        {task.name}
                                    </Link>
                                </th>

                                {/* Status */}
                                <td className="px-3 py-2">
                                    <span
                                        className={
                                            "px-2 py-1 rounded text-white " +
                                            TASK_STATUS_CLASS_MAP[task.status]
                                        }
                                    >
                                        {TASK_STATUS_TEXT_MAP[task.status]}
                                    </span>
                                </td>
                                
                                {/* Created Date */}
                                <td className="px-3 py-2 text-nowrap">
                                    {task.created_at}
                                </td>
                                {/* Due Date */}
                                <td className="px-3 py-2 text-nowrap">
                                    {task.due_date}
                                </td>
                                {/* Created By */}
                                <td className="px-3 py-2">{task.createdBy.name}</td>
                                {/* Actions */}
                                <td className="px-3 py-2 text-nowrap">
                                    <Link
                                        href={route("task.edit", task.id)}
                                        className="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-1"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        onClick={(e) => deleteTask(task)}
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
            <Pagination links={tasks.meta.links} />
        </>
    )
}