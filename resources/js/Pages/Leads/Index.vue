<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'full_name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'phone', label: 'Phone', sortable: false },
    { key: 'company', label: 'Company', sortable: false },
    { key: 'source', label: 'Source', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'assigned_to', label: 'Assigned To', sortable: false },
];

const dataTableRef = ref(null);

const handleRowClick = (row) => {
    router.visit(route('leads.show', row.id));
};

const handleCreate = () => {
    router.visit(route('leads.create'));
};
</script>

<template>
    <Head title="Leads" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Leads
                </h2>
                <Link
                    :href="route('leads.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                >
                    <PlusIcon class="h-5 w-5" />
                    New Lead
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
                            api-endpoint="/leads"
                            :per-page="15"
                            :searchable="true"
                            @row-click="handleRowClick"
                        >
                            <template #cell-full_name="{ row }">
                                <div class="font-medium text-gray-900">
                                    {{ row.full_name }}
                                </div>
                            </template>

                            <template #cell-status="{ row }">
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        row.status === 'New' ? 'bg-blue-100 text-blue-800' :
                                        row.status === 'Contacted' ? 'bg-yellow-100 text-yellow-800' :
                                        row.status === 'Qualified' ? 'bg-green-100 text-green-800' :
                                        row.status === 'Lost' ? 'bg-red-100 text-red-800' :
                                        row.status === 'Converted' ? 'bg-purple-100 text-purple-800' :
                                        'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ row.status }}
                                </span>
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
