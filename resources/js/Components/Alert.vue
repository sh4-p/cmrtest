<script setup>
import { computed } from 'vue';
import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline';
import { XMarkIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'warning', 'error', 'info'].includes(value),
    },
    title: {
        type: String,
        default: null,
    },
    dismissible: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['dismiss']);

const typeConfig = computed(() => {
    const configs = {
        success: {
            bgColor: 'bg-green-50',
            iconColor: 'text-green-400',
            titleColor: 'text-green-800',
            textColor: 'text-green-700',
            icon: CheckCircleIcon,
        },
        warning: {
            bgColor: 'bg-yellow-50',
            iconColor: 'text-yellow-400',
            titleColor: 'text-yellow-800',
            textColor: 'text-yellow-700',
            icon: ExclamationTriangleIcon,
        },
        error: {
            bgColor: 'bg-red-50',
            iconColor: 'text-red-400',
            titleColor: 'text-red-800',
            textColor: 'text-red-700',
            icon: XCircleIcon,
        },
        info: {
            bgColor: 'bg-blue-50',
            iconColor: 'text-blue-400',
            titleColor: 'text-blue-800',
            textColor: 'text-blue-700',
            icon: InformationCircleIcon,
        },
    };
    return configs[props.type] || configs.info;
});
</script>

<template>
    <div :class="['rounded-md p-4', typeConfig.bgColor]">
        <div class="flex">
            <div class="flex-shrink-0">
                <component :is="typeConfig.icon" :class="['h-5 w-5', typeConfig.iconColor]" />
            </div>
            <div class="ml-3 flex-1">
                <h3 v-if="title" :class="['text-sm font-medium', typeConfig.titleColor]">
                    {{ title }}
                </h3>
                <div :class="['text-sm', title ? 'mt-2' : '', typeConfig.textColor]">
                    <slot />
                </div>
            </div>
            <div v-if="dismissible" class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button
                        type="button"
                        @click="emit('dismiss')"
                        :class="[
                            'inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2',
                            typeConfig.bgColor,
                            typeConfig.iconColor,
                        ]"
                    >
                        <span class="sr-only">Dismiss</span>
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
