<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useApi } from '@/composables/useApi';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const { post, loading, error } = useApi();

const form = ref({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    company: '',
    job_title: '',
    source: 'Website',
    status: 'New',
    assigned_to_id: null,
    notes: '',
});

const sourceOptions = ['Website', 'Referral', 'Social Media', 'Email Campaign', 'Trade Show', 'Cold Call', 'Other'];
const statusOptions = ['New', 'Contacted', 'Qualified', 'Lost', 'Converted'];

const errors = ref({});

const handleSubmit = async () => {
    errors.value = {};

    try {
        const response = await post('/contacts', form.value);
        router.visit(route('contacts.show', response.id));
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
        }
    }
};

const handleCancel = () => {
    router.visit(route('contacts.index'));
};
</script>

<template>
    <Head title="Create Contact" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold contacting-tight text-gray-800">
                Create Contact
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
                        <!-- Name Section -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.first_name }"
                                />
                                <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">
                                    {{ errors.first_name[0] }}
                                </p>
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.last_name }"
                                />
                                <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">
                                    {{ errors.last_name[0] }}
                                </p>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.email }"
                                />
                                <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                                    {{ errors.email[0] }}
                                </p>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Phone
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <!-- Company & Job Title -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700">
                                    Company
                                </label>
                                <input
                                    id="company"
                                    v-model="form.company"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label for="job_title" class="block text-sm font-medium text-gray-700">
                                    Job Title
                                </label>
                                <input
                                    id="job_title"
                                    v-model="form.job_title"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <!-- Source & Status -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="source" class="block text-sm font-medium text-gray-700">
                                    Source
                                </label>
                                <select
                                    id="source"
                                    v-model="form.source"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option v-for="option in sourceOptions" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option v-for="option in statusOptions" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Assigned To -->
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

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Notes
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
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
                                Create Contact
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
