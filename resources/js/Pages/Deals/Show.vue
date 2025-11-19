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
    CurrencyDollarIcon,
    ChartBarIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    dealId: {
        type: [String, Number],
        required: true,
    },
});

const { get, destroy, loading } = useApi();
const deal = ref(null);
const showDeleteModal = ref(false);

const fetchDeal = async () => {
    try {
        const response = await get(`/deals/${props.dealId}`);
        deal.value = response;
    } catch (error) {
        console.error('Error fetching deal:', error);
    }
};

const handleEdit = () => {
    router.visit(route('deals.edit', props.dealId));
};

const handleDelete = async () => {
    try {
        await destroy(`/deals/${props.dealId}`);
        router.visit(route('deals.index'));
    } catch (error) {
        console.error('Error deleting deal:', error);
    }
};

const expectedRevenue = computed(() => {
    if (!deal.value) return 0;
    return (deal.value.amount * (deal.value.probability / 100)).toFixed(2);
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

onMounted(() => {
    fetchDeal();
});
</script>

<template>
    <Head title="Deal Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Deal Details
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

                <!-- Deal Details -->
                <div v-else-if="deal" class="space-y-6">
                    <!-- Header Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900">
                                        {{ deal.name }}
                                    </h3>
                                    <div class="mt-2 flex items-center gap-2 flex-wrap">
                                        <span 
                                            v-if="deal.stage"
                                            class="inline-flex rounded-full px-3 py-1 text-sm font-semibold text-white"
                                            :style="{ backgroundColor: deal.stage.color }"
                                        >
                                            {{ deal.stage.name }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">
                                            <CurrencyDollarIcon class="h-4 w-4" />
                                            {{ formatCurrency(deal.amount) }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">
                                            <ChartBarIcon class="h-4 w-4" />
                                            {{ deal.probability }}% Probability
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deal Information -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h4 class="text-lg font-semibold text-gray-900">Deal Information</h4>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ deal.contact ? `${deal.contact.first_name} ${deal.contact.last_name}` : '-' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Expected Revenue</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(expectedRevenue) }}
                                    </dd>
                                </div>

                                <div class="flex items-start gap-3">
                                    <CalendarIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Expected Closing Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ formatDate(deal.closing_date) }}
                                        </dd>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <UserIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ deal.assigned_to?.name || 'Unassigned' }}
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
                                {{ deal.description || 'No description available' }}
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
                                        {{ formatDate(deal.created_at) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ formatDate(deal.updated_at) }}
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
                            Delete Deal
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Are you sure you want to delete this deal? This action cannot be undone.
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
