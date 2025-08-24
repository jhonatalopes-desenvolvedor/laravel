<script setup>
import useFlashMessages from '@/Composables/useFlashMessages.js'

useFlashMessages()
</script>

<template>
    <div>
        <slot />
    </div>
</template>
