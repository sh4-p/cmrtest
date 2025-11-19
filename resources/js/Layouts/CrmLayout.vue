<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';
import {
    HomeIcon,
    UserGroupIcon,
    UsersIcon,
    BuildingOfficeIcon,
    CurrencyDollarIcon,
    CheckCircleIcon,
    ChartBarIcon,
    Cog6ToothIcon,
} from '@heroicons/vue/24/outline';

const sidebarOpen = ref(true);
const mobileMenuOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: 'dashboard', icon: HomeIcon, permission: null },
    { name: 'Leads', href: 'leads.index', icon: UserGroupIcon, permission: 'view-leads' },
    { name: 'Contacts', href: 'contacts.index', icon: UsersIcon, permission: 'view-contacts' },
    { name: 'Companies', href: 'companies.index', icon: BuildingOfficeIcon, permission: 'view-companies' },
    { name: 'Deals', href: 'deals.index', icon: CurrencyDollarIcon, permission: 'view-deals' },
    { name: 'Tasks', href: 'tasks.index', icon: CheckCircleIcon, permission: 'view-tasks' },
    { name: 'Reports', href: 'reports.index', icon: ChartBarIcon, permission: 'view-reports' },
    { name: 'Settings', href: 'settings.index', icon: Cog6ToothIcon, permission: 'manage-settings' },
];

const canAccess = (permission) => {
    if (!permission) return true;
    return $page.props.auth.permissions?.includes(permission) ||
           $page.props.auth.user?.is_super_admin ||
           false;
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile sidebar overlay -->
        <div
            v-show="mobileMenuOpen"
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
            @click="mobileMenuOpen = false"
        ></div>

        <!-- Sidebar -->
        <div
            :class="[
                'fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white shadow-lg transition-transform duration-300 ease-in-out lg:translate-x-0',
                mobileMenuOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <!-- Logo -->
            <div class="flex h-16 items-center justify-between border-b border-gray-200 px-6">
                <Link :href="route('dashboard')" class="flex items-center">
                    <ApplicationLogo class="h-8 w-auto" />
                    <span class="ml-2 text-xl font-bold text-gray-800">CRM</span>
                </Link>
                <button
                    @click="mobileMenuOpen = false"
                    class="lg:hidden"
                >
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <template v-for="item in navigation" :key="item.name">
                    <Link
                        v-if="canAccess(item.permission)"
                        :href="route(item.href)"
                        :class="[
                            route().current(item.href + '*')
                                ? 'bg-blue-50 text-blue-600'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                            'group flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150',
                        ]"
                    >
                        <component
                            :is="item.icon"
                            :class="[
                                route().current(item.href + '*')
                                    ? 'text-blue-600'
                                    : 'text-gray-400 group-hover:text-gray-500',
                                'mr-3 h-5 w-5 flex-shrink-0',
                            ]"
                        />
                        {{ item.name }}
                    </Link>
                </template>
            </nav>

            <!-- User section -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-medium text-white">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="text-sm font-medium text-gray-700">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content area -->
        <div class="lg:pl-64">
            <!-- Top navigation bar -->
            <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 border-b border-gray-200 bg-white shadow-sm">
                <button
                    @click="mobileMenuOpen = true"
                    class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex flex-1 justify-between px-4 sm:px-6 lg:px-8">
                    <!-- Page title from slot -->
                    <div class="flex items-center">
                        <h1 v-if="$slots.header" class="text-xl font-semibold text-gray-900">
                            <slot name="header" />
                        </h1>
                    </div>

                    <!-- Right side items -->
                    <div class="ml-4 flex items-center space-x-4">
                        <!-- Notifications (future) -->
                        <!-- <button class="rounded-full p-1 text-gray-400 hover:text-gray-500">
                            <BellIcon class="h-6 w-6" />
                        </button> -->

                        <!-- User dropdown -->
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none"
                                >
                                    <span>{{ $page.props.auth.user.name }}</span>
                                    <svg
                                        class="ml-2 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
                                </DropdownLink>
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1">
                <div class="py-6">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
