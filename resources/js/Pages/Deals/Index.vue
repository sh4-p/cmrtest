<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Deal Name', sortable: true },
    { key: 'amount', label: 'Amount', sortable: true },
    { key: 'probability', label: 'Probability', sortable: true },
    { key: 'deal_stage', label: 'Stage', sortable: false },
    { key: 'closing_date', label: 'Close Date', sortable: true },
    { key: 'assigned_to', label: 'Assigned To', sortable: false },
];

const dataTableRef = ref(null);

const handleRowClick = (row) => {
    router.visit(route('deals.show', row.id));
};

const formatCurrency = (value) => {
    if (!value) return '-';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
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
    <Head title="Deals" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Deals
                </h2>
                <Link
                    :href="route('deals.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                >
                    <PlusIcon class="h-5 w-5" />
                    New Deal
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
                            api-endpoint="/deals"
                            :per-page="15"
                            :searchable="true"
                            @row-click="handleRowClick"
                        >
                            <template #cell-name="{ row }">
                                <div class="font-medium text-gray-900">
                                    {{ row.name }}
                                </div>
                            </template>

                            <template #cell-amount="{ row }">
                                <span class="font-semibold text-green-600">
                                    {{ formatCurrency(row.amount) }}
                                </span>
                            </template>

                            <template #cell-probability="{ row }">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-16 bg-gray-200 rounded-full overflow-hidden">
                                        <div
                                            :style="{ width: `${row.probability}%` }"
                                            :class="[
                                                'h-full',
                                                row.probability >= 75 ? 'bg-green-500' :
                                                row.probability >= 50 ? 'bg-yellow-500' :
                                                row.probability >= 25 ? 'bg-orange-500' :
                                                'bg-red-500'
                                            ]"
                                        ></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ row.probability }}%</span>
                                </div>
                            </template>

                            <template #cell-deal_stage="{ row }">
                                <span
                                    v-if="row.deal_stage"
                                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                    :style="{
                                        backgroundColor: row.deal_stage.color + '20',
                                        color: row.deal_stage.color,
                                    }"
                                >
                                    {{ row.deal_stage.name }}
                                </span>
                            </template>

                            <template #cell-closing_date="{ row }">
                                {{ formatDate(row.closing_date) }}
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
