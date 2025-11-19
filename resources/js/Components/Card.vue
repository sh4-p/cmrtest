<script setup>
defineProps({
    title: {
        type: String,
        default: null,
    },
    subtitle: {
        type: String,
        default: null,
    },
    padding: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <!-- Header -->
        <div
            v-if="$slots.header || title"
            class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6"
        >
            <slot name="header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-gray-900">
                            {{ title }}
                        </h3>
                        <p v-if="subtitle" class="mt-1 max-w-2xl text-sm text-gray-500">
                            {{ subtitle }}
                        </p>
                    </div>
                    <div v-if="$slots.actions">
                        <slot name="actions" />
                    </div>
                </div>
            </slot>
        </div>

        <!-- Body -->
        <div :class="padding ? 'px-4 py-5 sm:p-6' : ''">
            <slot />
        </div>

        <!-- Footer -->
        <div
            v-if="$slots.footer"
            class="border-t border-gray-200 bg-gray-50 px-4 py-4 sm:px-6"
        >
            <slot name="footer" />
        </div>
    </div>
</template>
