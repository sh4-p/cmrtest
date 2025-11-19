<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '@/composables/useApi';
import {
    PhoneIcon,
    EnvelopeIcon,
    UserGroupIcon,
    DocumentTextIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    limit: {
        type: Number,
        default: 10,
    },
});

const { get, loading } = useApi();
const activities = ref([]);

const activityIcons = {
    Call: PhoneIcon,
    Email: EnvelopeIcon,
    Meeting: UserGroupIcon,
    Note: DocumentTextIcon,
};

const activityColors = {
    Call: 'bg-blue-500',
    Email: 'bg-green-500',
    Meeting: 'bg-purple-500',
    Note: 'bg-gray-500',
};

const fetchActivities = async () => {
    try {
        const response = await get(`/api/activities/recent?limit=${props.limit}`);
        activities.value = response.data || [];
    } catch (error) {
        console.error('Error fetching activities:', error);
        activities.value = [];
    }
};

const formatDate = (date) => {
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    return d.toLocaleDateString();
};

const loadMore = () => {
    // TODO: Implement load more functionality
    console.log('Load more clicked');
};

onMounted(() => {
    fetchActivities();
});

defineExpose({
    refresh: fetchActivities,
});
</script>

<template>
    <div class="flow-root">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-8">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="activities.length === 0" class="py-8 text-center">
            <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm text-gray-500">No recent activity</p>
        </div>

        <!-- Activity List -->
        <ul v-else role="list" class="-mb-8">
            <li v-for="(activity, index) in activities" :key="activity.id">
                <div class="relative pb-8">
                    <!-- Vertical line connector -->
                    <span
                        v-if="index !== activities.length - 1"
                        class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                    ></span>

                    <div class="relative flex space-x-3">
                        <!-- Activity Icon -->
                        <div>
                            <span
                                :class="[
                                    activityColors[activity.type] || 'bg-gray-500',
                                    'flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-white',
                                ]"
                            >
                                <component
                                    :is="activityIcons[activity.type] || DocumentTextIcon"
                                    class="h-5 w-5 text-white"
                                />
                            </span>
                        </div>

                        <!-- Activity Content -->
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-gray-500">
                                    <span class="font-medium text-gray-900">
                                        {{ activity.user?.name || 'Unknown User' }}
                                    </span>
                                    {{ ' ' }}
                                    <span class="text-gray-600">{{ activity.description }}</span>
                                </p>
                                <p v-if="activity.subject" class="mt-0.5 text-xs text-gray-500">
                                    {{ activity.subject_type?.split('\\').pop() }}:
                                    {{ activity.subject?.name || activity.subject?.full_name || '#' + activity.subject_id }}
                                </p>
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                {{ formatDate(activity.activity_date || activity.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <!-- Load More Button -->
        <div v-if="activities.length >= limit" class="mt-6 text-center">
            <button
                @click="loadMore"
                type="button"
                class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            >
                Load more
            </button>
        </div>
    </div>
</template>
