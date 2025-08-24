<template>
    <header class="bg-white border-b border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700 fixed left-0 right-0 top-0 z-50 flex">
        <nav class="w-full mx-auto sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center justify-between min-h-14 px-4">
                <div class="flex justify-start items-center gap-4">
                    <button
                        data-drawer-target="drawer-navigation"
                        data-drawer-toggle="drawer-navigation"
                        aria-controls="drawer-navigation"
                        class="text-neutral-600 px-2 py-2 border border-neutral-700 rounded-md cursor-pointer md:hidden hover:text-neutral-900 hover:bg-neutral-100 focus:bg-neutral-100 dark:focus:bg-neutral-700 focus:ring-2 focus:ring-neutral-100 dark:focus:ring-neutral-700 dark:text-neutral-100 dark:hover:bg-neutral-700 dark:hover:text-white"
                    >
                        <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <svg aria-hidden="true" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Toggle sidebar</span>
                    </button>

                    <slot name="brand"></slot>
                </div>

                <div class="sm:hidden">
                    <button
                        type="button"
                        class="hs-collapse-toggle relative size-9 flex justify-center items-center gap-x-2 rounded-lg border border-neutral-200 bg-white text-neutral-800 shadow-2xs hover:bg-neutral-50 focus:outline-hidden focus:bg-neutral-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-neutral-700 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10"
                        id="hs-navbar-header-collapse"
                        aria-expanded="false"
                        aria-controls="hs-navbar-header"
                        aria-label="Toggle navigation"
                        data-hs-collapse="#hs-navbar-header"
                    >
                        <svg class="hs-collapse-open:hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" x2="21" y1="6" y2="6" />
                            <line x1="3" x2="21" y1="12" y2="12" />
                            <line x1="3" x2="21" y1="18" y2="18" />
                        </svg>
                        <svg class="hs-collapse-open:block hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                        <span class="sr-only">Toggle navigation</span>
                    </button>
                </div>
            </div>

            <div id="hs-navbar-header" class="hidden hs-collapse sm:border-none border-t border-neutral-700 overflow-hidden transition-all duration-300 basis-full grow sm:block" aria-labelledby="hs-navbar-header-collapse">
                <div class="flex flex-col px-6 py-4 gap-5 sm:flex-row sm:items-center sm:justify-end">
                    <slot name="user-menu"></slot>
                </div>
            </div>
        </nav>
    </header>
</template>
