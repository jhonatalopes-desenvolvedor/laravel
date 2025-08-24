<script setup>
import { computed } from 'vue'
import icons from '@/icons.js'

/**
 * Componente para renderizar ícones SVG a partir de um objeto central.
 *
 * @property {string} name
 * @property {string} [class='']
 */
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    class: {
        type: String,
        default: 'w-6 h-6',
    },
})

const path = computed(() => {
    if (!icons[props.name]) {
        console.warn(`[IconComponent] Icon "${props.name}" not found.`)

        return `<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" /> <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" /> <line x1="12" y1="16" x2="12" y2="16" stroke="currentColor" stroke-width="2" />`
    }
    return icons[props.name]
})
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="props.class" v-html="path"></svg>
</template>
