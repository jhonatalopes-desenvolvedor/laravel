<script setup>
/**
 * Componente de Checkbox genérico para formulários.
 *
 * @property {string} id
 * @property {boolean} modelValue
 * @property {boolean} [isInvalid=false] -
 *
 * @emits {boolean} update:modelValue
 */
const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    modelValue: {
        type: Boolean,
        required: true,
    },
    isInvalid: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
    <div>
        <div class="flex items-center">
            <input
                :id="props.id"
                type="checkbox"
                :checked="props.modelValue"
                @change="emit('update:modelValue', $event.target.checked)"
                :class="{ 'border-red-500': props.isInvalid }"
                class="h-4 w-4 rounded border-neutral-600 bg-neutral-700 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:ring-offset-neutral-800"
                :aria-invalid="isInvalid ? 'true' : null"
                :aria-describedby="isInvalid ? `${props.id}-error` : null"
            />
            <label :for="props.id" class="ml-2 text-sm font-medium text-neutral-300 dark:text-white">
                <slot name="label" />
            </label>
        </div>
        <div v-if="$slots.error && isInvalid" :id="`${props.id}-error`" class="mt-2 text-xs text-red-400">
            <slot name="error" />
        </div>
    </div>
</template>
