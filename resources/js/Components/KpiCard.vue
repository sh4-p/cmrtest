<script setup>
import { computed } from 'vue';
import { ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/24/solid';

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
        required: true,
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'purple', 'yellow', 'red', 'indigo'].includes(value),
    },
    change: {
        type: Number,
        default: null,
    },
    changeLabel: {
        type: String,
        default: 'vs last month',
    },
});

const changeDirection = computed(() => {
    if (props.change === null || props.change === 0) return 'neutral';
    return props.change > 0 ? 'up' : 'down';
});

const changeColor = computed(() => {
    if (changeDirection.value === 'up') return 'text-green-600';
    if (changeDirection.value === 'down') return 'text-red-600';
    return 'text-gray-600';
});

const iconColorClass = computed(() => {
    const colors = {
        blue: 'text-blue-600 bg-blue-100',
        green: 'text-green-600 bg-green-100',
        purple: 'text-purple-600 bg-purple-100',
        yellow: 'text-yellow-600 bg-yellow-100',
        red: 'text-red-600 bg-red-100',
        indigo: 'text-indigo-600 bg-indigo-100',
    };
    return colors[props.color];
});
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div :class="['rounded-md p-3', iconColorClass]">
                        <component :is="icon" class="h-6 w-6" aria-hidden="true" />
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">
                            {{ title }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">
                                {{ value }}
                            </div>
                            <div v-if="change !== null" :class="['ml-2 flex items-baseline text-sm font-semibold', changeColor]">
                                <ArrowUpIcon
                                    v-if="changeDirection === 'up'"
                                    class="h-4 w-4 flex-shrink-0 self-center"
                                    aria-hidden="true"
                                />
                                <ArrowDownIcon
                                    v-else-if="changeDirection === 'down'"
                                    class="h-4 w-4 flex-shrink-0 self-center"
                                    aria-hidden="true"
                                />
                                <span>
                                    {{ Math.abs(change) }}%
                                </span>
                            </div>
                        </dd>
                        <dd v-if="change !== null" class="mt-1 text-xs text-gray-500">
                            {{ changeLabel }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>
