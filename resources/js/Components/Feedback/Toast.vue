<script setup>
import { computed } from 'vue'

/**
 * Componente para exibir mensagens de toast.
 *
 * @property {string} type
 * @property {string} text
 *
 * @slot icon
 */
const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value),
    },
    text: {
        type: String,
        required: true,
    },
})

const iconConfig = computed(() => {
    switch (props.type) {
        case 'success':
            return {
                bgClass: 'bg-green-100 dark:bg-green-800',
                iconClass: 'text-green-500 dark:text-green-200',
                path: 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z',
                title: 'Success icon',
            }
        case 'error':
            return {
                bgClass: 'bg-red-100 dark:bg-red-800',
                iconClass: 'text-red-500 dark:text-red-200',
                path: 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z',
                title: 'Error icon',
            }
        case 'warning':
            return {
                bgClass: 'bg-orange-100 dark:bg-orange-700',
                iconClass: 'text-orange-500 dark:text-orange-200',
                path: 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z',
                title: 'Warning icon',
            }
        case 'info':
        default:
            return {
                bgClass: 'bg-blue-100 dark:bg-blue-800',
                iconClass: 'text-blue-500 dark:text-blue-200',
                path: 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z',
                title: 'Info icon',
            }
    }
})
</script>

<template>
    <div class="cursor-pointer flex items-center w-full max-w-xs p-4 rounded-lg shadow-md text-neutral-400 bg-[#2e2e2e]" role="alert">
        <slot name="icon">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg" :class="iconConfig.bgClass">
                <svg class="w-5 h-5" :class="iconConfig.iconClass" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path :d="iconConfig.path" />
                </svg>
                <span class="sr-only">{{ iconConfig.title }}</span>
            </div>
        </slot>

        <div class="ms-3 text-sm font-normal text-white">{{ props.text }}</div>
    </div>
</template>

<style>
.Vue-Toastification__toast--default.my-custom-toast-class {
    background-color: transparent;
    box-shadow: none;
    padding: 0 0;
}
</style>
