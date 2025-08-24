<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: { type: String, default: '#' },
    text: { type: String, default: 'Brand' },
})
</script>

<template>
    <Link :href="href" class="flex items-center gap-2">
        <slot>
            <span class="text-2xl font-semibold whitespace-nowrap dark:text-white">
                {{ text }}
            </span>
        </slot>
    </Link>
</template>
