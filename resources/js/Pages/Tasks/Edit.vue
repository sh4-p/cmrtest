<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useApi } from '@/composables/useApi';

const props = defineProps({
    taskId: {
        type: [String, Number],
        required: true,
    },
    users: {
        type: Array,
        default: () => [],
    },
});

const { get, put, loading, error } = useApi();

const task = ref(null);
const form = ref({
    title: '',
    description: '',
    status: 'Pending',
    priority: 'Medium',
    due_date: '',
    assigned_to_id: null,
});

const statusOptions = ['Pending', 'In Progress', 'Completed'];
const priorityOptions = ['Low', 'Medium', 'High'];

const errors = ref({});

const fetchTask = async () => {
    try {
        const response = await get(`/tasks/${props.taskId}`);
        task.value = response;

        // Populate form
        form.value = {
            title: response.title || '',
            description: response.description || '',
            status: response.status || 'Pending',
            priority: response.priority || 'Medium',
            due_date: response.due_date ? response.due_date.slice(0, 16) : '',
            assigned_to_id: response.assigned_to_id || null,
        };
    } catch (err) {
        console.error('Error fetching task:', err);
    }
};

const handleSubmit = async () => {
    errors.value = {};

    try {
        await put(`/tasks/${props.taskId}`, form.value);
        router.visit(route('tasks.show', props.taskId));
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
        }
    }
};

const handleCancel = () => {
    router.visit(route('tasks.show', props.taskId));
};

onMounted(() => {
    fetchTask();
});
</script>

<template>
    <Head title="Edit Task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Task
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{ 'border-red-500': errors.title }"
                            />
                            <p v-if="errors.title" class="mt-1 text-sm text-red-600">
                                {{ errors.title[0] }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                        </div>

                        <!-- Status & Priority -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option v-for="option in statusOptions" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">
                                    Priority
                                </label>
                                <select
                                    id="priority"
                                    v-model="form.priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option v-for="option in priorityOptions" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Due Date & Assigned To -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">
                                    Due Date
                                </label>
                                <input
                                    id="due_date"
                                    v-model="form.due_date"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label for="assigned_to_id" class="block text-sm font-medium text-gray-700">
                                    Assigned To
                                </label>
                                <select
                                    id="assigned_to_id"
                                    v-model="form.assigned_to_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option :value="null">Unassigned</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="error" class="rounded-md bg-red-50 p-4">
                            <p class="text-sm text-red-800">{{ error }}</p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 border-t pt-4">
                            <button
                                type="button"
                                @click="handleCancel"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                                :disabled="loading"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 disabled:opacity-50"
                                :disabled="loading"
                            >
                                <div v-if="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                Update Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
