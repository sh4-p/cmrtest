<script setup>
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useApi } from '@/composables/useApi';
import {
    PencilIcon,
    TrashIcon,
    UserIcon,
    EnvelopeIcon,
    PhoneIcon,
    BriefcaseIcon,
    BuildingOfficeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    contactId: {
        type: [String, Number],
        required: true,
    },
});

const { get, destroy, loading } = useApi();
const contact = ref(null);
const showDeleteModal = ref(false);

const fetchContact = async () => {
    try {
        const response = await get(`/contacts/${props.contactId}`);
        contact.value = response;
    } catch (error) {
        console.error('Error fetching contact:', error);
    }
};

const handleEdit = () => {
    router.visit(route('contacts.edit', props.contactId));
};

const handleDelete = async () => {
    try {
        await destroy(`/contacts/${props.contactId}`);
        router.visit(route('contacts.index'));
    } catch (error) {
        console.error('Error deleting contact:', error);
    }
};

const getStatusColor = (status) => {
    const colors = {
        'New': 'bg-blue-100 text-blue-800',
        'Contacted': 'bg-yellow-100 text-yellow-800',
        'Qualified': 'bg-green-100 text-green-800',
        'Lost': 'bg-red-100 text-red-800',
        'Converted': 'bg-purple-100 text-purple-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

onMounted(() => {
    fetchContact();
});
</script>

<template>
    <Head title="Contact Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold contacting-tight text-gray-800">
                    Contact Details
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

                <!-- Contact Details -->
                <div v-else-if="contact" class="space-y-6">
                    <!-- Header Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">
                                        {{ contact.full_name }}
                                    </h3>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span :class="['inline-flex rounded-full px-3 py-1 text-sm font-semibold', getStatusColor(contact.status)]">
                                            {{ contact.status }}
                                        </span>
                                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-800">
                                            {{ contact.source }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h4 class="text-lg font-semibold text-gray-900">Contact Information</h4>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="flex items-start gap-3">
                                    <EnvelopeIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a :href="`mailto:${contact.email}`" class="text-blue-600 hover:text-blue-500">
                                                {{ contact.email }}
                                            </a>
                                        </dd>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <PhoneIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ contact.phone || '-' }}
                                        </dd>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <BuildingOfficeIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Company</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ contact.company || '-' }}
                                        </dd>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <BriefcaseIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Job Title</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ contact.job_title || '-' }}
                                        </dd>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <UserIcon class="h-6 w-6 text-gray-400" />
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ contact.assigned_to?.name || 'Unassigned' }}
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h4 class="text-lg font-semibold text-gray-900">Additional Information</h4>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                                        {{ contact.notes || 'No notes available' }}
                                    </dd>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ formatDate(contact.created_at) }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ formatDate(contact.updated_at) }}
                                        </dd>
                                    </div>
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
                            Delete Contact
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Are you sure you want to delete this contact? This action cannot be undone.
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
