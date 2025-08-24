<script setup>
import { Link } from '@inertiajs/vue3'
import Icon from '@/Components/Base/Icon.vue'

/**
 * Componente de barra lateral genérico.
 *
 * @property {Object<{href: string, label: string, icon: string, active: boolean}>} menuItems
 */
defineProps({
    menuItems: {
        type: Object,
        default: () => [],
    },
})
</script>

<template>
    <aside class="fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform -translate-x-full md:translate-x-0 bg-neutral-800" aria-label="Sidenav" id="drawer-navigation">
        <div class="overflow-y-auto h-full">
            <div class="relative flex flex-col h-full max-h-full">
                <nav
                    class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-neutral-100 [&::-webkit-scrollbar-thumb]:bg-neutral-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500"
                >
                    <div class="hs-accordion-group pb-0 px-4 w-full flex flex-col flex-wrap mt-4" data-hs-accordion-always-open>
                        <ul class="space-y-1">
                            <li v-for="(item, index) in menuItems" :key="index">
                                <Link :href="item.href" class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg focus:outline-none transition-colors duration-150" :class="item.active ? 'bg-neutral-700 text-white' : 'text-neutral-300 hover:bg-neutral-700/50 hover:text-white'">
                                    <Icon :name="item.icon" class="size-5" />
                                    <span>{{ item.label }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </nav>

                <footer v-if="$slots.footer" class="mt-auto px-2 py-2 border-t border-neutral-200 dark:border-neutral-700">
                    <slot name="footer" />
                </footer>
            </div>
        </div>
    </aside>
</template>
