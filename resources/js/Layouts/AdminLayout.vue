<script setup>
import { computed } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'

import Navbar from '@/Components/Layout/Navbar.vue'
import Sidebar from '@/Components/Layout/Sidebar.vue'
import NavbarBrand from '@/Components/Layout/NavbarBrand.vue'
import NavbarUserDropdown from '@/Components/Layout/NavbarUserDropdown.vue'

const page = usePage()
const adminMenuItems = computed(() => page.props.area?.admin?.sidebar || [])
const user = computed(() => page.props.users?.admin?.full_name || { full_name: 'Usuário' })
</script>

<template>
    <div class="antialiased bg-neutral-900">
        <Head>
            <title>{{ page.props.page?.title }}</title>
            <meta v-if="page.props.page?.description" name="description" :content="page.props.page.description" />
        </Head>

        <Navbar>
            <template #brand>
                <NavbarBrand text="Laravel" href="#" />
            </template>

            <template #user-menu>
                <NavbarUserDropdown
                    :name="user"
                    :items="[
                        { text: 'Alterar perfil', href: '#' },
                        { text: 'Alterar senha', href: '#' },
                        { text: 'Sair', href: '#' },
                    ]"
                />
            </template>
        </Navbar>

        <Sidebar :menuItems="adminMenuItems" />

        <main class="p-0 md:ml-64 h-auto pt-14.5">
            <slot />
        </main>
    </div>
</template>
