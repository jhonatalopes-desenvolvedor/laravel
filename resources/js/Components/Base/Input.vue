<script setup>
/**
 * Componente de Input genérico para formulários.
 *
 * @property {string} id
 * @property {string} [type='text']
 * @property {string} [autocomplete='']
 * @property {string} [placeholder='']
 * @property {string|number|null} modelValue
 * @property {boolean} [hasLeadingIcon=false]
 * @property {boolean} [isInvalid=false]
 *
 * @emits {string|number|null} update:modelValue
 * @emits {string|number|null} change
 */
const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    autocomplete: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    modelValue: {
        type: [String, Number, null],
        required: true,
    },
    hasLeadingIcon: {
        type: Boolean,
        default: false,
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
            <div v-if="$slots.leadingIcon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <slot name="leadingIcon" />
            </div>
            <input
                :id="props.id"
                :type="props.type"
                :value="props.modelValue"
                :placeholder="props.placeholder"
                :autocomplete="props.autocomplete"
                @input="updateValue"
                :class="{ 'pl-10': hasLeadingIcon, 'pr-10': isInvalid }"
                class="w-full px-4 py-2.5 bg-neutral-700 border border-neutral-600 text-neutral-100 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 placeholder-neutral-500 transition-colors duration-200 ease-in-out dark:bg-neutral-700 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                :aria-invalid="isInvalid ? 'true' : null"
                :aria-describedby="isInvalid ? `${props.id}-error` : null"
            />
            <div v-if="isInvalid" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
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
