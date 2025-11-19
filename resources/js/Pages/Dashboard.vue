<script setup>
import CrmLayout from '@/Layouts/CrmLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    UserGroupIcon,
    UsersIcon,
    BuildingOfficeIcon,
    CurrencyDollarIcon
} from '@heroicons/vue/24/outline';

// Mock data - will be replaced with real data from backend
const stats = [
    { name: 'Total Leads', value: '0', icon: UserGroupIcon, color: 'blue' },
    { name: 'Total Contacts', value: '0', icon: UsersIcon, color: 'green' },
    { name: 'Total Companies', value: '0', icon: BuildingOfficeIcon, color: 'purple' },
    { name: 'Active Deals', value: '0', icon: CurrencyDollarIcon, color: 'yellow' },
];
</script>

<template>
    <Head title="Dashboard" />

    <CrmLayout>
        <template #header>
            Dashboard
        </template>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="item in stats"
                :key="item.name"
                class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <component
                            :is="item.icon"
                            :class="[
                                item.color === 'blue' && 'text-blue-600',
                                item.color === 'green' && 'text-green-600',
                                item.color === 'purple' && 'text-purple-600',
                                item.color === 'yellow' && 'text-yellow-600',
                                'h-8 w-8',
                            ]"
                        />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dt class="truncate text-sm font-medium text-gray-500">
                            {{ item.name }}
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                            {{ item.value }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Message -->
        <div class="mt-8">
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Welcome to Advanced CRM System
                    </h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>
                            This is your CRM dashboard. Here you can manage leads, contacts, companies, deals, and tasks.
                            Use the navigation menu on the left to access different sections.
                        </p>
                    </div>
                    <div class="mt-5">
                        <p class="text-sm text-gray-600">
                            <strong>Quick Actions:</strong>
                        </p>
                        <div class="mt-2 flex flex-wrap gap-3">
                            <a
                                href="#"
                                class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                            >
                                Create Lead
                            </a>
                            <a
                                href="#"
                                class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            >
                                Add Contact
                            </a>
                            <a
                                href="#"
                                class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            >
                                Create Deal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Placeholder -->
        <div class="mt-8">
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Recent Activity
                    </h3>
                    <div class="mt-4 text-sm text-gray-500">
                        <p>No recent activity to display.</p>
                    </div>
                </div>
            </div>
        </div>
    </CrmLayout>
</template>
