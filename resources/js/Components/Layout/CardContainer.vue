<script setup>
import BlockHeader from '@/Components/Sections/BlockHeader.vue'

/**
 * Componente de Card especializado para páginas de listagem.
 *
 * @property {string} [title='']
 * @property {string} [description='']
 *
 * @slot filters
 * @slot default
 * @slot footer
 */
const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
})
</script>

<template>
    <div class="bg-neutral-800 rounded-md shadow-lg @container">
        <BlockHeader :title="props.title" :description="props.description" />

        <div v-if="$slots.filters" class="px-8 py-4">
            <slot name="filters" />
        </div>

        <main class="px-8 py-4 space-y-4">
            <slot />
        </main>

        <footer v-if="$slots.footer" class="border-t border-neutral-700 py-4 px-8">
            <slot name="footer" />
        </footer>
    </div>
</template>
