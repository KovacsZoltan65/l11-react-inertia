import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import TextAreaInput from "@/Components/TextAreaInput";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import SelectInput from "@/Components/SelectInput";

export default function Create({ auth }) {
    /**
     * The form data object.
     * @type {Object}
     * @property {string} image - A képfájl
     * @property {string} name - A projekt neve.
     * @property {string} status - A projekt állapota
     * @property {string} description - A projekt leírása.
     * @property {string} due_date - A projekt határideje
     */
    const { data, setData, post, errors, reset } = useForm({
        // A képfájl
        image: '',
        // A projekt neve.
        name: '',
        // A projekt állapota
        status: '',
        // A projekt leírása.
        description: '',
        // A projekt határideje
        due_date: '',
    });

    /**
     * Kezeli az űrlap beküldési eseményét.
     *
     * @param {Event} e - The form submission event.
     * @return {void}
     */
    const onSubmit = (e) => {
        // Az alapértelmezett űrlapbeküldési viselkedés megakadályozása
        e.preventDefault();

        // Küldje el az űrlapadatokat a szervernek post módszerrel a "project.store" útvonalon
        post(route('project.store'));
    };

    return (
        <AuthenticatedLayout user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >Create new Project</h2>
            }>
            <Head title="Create new Project" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={onSubmit}
                                className="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">

                                {/* IMAGE */}
                                <div>
                                    <InputLabel
                                        htmlFor="project_image_path"
                                        value="Project Image"
                                    />
                                    <TextInput
                                        id="project_image_path"
                                        type="file"
                                        name="image"
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData("image", e.target.files[0])}
                                    />
                                    <InputError message={errors.image} className="mt-2" />
                                </div>

                                {/* NAME */}
                                <div className="mt-4">
                                    <InputLabel htmlFor="project_name"
                                        value="Project Name" />
                                    <TextInput id="name" name="name"
                                        type="text" value={data.name}
                                        className="mt-1 block w-full"
                                        isFocused={true}
                                        onChange={(e) => setData('name', e.target.value)} />
                                    <InputError message={errors.name} className="mt-2" />
                                </div>

                                {/* DESCRIPTION */}
                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="project_description"
                                        value="Project Description"
                                    />

                                    <TextAreaInput
                                        id="project_description"
                                        name="description"
                                        value={data.description}
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData("description", e.target.value)} />

                                    <InputError message={errors.description} className="mt-2" />
                                </div>

                                {/* DUE DATE */}
                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="project_due_date"
                                        value="Project Deadline"
                                    />

                                    <TextInput
                                        id="project_due_date"
                                        type="date"
                                        name="due_date"
                                        value={data.due_date}
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData("due_date", e.target.value)}
                                    />

                                    <InputError message={errors.due_date} className="mt-2" />
                                </div>

                                {/* STATUS */}
                                <div className="mt-4">
                                    <InputLabel htmlFor="project_status" value="Project Status" />

                                    <SelectInput
                                        name="status"
                                        id="project_status"
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData("status", e.target.value)}
                                    >
                                        <option value="">Select Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </SelectInput>

                                    <InputError message={errors.project_status} className="mt-2" />
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
