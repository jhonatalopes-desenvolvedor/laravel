<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    name: { type: String, default: '' },
    items: { type: Array, default: () => [] },
})
</script>

<template>
    <div class="hs-dropdown [--strategy:static] sm:[--strategy:fixed] [--adaptive:none] sm:[--adaptive:adaptive]">
        <button
            id="hs-navbar-example-dropdown"
            type="button"
            class="hs-dropdown-toggle flex items-center w-full text-neutral-600 hover:text-neutral-400 focus:outline-hidden focus:text-neutral-400 font-medium dark:text-neutral-400 dark:hover:text-neutral-500 dark:focus:text-neutral-500"
            aria-haspopup="menu"
            aria-expanded="false"
            aria-label="Mega Menu"
        >
            <div class="flex items-center gap-2 flex-1">
                <span>{{ name }}</span>
            </div>

            <svg class="hs-dropdown-open:-rotate-180 sm:hs-dropdown-open:rotate-0 duration-300 ms-1 shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6" />
            </svg>
        </button>

        <div
            class="hs-dropdown-menu transition-[opacity,margin] ease-in-out duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 sm:w-48 z-10 bg-white sm:shadow-md rounded-lg p-1 space-y-1 dark:bg-neutral-800 sm:dark:border dark:border-neutral-700 dark:divide-neutral-700 before:absolute top-full sm:border border-neutral-200 before:-top-5 before:start-0 before:w-full before:h-5 hidden"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="hs-navbar-example-dropdown"
        >
            <template v-for="item in items" :key="item.text">
                <Link
                    :href="item.href"
                    class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-neutral-800 hover:bg-neutral-100 focus:outline-hidden focus:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300"
                >
                    {{ item.text }}
                </Link>
            </template>
        </div>
    </div>
</template>
