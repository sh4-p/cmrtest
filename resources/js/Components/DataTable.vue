<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useApi } from '@/composables/useApi';
import {
    ChevronUpIcon,
    ChevronDownIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    columns: {
        type: Array,
        required: true,
        // Example: [{ key: 'name', label: 'Name', sortable: true }, ...]
    },
    apiEndpoint: {
        type: String,
        required: true,
    },
    perPage: {
        type: Number,
        default: 10,
    },
    searchable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['row-click']);

const { get, loading } = useApi();

const data = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const totalItems = ref(0);
const searchQuery = ref('');
const sortColumn = ref(null);
const sortDirection = ref('asc');

let searchTimeout = null;

const fetchData = async () => {
    try {
        const params = {
            page: currentPage.value,
            per_page: props.perPage,
            search: searchQuery.value,
            sort_by: sortColumn.value,
            sort_direction: sortDirection.value,
        };

        const response = await get(props.apiEndpoint, { params });

        data.value = response.data || [];
        currentPage.value = response.current_page || 1;
        totalPages.value = response.last_page || 1;
        totalItems.value = response.total || 0;
    } catch (error) {
        console.error('Error fetching data:', error);
        data.value = [];
    }
};

const handleSort = (column) => {
    if (!column.sortable) return;

    if (sortColumn.value === column.key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn.value = column.key;
        sortDirection.value = 'asc';
    }

    fetchData();
};

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage.value = 1;
        fetchData();
    }, 300);
};

const goToPage = (page) => {
    if (page < 1 || page > totalPages.value) return;
    currentPage.value = page;
    fetchData();
};

const paginationRange = computed(() => {
    const range = [];
    const delta = 2;
    const left = currentPage.value - delta;
    const right = currentPage.value + delta;

    for (let i = 1; i <= totalPages.value; i++) {
        if (i === 1 || i === totalPages.value || (i >= left && i <= right)) {
            range.push(i);
        } else if (range[range.length - 1] !== '...') {
            range.push('...');
        }
    }

    return range;
});

watch(searchQuery, handleSearch);

onMounted(() => {
    fetchData();
});

defineExpose({
    refresh: fetchData,
});
</script>

<template>
    <div class="space-y-4">
        <!-- Search Bar -->
        <div v-if="searchable" class="flex items-center justify-between">
            <div class="relative flex-1 max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </div>
                <input
                    v-model="searchQuery"
                    type="text"
                    class="block w-full rounded-md border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="Search..."
                />
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                v-for="column in columns"
                                :key="column.key"
                                scope="col"
                                :class="[
                                    'px-3 py-3.5 text-left text-sm font-semibold text-gray-900',
                                    column.sortable ? 'cursor-pointer select-none hover:bg-gray-100' : '',
                                ]"
                                @click="handleSort(column)"
                            >
                                <div class="flex items-center gap-2">
                                    {{ column.label }}
                                    <span v-if="column.sortable && sortColumn === column.key">
                                        <ChevronUpIcon
                                            v-if="sortDirection === 'asc'"
                                            class="h-4 w-4"
                                        />
                                        <ChevronDownIcon v-else class="h-4 w-4" />
                                    </span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <!-- Loading State -->
                        <tr v-if="loading">
                            <td :colspan="columns.length" class="px-3 py-12 text-center">
                                <div class="flex justify-center">
                                    <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
                                </div>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-else-if="data.length === 0">
                            <td :colspan="columns.length" class="px-3 py-12 text-center">
                                <p class="text-sm text-gray-500">No data available</p>
                            </td>
                        </tr>

                        <!-- Data Rows -->
                        <tr
                            v-else
                            v-for="(row, index) in data"
                            :key="index"
                            class="hover:bg-gray-50 cursor-pointer"
                            @click="emit('row-click', row)"
                        >
                            <td
                                v-for="column in columns"
                                :key="column.key"
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-900"
                            >
                                <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                    {{ row[column.key] }}
                                </slot>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                <button
                    @click="goToPage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                >
                    Previous
                </button>
                <button
                    @click="goToPage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                >
                    Next
                </button>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
                        to
                        <span class="font-medium">{{ Math.min(currentPage * perPage, totalItems) }}</span>
                        of
                        <span class="font-medium">{{ totalItems }}</span>
                        results
                    </p>
                </div>
                <div>
                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 disabled:opacity-50"
                        >
                            <span class="sr-only">Previous</span>
                            <ChevronUpIcon class="h-5 w-5 rotate-[-90deg]" />
                        </button>

                        <template v-for="(page, index) in paginationRange" :key="index">
                            <span
                                v-if="page === '...'"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300"
                            >
                                ...
                            </span>
                            <button
                                v-else
                                @click="goToPage(page)"
                                :class="[
                                    page === currentPage
                                        ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                                        : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50',
                                    'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                                ]"
                            >
                                {{ page }}
                            </button>
                        </template>

                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 disabled:opacity-50"
                        >
                            <span class="sr-only">Next</span>
                            <ChevronUpIcon class="h-5 w-5 rotate-90" />
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>
