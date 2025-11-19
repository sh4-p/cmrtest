<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    icon: {
        type: Object,
        default: null,
    },
    iconColor: {
        type: String,
        default: 'blue',
    },
    trend: {
        type: Object,
        default: null,
        // Example: { value: 12, direction: 'up' }
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const iconColorClass = computed(() => {
    const colors = {
        blue: 'text-blue-600',
        green: 'text-green-600',
        purple: 'text-purple-600',
        yellow: 'text-yellow-600',
        red: 'text-red-600',
        gray: 'text-gray-600',
    };
    return colors[props.iconColor] || colors.blue;
});

const trendColorClass = computed(() => {
    if (!props.trend) return '';
    return props.trend.direction === 'up' ? 'text-green-600' : 'text-red-600';
});

const trendBgClass = computed(() => {
    if (!props.trend) return '';
    return props.trend.direction === 'up' ? 'bg-green-100' : 'bg-red-100';
});
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
        <div class="flex items-center">
            <!-- Icon -->
            <div v-if="icon" class="flex-shrink-0">
                <component :is="icon" :class="['h-8 w-8', iconColorClass]" />
            </div>

            <!-- Content -->
            <div :class="icon ? 'ml-5' : ''" class="w-0 flex-1">
                <dt class="truncate text-sm font-medium text-gray-500">
                    {{ title }}
                </dt>
                <dd class="mt-1 flex items-baseline">
                    <!-- Loading State -->
                    <div v-if="loading" class="h-8 w-20 animate-pulse rounded bg-gray-200"></div>

                    <!-- Value -->
                    <div v-else class="text-3xl font-semibold tracking-tight text-gray-900">
                        {{ value }}
                    </div>

                    <!-- Trend -->
                    <div v-if="trend && !loading" :class="['ml-2 flex items-baseline text-sm font-semibold', trendColorClass]">
                        <svg
                            v-if="trend.direction === 'up'"
                            class="h-5 w-5 flex-shrink-0 self-center"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-5 w-5 flex-shrink-0 self-center"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="sr-only">
                            {{ trend.direction === 'up' ? 'Increased' : 'Decreased' }} by
                        </span>
                        {{ trend.value }}%
                    </div>
                </dd>
            </div>
        </div>

        <!-- Additional Info Slot -->
        <div v-if="$slots.default" class="mt-4 border-t border-gray-100 pt-4">
            <slot />
        </div>
    </div>
</template>
