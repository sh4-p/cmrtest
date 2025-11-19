<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="max-w-4xl mx-auto p-8 bg-white rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                {{ message }}
            </h1>
            <p class="text-gray-600 mb-4">
                Welcome to your Advanced CRM System built with Laravel, Vue.js 3, and Tailwind CSS
            </p>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <CheckCircleIcon class="h-5 w-5 text-green-500" />
                <span>Laravel {{ laravelVersion }}</span>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <CheckCircleIcon class="h-5 w-5 text-green-500" />
                <span>Vue.js 3</span>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <CheckCircleIcon class="h-5 w-5 text-green-500" />
                <span>Tailwind CSS</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { CheckCircleIcon } from '@heroicons/vue/24/solid'

const message = ref('Advanced CRM System')
const laravelVersion = ref('12.39.0')
</script>
