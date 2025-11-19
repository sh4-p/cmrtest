<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useApi } from '@/composables/useApi';
import {
    PencilIcon,
    TrashIcon,
    UserIcon,
    CalendarIcon,
    ClockIcon,
    FlagIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    taskId: {
        type: [String, Number],
        required: true,
    },
});

const { get, destroy, loading } = useApi();
const task = ref(null);
const showDeleteModal = ref(false);

const fetchTask = async () => {
    try {
        const response = await get(`/tasks/${props.taskId}`);
        task.value = response;
    } catch (error) {
        console.error('Error fetching task:', error);
    }
};

const handleEdit = () => {
    router.visit(route('tasks.edit', props.taskId));
};

const handleDelete = async () => {
    try {
        await destroy(`/tasks/${props.taskId}`);
        router.visit(route('tasks.index'));
    } catch (error) {
        console.error('Error deleting task:', error);
    }
};

const getStatusColor = (status) => {
    const colors = {
        'Pending': 'bg-yellow-100 text-yellow-800',
        'In Progress': 'bg-blue-100 text-blue-800',
        'Completed': 'bg-green-100 text-green-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getPriorityColor = (priority) => {
    const colors = {
        'Low': 'bg-gray-100 text-gray-800',
        'Medium': 'bg-blue-100 text-blue-800',
        'High': 'bg-red-100 text-red-800',
    };
    return colors[priority] || 'bg-gray-100 text-gray-800';
};

const isOverdue = computed(() => {
    if (!task.value || !task.value.due_date || task.value.status === 'Completed') {
        return false;
    }
    return new Date(task.value.due_date) < new Date();
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

onMounted(() => {
    fetchTask();
});
</script>

<template>
    <Head title="Task Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Task Details
                </h2>
                <div class="flex gap-3">
                    <button
                        @click="handleEdit"
                        class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                    >
                        <PencilIcon class="h-4 w-4" />
                        Edit
                    </button>
                    <button
                        @click="showDeleteModal = true"
                        class="inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                    >
                        <TrashIcon class="h-4 w-4" />
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center py-12">
                    <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                </div>

                <!-- Task Details -->
                <div v-else-if="task" class="space-y-6">
                    <!-- Header Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900">
                                        {{ task.title }}
                                    </h3>
                                    <div class="mt-2 flex items-center gap-2 flex-wrap">
                                        <span :class="['inline-flex rounded-full px-3 py-1 text-sm font-semibold', getStatusColor(task.status)]">
                                            {{ task.status }}
                                        </span>
                                        <span :class="['inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-semibold', getPriorityColor(task.priority)]">
                                            <FlagIcon class="h-4 w-4" />
                                            {{ task.priority }} Priority
                                        </span>
                                        <span v-if="isOverdue" class="inline-flex rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">
                                            Overdue
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Information -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h4 class="text-lg font-semibold text-gray-900">Task Information</h4>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="flex items-start gap-3">
                                    <CalendarIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900" :class="{ 'text-red-600 font-semibold': isOverdue }">
                                            {{ formatDate(task.due_date) }}
                                        </dd>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <UserIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ task.assigned_to?.name || 'Unassigned' }}
                                        </dd>
                                    </div>
                                </div>

                                <div v-if="task.completed_at" class="flex items-start gap-3 sm:col-span-2">
                                    <ClockIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Completed At</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ formatDate(task.completed_at) }}
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h4 class="text-lg font-semibold text-gray-900">Description</h4>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">
                                {{ task.description || 'No description available' }}
                            </p>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ formatDate(task.created_at) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ formatDate(task.updated_at) }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" @click="showDeleteModal = false">
            <div class="flex min-h-screen items-center justify-center px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full" @click.stop>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Delete Task
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Are you sure you want to delete this task? This action cannot be undone.
                        </p>
                        <div class="flex justify-end gap-3">
                            <button
                                @click="showDeleteModal = false"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="handleDelete"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
