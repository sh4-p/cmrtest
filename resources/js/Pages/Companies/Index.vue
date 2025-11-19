<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'phone', label: 'Phone', sortable: false },
    { key: 'website', label: 'Website', sortable: false },
    { key: 'industry', label: 'Industry', sortable: true },
    { key: 'owner', label: 'Owner', sortable: false },
];

const dataTableRef = ref(null);

const handleRowClick = (row) => {
    router.visit(route('companies.show', row.id));
};
</script>

<template>
    <Head title="Companies" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Companies
                </h2>
                <Link
                    :href="route('companies.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                >
                    <PlusIcon class="h-5 w-5" />
                    New Company
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
                            api-endpoint="/companies"
                            :per-page="15"
                            :searchable="true"
                            @row-click="handleRowClick"
                        >
                            <template #cell-name="{ row }">
                                <div class="font-medium text-gray-900">
                                    {{ row.name }}
                                </div>
                            </template>

                            <template #cell-website="{ row }">
                                <a
                                    v-if="row.website"
                                    :href="row.website"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-500"
                                    @click.stop
                                >
                                    {{ row.website }}
                                </a>
                                <span v-else class="text-gray-400 italic">
                                    No website
                                </span>
                            </template>

                            <template #cell-owner="{ row }">
                                <span v-if="row.owner" class="text-gray-700">
                                    {{ row.owner.name }}
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
