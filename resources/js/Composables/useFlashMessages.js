import { useToast } from 'vue-toastification'
import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import Toast from '@/Components/Feedback/Toast.vue' 

/**
 * Composable para gerenciar e exibir mensagens flash globais do Inertia usando Vue-Toastification.
 *
 * @returns {{showSuccess: Function, showError: Function, showWarning: Function}}
 */
export default function useFlashMessages() {
    const toast = useToast()
    const page = usePage()

    watch(
        () => page.props.flash?.notification,
        (flashNotification) => {
            if (flashNotification) {
                toast({
                    component: Toast,
                    props: {
                        type: flashNotification.type,
                        text: flashNotification.text,
                    },
                }, {
                    hideProgressBar: true,
                    toastClassName: 'my-custom-toast-class', 
                    icon: false,
                    closeButton: false,
                    timeout: 3000,
                });

                page.props.flash.notification = null;
            }
        },
        { immediate: true } 
    );

    /**
     * Exibe uma mensagem de sucesso.
     * 
     * @param {string} text 
     */
    const showSuccess = (text) => {
        toast({
            component: Toast,
            props: { type: 'success', text: text },
        }, {
            hideProgressBar: true,
            toastClassName: 'my-custom-toast-class',
            icon: false,
            closeButton: false,
            timeout: 3000,
        });
    };

    /**
     * Exibe uma mensagem de erro.
     * 
     * @param {string} text 
     */
    const showError = (text) => {
        toast({
            component: Toast,
            props: { type: 'error', text: text },
        }, {
            hideProgressBar: true,
            toastClassName: 'my-custom-toast-class',
            icon: false,
            closeButton: false,
            timeout: 3000,
        });
    };

    /**
     * Exibe uma mensagem de aviso.
     * 
     * @param {string} text 
     */
    const showWarning = (text) => {
        toast({
            component: Toast,
            props: { type: 'warning', text: text },
        }, {
            hideProgressBar: true,
            toastClassName: 'my-custom-toast-class',
            icon: false,
            closeButton: false,
            timeout: 3000,
        });
    };

    return {
        showSuccess,
        showError,
        showWarning,
    };
}