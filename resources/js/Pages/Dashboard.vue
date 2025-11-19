<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useApi } from '@/composables/useApi';
import KpiCard from '@/Components/KpiCard.vue';
import RecentActivities from '@/Components/RecentActivities.vue';
import UpcomingTasks from '@/Components/UpcomingTasks.vue';
import {
    UserGroupIcon,
    UsersIcon,
    BuildingOfficeIcon,
    CurrencyDollarIcon,
    ClipboardDocumentListIcon,
    CheckCircleIcon,
    TrendingUpIcon,
    BanknotesIcon,
} from '@heroicons/vue/24/outline';

const { get, loading } = useApi();
const stats = ref(null);

const kpiCards = ref([
    {
        title: 'Total Leads',
        value: '0',
        icon: UserGroupIcon,
        color: 'blue',
        change: null,
    },
    {
        title: 'Active Deals',
        value: '0',
        icon: CurrencyDollarIcon,
        color: 'green',
        change: null,
    },
    {
        title: 'Total Contacts',
        value: '0',
        icon: UsersIcon,
        color: 'purple',
        change: null,
    },
    {
        title: 'Pending Tasks',
        value: '0',
        icon: ClipboardDocumentListIcon,
        color: 'yellow',
        change: null,
    },
]);

const fetchDashboardStats = async () => {
    try {
        const response = await get('/dashboard/stats');
        stats.value = response;

        // Update KPI cards with real data
        kpiCards.value[0].value = response.leads?.total || 0;
        kpiCards.value[0].change = calculateChange(response.leads?.new || 0, response.leads?.total || 0);

        kpiCards.value[1].value = response.deals?.active || 0;
        kpiCards.value[1].change = calculateChange(response.deals?.active || 0, response.deals?.total || 0);

        kpiCards.value[2].value = response.contacts?.total || 0;

        kpiCards.value[3].value = response.tasks?.pending || 0;
        kpiCards.value[3].change = calculateChange(response.tasks?.pending || 0, response.tasks?.total || 0);
    } catch (error) {
        console.error('Error fetching dashboard stats:', error);
    }
};

const calculateChange = (current, total) => {
    if (total === 0) return 0;
    return Math.round((current / total) * 100);
};

onMounted(() => {
    fetchDashboardStats();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center py-12">
                    <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                </div>

                <div v-else class="space-y-6">
                    <!-- KPI Cards Grid -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <KpiCard
                            v-for="(card, index) in kpiCards"
                            :key="index"
                            :title="card.title"
                            :value="card.value"
                            :icon="card.icon"
                            :color="card.color"
                            :change="card.change"
                        />
                    </div>

                    <!-- Main Content Grid -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Left Column (2/3 width) -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Revenue & Deal Stats Card -->
                            <div class="overflow-hidden rounded-lg bg-white shadow">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                        Performance Overview
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded">
                                            <div class="flex items-center">
                                                <BanknotesIcon class="h-8 w-8 text-green-600" />
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-green-800">Total Deal Value</p>
                                                    <p class="text-2xl font-bold text-green-900">
                                                        ${{ (stats?.deals?.total_value || 0).toLocaleString() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded">
                                            <div class="flex items-center">
                                                <UserGroupIcon class="h-8 w-8 text-blue-600" />
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-blue-800">New Leads</p>
                                                    <p class="text-2xl font-bold text-blue-900">
                                                        {{ stats?.leads?.new || 0 }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border-l-4 border-purple-500 bg-purple-50 p-4 rounded">
                                            <div class="flex items-center">
                                                <TrendingUpIcon class="h-8 w-8 text-purple-600" />
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-purple-800">Converted Leads</p>
                                                    <p class="text-2xl font-bold text-purple-900">
                                                        {{ stats?.leads?.converted || 0 }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activities -->
                            <RecentActivities />
                        </div>

                        <!-- Right Column (1/3 width) -->
                        <div class="lg:col-span-1">
                            <!-- Upcoming Tasks -->
                            <UpcomingTasks />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
