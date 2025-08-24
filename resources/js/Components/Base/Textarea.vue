<script setup>
/**
 * Componente de Textarea genérico para formulários.
 *
 * @property {string} id
 * @property {number|string} [rows=4]
 * @property {string} [placeholder='']
 * @property {string|number|null} modelValue
 * @property {boolean} [isInvalid=false]
 */
const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    rows: {
        type: [Number, String],
        default: 4,
    },
    placeholder: {
        type: String,
        default: '',
    },
    modelValue: {
        type: [String, Number, null],
        required: true,
    },
    isInvalid: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue', 'change'])

const updateValue = (event) => {
    emit('update:modelValue', event.target.value)
    emit('change', event.target.value)
}
</script>

<template>
    <div>
        <label v-if="$slots.label" :for="props.id" class="block mb-2 text-sm font-medium text-neutral-300 dark:text-neutral-200">
            <slot name="label" />
        </label>

        <div class="relative flex items-center">
            <textarea
                :id="props.id"
                :rows="props.rows"
                :value="props.modelValue"
                :placeholder="props.placeholder"
                @input="updateValue"
                :class="{ 'pr-10': isInvalid, 'border-red-500 focus:ring-red-500 focus:border-red-500': isInvalid }"
                class="block p-2.5 w-full text-sm text-neutral-100 bg-neutral-700 rounded-lg border border-neutral-600 focus:ring-primary-500 focus:border-primary-500 dark:bg-neutral-700 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                :aria-invalid="isInvalid ? 'true' : null"
                :aria-describedby="isInvalid ? `${props.id}-error` : null"
            ></textarea>
            <div v-if="isInvalid" class="absolute top-3 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div v-if="$slots.error" :id="`${props.id}-error`" class="mt-2 text-xs text-red-400">
            <slot name="error" />
        </div>
    </div>
</template>
