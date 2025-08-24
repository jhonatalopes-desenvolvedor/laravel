<script setup>
/**
 * Componente de Card genérico para encapsular conteúdo.
 *
 * @slot header
 * @slot default
 * @slot footer
 */
</script>

<template>
    <div class="bg-[#2f2f2f] rounded-md @container flex flex-col h-full shadow-md">
        <div v-if="$slots.header" class="px-6 py-4 border-b border-neutral-700">
            <slot name="header" />
        </div>

        <div class="flex-grow p-6">
            <slot />
        </div>

        <div v-if="$slots.footer" class="px-6 py-4 border-t border-neutral-700 rounded-b-md">
            <slot name="footer" />
        </div>
    </div>
</template>
