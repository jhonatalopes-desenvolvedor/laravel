import { useForm as usePrecognitionForm } from 'laravel-precognition-vue-inertia'
import { usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import Toast from '@/Components/Feedback/Toast.vue' 

/**
 * Composable para gerenciar formulários Inertia com validação Precognition e feedback de toast.
 *
 * @param {string} method 
 * @param {string} url
 * @param {object} data 
 * @returns {object} 
 */
export default function useFormValidation(method, url, data) {
    const page = usePage()
    const toast = useToast()

    const form = usePrecognitionForm(method, url, data)

    form.setValidationTimeout(150) 

    const originalSubmit = form.submit

    form.submit = (options = {}) => {
        return originalSubmit({
            ...options,
            onSuccess: (inertiaPage) => {
                if (options.resetOnSuccess !== false) {
                    form.reset()
                }

                if (typeof options.onSuccess === 'function') {
                    options.onSuccess(inertiaPage)
                }
            },
            onError: (errors) => {
                const generalError = errors.general || errors.message; 

                if (generalError) {
                    toast({
                        component: Toast,
                        props: {
                            type: 'error',
                            text: generalError,
                        },
                    }, {
                        hideProgressBar: true,
                        toastClassName: 'my-custom-toast-class',
                        icon: false,
                        closeButton: false,
                        timeout: 3000,
                    });
                }

                if (typeof options.onError === 'function') {
                    options.onError(errors)
                }
            }
        })
    }

    return form
}