<script setup>
import { computed } from 'vue'

/**
 * Componente de botão genérico com estados, cores e tamanhos.
 *
 * @property {string} [type='button']
 * @property {boolean} [processing=false]
 * @property {boolean} [disabled=false]
 * @property {string} [color='primary']
 * @property {string} [size='md']
 */
const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    processing: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'success', 'danger', 'warning', 'neutral', 'lime'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
})

const colorClasses = computed(() => {
    switch (props.color) {
        case 'success':
            return 'text-white bg-green-700 hover:bg-green-800 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 border border-green-600'
        case 'danger':
            return 'text-white bg-red-700 hover:bg-red-800 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900'
        case 'warning':
            return 'text-gray-900 bg-amber-400 hover:bg-amber-500 focus:ring-amber-300 dark:focus:ring-amber-800'
        case 'neutral':
            return 'text-white bg-neutral-700 hover:bg-neutral-800 focus:ring-neutral-300 dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:focus:ring-neutral-600 dark:border-neutral-600 border border-neutral-700'
        case 'lime':
            return 'text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-lime-300 dark:focus:ring-lime-800'
        case 'primary':
        default:
            return 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800'
    }
})

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'px-3 py-2 text-xs'
        case 'lg':
            return 'px-6 py-3 text-base'
        case 'md':
        default:
            return 'px-5 py-2.5 text-sm'
    }
})
</script>

<template>
    <button
        :type="props.type"
        :disabled="props.disabled || props.processing"
        :class="['flex items-center justify-center gap-2 text-nowrap font-medium rounded-lg focus:outline-none focus:ring-4 transition-colors duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed', colorClasses, sizeClasses]"
    >
        <svg v-if="props.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>

        <span v-else class="flex items-center gap-2">
            <slot />
        </span>
    </button>
</template>
