<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) =>
            ['default', 'success', 'warning', 'danger', 'info', 'purple', 'gray'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    rounded: {
        type: Boolean,
        default: false,
    },
    removable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['remove']);

const variantClasses = computed(() => {
    const variants = {
        default: 'bg-gray-100 text-gray-800',
        success: 'bg-green-100 text-green-800',
        warning: 'bg-yellow-100 text-yellow-800',
        danger: 'bg-red-100 text-red-800',
        info: 'bg-blue-100 text-blue-800',
        purple: 'bg-purple-100 text-purple-800',
        gray: 'bg-gray-100 text-gray-600',
    };
    return variants[props.variant] || variants.default;
});

const sizeClasses = computed(() => {
    const sizes = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-2.5 py-0.5 text-sm',
        lg: 'px-3 py-1 text-base',
    };
    return sizes[props.size];
});

const roundedClass = computed(() => {
    return props.rounded ? 'rounded-full' : 'rounded';
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center font-medium',
            variantClasses,
            sizeClasses,
            roundedClass,
        ]"
    >
        <slot />
        <button
            v-if="removable"
            type="button"
            @click="emit('remove')"
            class="ml-1 inline-flex flex-shrink-0 rounded-full p-0.5 hover:bg-black hover:bg-opacity-10 focus:bg-black focus:bg-opacity-20 focus:outline-none"
        >
            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>
    </span>
</template>
