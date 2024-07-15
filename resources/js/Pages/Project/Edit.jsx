import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import TextAreaInput from "@/Components/TextAreaInput";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import SelectInput from "@/Components/SelectInput";

export default function Edit({ auth, project }) {
    /**
     * The form data object.
     * @type {Object}
     * @property {string} image - The image file.
     * @property {string} image_path - The image path.
     * @property {string} name - The project name.
     * @property {string} status - The project status.
     * @property {string} description - The project description.
     * @property {string} due_date - The project due date.
     * @property {string} _method - The HTTP method for the form submission.
     */
    const { data, setData, post, errors, reset } = useForm({
        // The image file
        image: '',
        // The image path
        image_path: project.image_path || '',
        // The project name
        name: project.name || '',
        // The project status
        status: project.status || '',
        // The project description
        description: project.description || '',
        // The project due date
        due_date: project.due_date || '',
        // The HTTP method for the form submission
        _method: 'PUT',
    });

    /**
     * Handle the form submission.
     *
     * @param {React.FormEvent} e - The form submission event.
     * @returns {void}
     */
    const onSubmit = (e) => {
        e.preventDefault();

        // Send the form data to the server.
        post(route('project.update', project.id));
    };

    return (
        <AuthenticatedLayout user={auth.user}
            /**
             * Jelenítse meg az oldal fejlécét.
             *
             * @return {React.ReactElement} Az oldalfejléc összetevő.
             */
            header={
                // Jelenítse meg az oldal fejlécét a projekt nevével.
                <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {/* A fejléc szövege */}
                    Edit Project "{project.name}"
                </h2>
            }>
            {/**
              * Jelenítse meg az oldal címét.
              *
              * @return {React.ReactElement} Az oldal cím összetevője.
              */}
            <Head title="Edit Project" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={onSubmit}
                                className="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                {/*
                                  * Ha a projektnek van képe, jelenítse meg.
                                  */}
                            {project.image_path && (
                                <div className="mb-4">
                                    <img src={project.image_path} className="w-64" />
                                </div>
                            )}

                                {/*
                                  * Ez az űrlap azon része, amely a projekt képével foglalkozik.
                                  *
                                  * Magába foglalja:
                                  * - Címke a projekt képéhez
                                  * - Szövegbevitel a projekt képéhez ("fájl" típusú)
                                  * - Bemeneti hibaüzenet, ha bármilyen hiba van
                                  */}
                                <div>
                                    {/* Label for the project image */}
                                    <InputLabel
                                        htmlFor="project_image_path"
                                        value="Project Image"
                                    />

                                    {/* Text input for the project image */}
                                    <TextInput
                                        id="project_image_path"
                                        type="file"
                                        name="image"
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData("image", e.target.files[0])}
                                    />

                                    {/* Beviteli hibaüzenet */}
                                    <InputError message={errors.image} className="mt-2" />
                                </div>

                                {/*
                                  * Ez az űrlap azon része, amely a projekt nevével foglalkozik.
                                  *
                                  * Magába foglalja:
                                  * - Címke a projekt nevéhez
                                  * - Szövegbevitel a projekt nevéhez
                                  * - Bemeneti hibaüzenet, ha bármilyen hiba van
                                  */}
                                <div className="mt-4">
                                    {/* Label for the project name */}
                                    <InputLabel htmlFor="project_name"
                                        value="Project Name" />

                                    {/* Text input for the project name */}
                                    <TextInput id="name" name="name"
                                        type="text" value={data.name}
                                        className="mt-1 block w-full"
                                        isFocused={true}
                                        onChange={(e) => setData('name', e.target.value)} />

                                    {/* Input error message */}
                                    <InputError message={errors.name} className="mt-2" />
                                </div>

                                {/**
                                * This is the section of the form that deals with the project description.
                                *
                                * It includes:
                                * - A label for the project description
                                * - A text area for the project description
                                * - An input error message if there are any errors with the project description
                                */}
                                <div className="mt-4">
                                    {/* Label for the project description */}
                                    <InputLabel
                                        htmlFor="project_description"
                                        value="Project Description"
                                    />

                                    {/* Input field for the project description */}
                                    <TextAreaInput
                                        id="project_description"
                                        name="description"
                                        value={data.description}
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData("description", e.target.value)}
                                    />

                                    {/* Input error message for the project description */}
                                    <InputError message={errors.description} className="mt-2" />
                                </div>

                                {/* Project Deadline */}
                                <div className="mt-4">
                                    {/* Label for the input */}
                                    <InputLabel
                                        htmlFor="project_due_date"
                                        value="Project Deadline"
                                    />

                                    {/* Input field for the due date */}
                                    {/* The type attribute is set to "date" to display a date picker */}
                                    <TextInput
                                        id="project_due_date"
                                        type="date"
                                        name="due_date"
                                        value={data.due_date}
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData("due_date", e.target.value)}
                                    />

                                    {/* Error message for the due date */}
                                    <InputError message={errors.due_date} className="mt-2" />
                                </div>

                                {/* Select input for project status */}
                                <div className="mt-4">
                                    {/* Label for the select input */}
                                    <InputLabel htmlFor="project_status" value="Project Status" />

                                    {/* Select input element */}
                                    <SelectInput
                                        name="status"
                                        id="project_status"
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData("status", e.target.value)}
                                    >
                                        {/* Default option */}
                                        <option value="">Select Status</option>

                                        {/* Options for project status */}

                                        {/* Option for "pending" status */}
                                        <option value="pending">Pending</option>

                                        {/* Option for "in_progress" status */}
                                        <option value="in_progress">In Progress</option>

                                        {/* Option for "completed" status */}
                                        <option value="completed">Completed</option>
                                    </SelectInput>

                                    {/* Error message for the select input */}
                                    <InputError
                                        message={errors.project_status}
                                        className="mt-2"
                                    />
                                </div>

                                {/* Elküldés és Mégse gombok */}
                                <div className="mt-4 text-right">
                                    {/* Mégse gomb */}
                                    {/* Visszaküldi a felhasználót a projekt indexoldalára */}
                                    <Link
                                        href={route("project.index")}
                                        className="bg-gray-100 py-1 px-3 text-gray-800 rounded shadow transition-all hover:bg-gray-200 mr-2"
                                    >
                                        Cancel
                                    </Link>

                                    {/* Küldés gomb */}
                                    {/* Elküldi az űrlap adatait a szervernek */}
                                    <button className="bg-emerald-500 py-1 px-3 text-white rounded shadow transition-all hover:bg-emerald-600">
                                        Submit
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </AuthenticatedLayout>
    );
}
