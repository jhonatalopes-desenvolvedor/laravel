<script setup>
import { computed } from 'vue'

/**
 * Componente de Toggle (switch) genérico para formulários.
 *
 * @property {string} id
 * @property {boolean} modelValue
 * @property {string} [color='primary']
 * @property {boolean} [isInvalid=false]
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
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'success', 'danger', 'warning'].includes(value),
    },
    isInvalid: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const toggleColorClasses = computed(() => {
    switch (props.color) {
        case 'success':
            return 'peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:bg-green-600'
        case 'danger':
            return 'peer-focus:ring-red-300 dark:peer-focus:ring-red-800 peer-checked:bg-red-600'
        case 'warning':
            return 'peer-focus:ring-yellow-300 dark:peer-focus:ring-yellow-800 peer-checked:bg-yellow-400'
        case 'primary':
        default:
            return 'peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 peer-checked:bg-blue-600'
    }
})
</script>

<template>
    <div>
        <label v-if="$slots.label" :for="props.id" class="block mb-2 text-sm font-medium text-neutral-300 dark:text-neutral-200">
            <slot name="label" />
        </label>

        <label :for="props.id" class="inline-flex items-center cursor-pointer">
            <input :id="props.id" type="checkbox" :checked="props.modelValue" @change="emit('update:modelValue', $event.target.checked)" class="sr-only peer" :aria-invalid="isInvalid ? 'true' : null" :aria-describedby="isInvalid ? `${props.id}-error` : null" />
            <div
                class="relative w-11 h-6 bg-neutral-200 rounded-full peer peer-focus:ring-4 dark:bg-neutral-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-neutral-600"
                :class="[toggleColorClasses, { 'ring-2 ring-red-500': props.isInvalid }]"
            ></div>
            <span v-if="$slots.description" class="ms-3 text-sm text-neutral-300 dark:text-neutral-200">
                <slot name="description" />
            </span>
        </label>

        <div v-if="$slots.error && isInvalid" :id="`${props.id}-error`" class="mt-2 text-xs text-red-400">
            <slot name="error" />
        </div>
    </div>
</template>
