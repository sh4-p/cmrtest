<script setup>
import { ref, computed, onMounted } from 'vue';
import { useApi } from '@/composables/useApi';
import { CheckCircleIcon, ClockIcon } from '@heroicons/vue/24/outline';

const { get, patch, loading } = useApi();
const tasks = ref([]);

const groupedTasks = computed(() => {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const thisWeek = new Date(today);
    thisWeek.setDate(thisWeek.getDate() + 7);

    const groups = {
        today: [],
        tomorrow: [],
        thisWeek: [],
    };

    tasks.value.forEach(task => {
        const dueDate = new Date(task.due_date);
        const dueDateOnly = new Date(dueDate.getFullYear(), dueDate.getMonth(), dueDate.getDate());

        if (dueDateOnly.getTime() === today.getTime()) {
            groups.today.push(task);
        } else if (dueDateOnly.getTime() === tomorrow.getTime()) {
            groups.tomorrow.push(task);
        } else if (dueDateOnly < thisWeek) {
            groups.thisWeek.push(task);
        }
    });

    return groups;
});

const getPriorityColor = (priority) => {
    const colors = {
        'Urgent': 'text-red-700 bg-red-50 border-red-200',
        'High': 'text-orange-700 bg-orange-50 border-orange-200',
        'Medium': 'text-yellow-700 bg-yellow-50 border-yellow-200',
        'Low': 'text-green-700 bg-green-50 border-green-200',
    };
    return colors[priority] || 'text-gray-700 bg-gray-50 border-gray-200';
};

const fetchTasks = async () => {
    try {
        const params = {
            status: 'Pending,In Progress',
            due_date: 'upcoming',
            per_page: 20,
        };
        const response = await get('/tasks', { params });
        tasks.value = response.data || [];
    } catch (error) {
        console.error('Error fetching tasks:', error);
    }
};

const markAsCompleted = async (taskId) => {
    try {
        await patch(`/tasks/${taskId}/complete`);
        // Remove the task from the list
        tasks.value = tasks.value.filter(t => t.id !== taskId);
    } catch (error) {
        console.error('Error completing task:', error);
    }
};

onMounted(() => {
    fetchTasks();
});

defineExpose({
    refresh: fetchTasks,
});
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
                Upcoming Tasks
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Tasks due in the next 7 days
            </p>
        </div>
        <div class="border-t border-gray-200">
            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center py-12">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
            </div>

            <!-- Empty State -->
            <div v-else-if="tasks.length === 0" class="px-4 py-8 text-center">
                <ClockIcon class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-2 text-sm text-gray-500">No upcoming tasks</p>
            </div>

            <!-- Tasks List -->
            <div v-else class="divide-y divide-gray-200">
                <!-- Today -->
                <div v-if="groupedTasks.today.length > 0" class="px-4 py-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Today
                    </h4>
                    <ul class="mt-2 space-y-2">
                        <li
                            v-for="task in groupedTasks.today"
                            :key="task.id"
                            :class="['rounded-md border p-3 transition hover:shadow-md', getPriorityColor(task.priority)]"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium">
                                        {{ task.title }}
                                    </p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-xs">
                                            {{ task.priority }}
                                        </span>
                                        <span v-if="task.assigned_to" class="text-xs text-gray-600">
                                            • {{ task.assigned_to.name }}
                                        </span>
                                    </div>
                                </div>
                                <button
                                    @click="markAsCompleted(task.id)"
                                    class="ml-2 rounded-full p-1 hover:bg-white/50"
                                    title="Mark as completed"
                                >
                                    <CheckCircleIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Tomorrow -->
                <div v-if="groupedTasks.tomorrow.length > 0" class="px-4 py-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Tomorrow
                    </h4>
                    <ul class="mt-2 space-y-2">
                        <li
                            v-for="task in groupedTasks.tomorrow"
                            :key="task.id"
                            :class="['rounded-md border p-3 transition hover:shadow-md', getPriorityColor(task.priority)]"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium">
                                        {{ task.title }}
                                    </p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-xs">
                                            {{ task.priority }}
                                        </span>
                                        <span v-if="task.assigned_to" class="text-xs text-gray-600">
                                            • {{ task.assigned_to.name }}
                                        </span>
                                    </div>
                                </div>
                                <button
                                    @click="markAsCompleted(task.id)"
                                    class="ml-2 rounded-full p-1 hover:bg-white/50"
                                    title="Mark as completed"
                                >
                                    <CheckCircleIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- This Week -->
                <div v-if="groupedTasks.thisWeek.length > 0" class="px-4 py-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        This Week
                    </h4>
                    <ul class="mt-2 space-y-2">
                        <li
                            v-for="task in groupedTasks.thisWeek"
                            :key="task.id"
                            :class="['rounded-md border p-3 transition hover:shadow-md', getPriorityColor(task.priority)]"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium">
                                        {{ task.title }}
                                    </p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-xs">
                                            {{ task.priority }}
                                        </span>
                                        <span v-if="task.assigned_to" class="text-xs text-gray-600">
                                            • {{ task.assigned_to.name }}
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            • {{ new Date(task.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
                                        </span>
                                    </div>
                                </div>
                                <button
                                    @click="markAsCompleted(task.id)"
                                    class="ml-2 rounded-full p-1 hover:bg-white/50"
                                    title="Mark as completed"
                                >
                                    <CheckCircleIcon class="h-5 w-5" />
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
