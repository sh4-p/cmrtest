<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'title', label: 'Task', sortable: true },
    { key: 'priority', label: 'Priority', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'due_date', label: 'Due Date', sortable: true },
    { key: 'is_overdue', label: 'Overdue', sortable: false },
    { key: 'assigned_to', label: 'Assigned To', sortable: false },
];

const dataTableRef = ref(null);

const handleRowClick = (row) => {
    router.visit(route('tasks.show', row.id));
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Tasks
                </h2>
                <Link
                    :href="route('tasks.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                >
                    <PlusIcon class="h-5 w-5" />
                    New Task
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <DataTable
                            ref="dataTableRef"
                            :columns="columns"
                            api-endpoint="/tasks"
                            :per-page="15"
                            :searchable="true"
                            @row-click="handleRowClick"
                        >
                            <template #cell-title="{ row }">
                                <div class="font-medium text-gray-900">
                                    {{ row.title }}
                                </div>
                            </template>

                            <template #cell-priority="{ row }">
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        row.priority === 'High' ? 'bg-red-100 text-red-800' :
                                        row.priority === 'Medium' ? 'bg-yellow-100 text-yellow-800' :
                                        'bg-green-100 text-green-800'
                                    ]"
                                >
                                    {{ row.priority }}
                                </span>
                            </template>

                            <template #cell-status="{ row }">
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        row.status === 'Pending' ? 'bg-gray-100 text-gray-800' :
                                        row.status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
                                        row.status === 'Completed' ? 'bg-green-100 text-green-800' :
                                        'bg-red-100 text-red-800'
                                    ]"
                                >
                                    {{ row.status }}
                                </span>
                            </template>

                            <template #cell-due_date="{ row }">
                                <span :class="row.is_overdue ? 'text-red-600 font-semibold' : ''">
                                    {{ formatDate(row.due_date) }}
                                </span>
                            </template>

                            <template #cell-is_overdue="{ row }">
                                <span
                                    v-if="row.is_overdue"
                                    class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800"
                                >
                                    Overdue
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </template>

                            <template #cell-assigned_to="{ row }">
                                <span v-if="row.assigned_to" class="text-gray-700">
                                    {{ row.assigned_to.name }}
                                </span>
                                <span v-else class="text-gray-400 italic">
                                    Unassigned
                                </span>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
