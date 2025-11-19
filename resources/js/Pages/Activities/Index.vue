<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'type', label: 'Type', sortable: true },
    { key: 'description', label: 'Description', sortable: false },
    { key: 'subject_type', label: 'Related To', sortable: false },
    { key: 'user', label: 'Created By', sortable: false },
    { key: 'created_at', label: 'Date', sortable: true },
];

const dataTableRef = ref(null);

const handleRowClick = (row) => {
    router.visit(route('activities.show', row.id));
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getSubjectTypeName = (type) => {
    if (!type) return '-';
    return type.split('\\').pop();
};
</script>

<template>
    <Head title="Activities" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Activities
                </h2>
                <Link
                    :href="route('activities.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                >
                    <PlusIcon class="h-5 w-5" />
                    New Activity
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
                            api-endpoint="/activities"
                            :per-page="15"
                            :searchable="true"
                            @row-click="handleRowClick"
                        >
                            <template #cell-type="{ row }">
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        row.type === 'Call' ? 'bg-blue-100 text-blue-800' :
                                        row.type === 'Meeting' ? 'bg-purple-100 text-purple-800' :
                                        row.type === 'Email' ? 'bg-green-100 text-green-800' :
                                        row.type === 'Note' ? 'bg-gray-100 text-gray-800' :
                                        'bg-yellow-100 text-yellow-800'
                                    ]"
                                >
                                    {{ row.type }}
                                </span>
                            </template>

                            <template #cell-description="{ row }">
                                <div class="max-w-xs truncate text-gray-700">
                                    {{ row.description }}
                                </div>
                            </template>

                            <template #cell-subject_type="{ row }">
                                <div class="text-sm">
                                    <div class="text-gray-500">{{ getSubjectTypeName(row.subject_type) }}</div>
                                    <div v-if="row.subject" class="text-gray-900">
                                        {{ row.subject.name || row.subject.title || row.subject.full_name || '-' }}
                                    </div>
                                </div>
                            </template>

                            <template #cell-user="{ row }">
                                <span v-if="row.user" class="text-gray-700">
                                    {{ row.user.name }}
                                </span>
                            </template>

                            <template #cell-created_at="{ row }">
                                {{ formatDate(row.created_at) }}
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
