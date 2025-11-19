<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useApi } from '@/composables/useApi';

const props = defineProps({
    dealId: {
        type: [String, Number],
        required: true,
    },
    users: {
        type: Array,
        default: () => [],
    },
    contacts: {
        type: Array,
        default: () => [],
    },
    stages: {
        type: Array,
        default: () => [],
    },
});

const { get, put, loading, error } = useApi();

const deal = ref(null);
const form = ref({
    name: '',
    contact_id: null,
    deal_stage_id: null,
    amount: '',
    closing_date: '',
    probability: 50,
    assigned_to_id: null,
    description: '',
});

const errors = ref({});

const fetchDeal = async () => {
    try {
        const response = await get(`/deals/${props.dealId}`);
        deal.value = response;

        // Populate form
        form.value = {
            name: response.name || '',
            contact_id: response.contact_id || null,
            deal_stage_id: response.deal_stage_id || null,
            amount: response.amount || '',
            closing_date: response.closing_date || '',
            probability: response.probability || 50,
            assigned_to_id: response.assigned_to_id || null,
            description: response.description || '',
        };
    } catch (err) {
        console.error('Error fetching deal:', err);
    }
};

const handleSubmit = async () => {
    errors.value = {};

    try {
        await put(`/deals/${props.dealId}`, form.value);
        router.visit(route('deals.show', props.dealId));
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
        }
    }
};

const handleCancel = () => {
    router.visit(route('deals.show', props.dealId));
};

onMounted(() => {
    fetchDeal();
});
</script>

<template>
    <Head title="Edit Deal" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Deal
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
                        <!-- Deal Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Deal Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{ 'border-red-500': errors.name }"
                            />
                            <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                                {{ errors.name[0] }}
                            </p>
                        </div>

                        <!-- Contact & Stage -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="contact_id" class="block text-sm font-medium text-gray-700">
                                    Contact <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="contact_id"
                                    v-model="form.contact_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.contact_id }"
                                >
                                    <option :value="null">Select a contact</option>
                                    <option v-for="contact in contacts" :key="contact.id" :value="contact.id">
                                        {{ contact.first_name }} {{ contact.last_name }}
                                    </option>
                                </select>
                                <p v-if="errors.contact_id" class="mt-1 text-sm text-red-600">
                                    {{ errors.contact_id[0] }}
                                </p>
                            </div>

                            <div>
                                <label for="deal_stage_id" class="block text-sm font-medium text-gray-700">
                                    Stage <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="deal_stage_id"
                                    v-model="form.deal_stage_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.deal_stage_id }"
                                >
                                    <option v-for="stage in stages" :key="stage.id" :value="stage.id">
                                        {{ stage.name }}
                                    </option>
                                </select>
                                <p v-if="errors.deal_stage_id" class="mt-1 text-sm text-red-600">
                                    {{ errors.deal_stage_id[0] }}
                                </p>
                            </div>
                        </div>

                        <!-- Amount & Probability -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">
                                    Amount ($) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="amount"
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.amount }"
                                />
                                <p v-if="errors.amount" class="mt-1 text-sm text-red-600">
                                    {{ errors.amount[0] }}
                                </p>
                            </div>

                            <div>
                                <label for="probability" class="block text-sm font-medium text-gray-700">
                                    Probability (%) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="probability"
                                    v-model="form.probability"
                                    type="number"
                                    min="0"
                                    max="100"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.probability }"
                                />
                                <p v-if="errors.probability" class="mt-1 text-sm text-red-600">
                                    {{ errors.probability[0] }}
                                </p>
                            </div>
                        </div>

                        <!-- Closing Date & Assigned To -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="closing_date" class="block text-sm font-medium text-gray-700">
                                    Expected Closing Date
                                </label>
                                <input
                                    id="closing_date"
                                    v-model="form.closing_date"
                                    type="date"
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
                                Update Deal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
