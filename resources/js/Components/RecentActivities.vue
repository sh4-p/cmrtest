<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '@/composables/useApi';
import {
    PhoneIcon,
    EnvelopeIcon,
    UserGroupIcon,
    DocumentTextIcon,
} from '@heroicons/vue/24/outline';

const { get, loading } = useApi();
const activities = ref([]);

const getActivityIcon = (type) => {
    const icons = {
        Call: PhoneIcon,
        Email: EnvelopeIcon,
        Meeting: UserGroupIcon,
        Note: DocumentTextIcon,
    };
    return icons[type] || DocumentTextIcon;
};

const getActivityColor = (type) => {
    const colors = {
        Call: 'text-blue-600 bg-blue-100',
        Email: 'text-green-600 bg-green-100',
        Meeting: 'text-purple-600 bg-purple-100',
        Note: 'text-gray-600 bg-gray-100',
    };
    return colors[type] || 'text-gray-600 bg-gray-100';
};

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
    if (seconds < 604800) return `${Math.floor(seconds / 86400)}d ago`;
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const fetchActivities = async () => {
    try {
        const response = await get('/activities/recent');
        activities.value = response.slice(0, 10); // Limit to 10 activities
    } catch (error) {
        console.error('Error fetching activities:', error);
    }
};

onMounted(() => {
    fetchActivities();
});

defineExpose({
    refresh: fetchActivities,
});
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
                Recent Activity
            </h3>
        </div>
        <div class="border-t border-gray-200">
            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center py-12">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
            </div>

            <!-- Empty State -->
            <div v-else-if="activities.length === 0" class="px-4 py-8 text-center">
                <p class="text-sm text-gray-500">No recent activity to display.</p>
            </div>

            <!-- Activities List -->
            <ul v-else role="list" class="divide-y divide-gray-200">
                <li v-for="activity in activities" :key="activity.id" class="px-4 py-4 hover:bg-gray-50">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div :class="['rounded-lg p-2', getActivityColor(activity.type)]">
                                <component :is="getActivityIcon(activity.type)" class="h-5 w-5" aria-hidden="true" />
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ activity.user?.name || 'Unknown User' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ activity.description }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                {{ formatTimeAgo(activity.created_at) }}
                            </p>
                        </div>
                        <span
                            :class="[
                                'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                activity.type === 'Call' ? 'bg-blue-100 text-blue-800' :
                                activity.type === 'Email' ? 'bg-green-100 text-green-800' :
                                activity.type === 'Meeting' ? 'bg-purple-100 text-purple-800' :
                                'bg-gray-100 text-gray-800'
                            ]"
                        >
                            {{ activity.type }}
                        </span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
