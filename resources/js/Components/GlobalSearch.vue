<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useApi } from '@/composables/useApi';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import {
    UserGroupIcon,
    UsersIcon,
    BuildingOfficeIcon,
    CurrencyDollarIcon,
    ClipboardDocumentListIcon,
} from '@heroicons/vue/24/solid';

const { get, loading } = useApi();

const query = ref('');
const results = ref([]);
const showResults = ref(false);
const selectedIndex = ref(-1);
let searchTimeout = null;

const entityIcons = {
    lead: UserGroupIcon,
    contact: UsersIcon,
    company: BuildingOfficeIcon,
    deal: CurrencyDollarIcon,
    task: ClipboardDocumentListIcon,
};

const entityColors = {
    lead: 'text-blue-600',
    contact: 'text-green-600',
    company: 'text-purple-600',
    deal: 'text-yellow-600',
    task: 'text-orange-600',
};

const groupedResults = ref({});

const search = async () => {
    if (query.value.length < 2) {
        results.value = [];
        groupedResults.value = {};
        showResults.value = false;
        return;
    }

    try {
        const response = await get('/search', {
            params: { q: query.value },
        });

        results.value = response || [];

        // Group results by entity type
        groupedResults.value = results.value.reduce((acc, item) => {
            const type = item.type.toLowerCase();
            if (!acc[type]) {
                acc[type] = [];
            }
            acc[type].push(item);
            return acc;
        }, {});

        showResults.value = true;
        selectedIndex.value = -1;
    } catch (error) {
        console.error('Search error:', error);
        results.value = [];
        groupedResults.value = {};
    }
};

const handleInput = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(search, 300);
};

const navigateToResult = (item) => {
    const routes = {
        lead: 'leads.show',
        contact: 'contacts.show',
        company: 'companies.show',
        deal: 'deals.show',
        task: 'tasks.show',
    };

    const routeName = routes[item.type.toLowerCase()];
    if (routeName) {
        router.visit(route(routeName, item.id));
        query.value = '';
        showResults.value = false;
    }
};

const handleKeydown = (event) => {
    const totalResults = results.value.length;

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        selectedIndex.value = Math.min(selectedIndex.value + 1, totalResults - 1);
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        selectedIndex.value = Math.max(selectedIndex.value - 1, -1);
    } else if (event.key === 'Enter' && selectedIndex.value >= 0) {
        event.preventDefault();
        navigateToResult(results.value[selectedIndex.value]);
    } else if (event.key === 'Escape') {
        showResults.value = false;
        selectedIndex.value = -1;
    }
};

const handleBlur = () => {
    // Delay to allow click events on results
    setTimeout(() => {
        showResults.value = false;
        selectedIndex.value = -1;
    }, 200);
};

watch(query, handleInput);
</script>

<template>
    <div class="relative">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </div>
            <input
                v-model="query"
                type="text"
                placeholder="Search leads, contacts, companies..."
                class="block w-full rounded-md border-gray-300 pl-10 pr-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                @keydown="handleKeydown"
                @blur="handleBlur"
                @focus="() => query.length >= 2 && (showResults = true)"
            />
            <div
                v-if="loading"
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
            >
                <div class="h-4 w-4 animate-spin rounded-full border-2 border-blue-500 border-t-transparent"></div>
            </div>
        </div>

        <!-- Results Dropdown -->
        <div
            v-if="showResults && Object.keys(groupedResults).length > 0"
            class="absolute z-50 mt-2 w-full max-w-md rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5"
        >
            <div class="max-h-96 overflow-y-auto py-1">
                <template v-for="(items, type) in groupedResults" :key="type">
                    <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 bg-gray-50">
                        {{ type }}s ({{ items.length }})
                    </div>
                    <button
                        v-for="(item, index) in items"
                        :key="item.id"
                        @click="navigateToResult(item)"
                        class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-3 transition"
                        :class="{
                            'bg-gray-100': selectedIndex === results.indexOf(item),
                        }"
                    >
                        <div :class="['flex-shrink-0', entityColors[type]]">
                            <component
                                :is="entityIcons[type]"
                                class="h-5 w-5"
                                aria-hidden="true"
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ item.name }}
                            </p>
                            <p v-if="item.subtitle" class="text-xs text-gray-500 truncate">
                                {{ item.subtitle }}
                            </p>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- No Results -->
        <div
            v-else-if="showResults && query.length >= 2 && !loading"
            class="absolute z-50 mt-2 w-full max-w-md rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5"
        >
            <div class="px-4 py-8 text-center">
                <MagnifyingGlassIcon class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-2 text-sm text-gray-500">No results found for "{{ query }}"</p>
            </div>
        </div>
    </div>
</template>
