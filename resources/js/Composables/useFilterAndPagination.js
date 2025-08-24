import { ref, watch, computed, nextTick } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3' 

/**
 * Composable para gerenciar filtros, busca e paginação de listagens.
 *
 * @param {object} initialFilters 
 * @param {string} routePath 
 * @param {Array<string>} preserveOnly 
 * @returns {{filterForm: object, applyFilters: Function, clearFilters: Function, showFilters: Ref<boolean>, hasFilters: Ref<boolean>}}
 */
export default function useFilterAndPagination(initialFilters, routePath, preserveOnly = []) {
    const page = usePage() 
    const showFilters = ref(false)
    let searchTimeout = null

    const filterForm = useForm(initialFilters)

    let ignoreWatch = false

    watch(() => page.props.filters, (newFilters) => {
        if (!newFilters) return; 
        ignoreWatch = true;
        for (const key in newFilters) {
            if (Object.prototype.hasOwnProperty.call(filterForm.data(), key)) {
                filterForm[key] = newFilters[key];
            }
        }
        nextTick(() => {
            ignoreWatch = false;
        });
    }, { deep: true, immediate: true });


    watch(
        () => ({ ...filterForm.data() }),
        (newVal, oldVal) => {
            if (ignoreWatch) {
                return;
            }

            if (newVal.search !== oldVal.search) {
                clearTimeout(searchTimeout)
                searchTimeout = setTimeout(() => {
                    applyFilters()
                }, 300)
            } else {
                applyFilters()
            }
        },
        { deep: true }
    )

    const applyFilters = () => {
        const data = {};
        for (const key in filterForm.data()) {
            if (filterForm[key] !== null && filterForm[key] !== undefined && filterForm[key] !== '') {
                data[key] = filterForm[key];
            }
        }

        router.get(routePath, data, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: [...preserveOnly, 'filters'],
        })
    }

    const clearFilters = () => {
        router.get(
            routePath,
            {},
            {
                preserveScroll: true,
                preserveState: false,
                replace: true,
                onFinish: () => {
                    filterForm.reset(); 
                    showFilters.value = false;
                },
            }
        )
    }

    const hasFilters = computed(() => {
        for (const key in initialFilters) {
            if (JSON.stringify(filterForm[key]) !== JSON.stringify(initialFilters[key])) {
                if(filterForm[key] !== '' && filterForm[key] !== null) {
                    return true;
                }
            }
        }
        return false;
    });

    return {
        filterForm,
        applyFilters,
        clearFilters,
        showFilters,
        hasFilters,
    }
}