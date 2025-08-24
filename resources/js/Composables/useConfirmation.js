import Swal from 'sweetalert2'

/**
 * Composable para gerenciar diálogos de confirmação usando SweetAlert2.
 *
 * @returns {{confirmAction: Function}}
 */
export default function useConfirmation() {
    /**
     * Exibe um diálogo de confirmação SweetAlert2 e executa um callback se confirmado.
     *
     * @param {object} config
     * @param {('success'|'error'|'warning'|'info'|'question')} config.icon
     * @param {string} config.title
     * @param {string} config.text
     * @param {string} [config.confirmButtonText='Confirmar'] 
     * @param {('success'|'danger'|'warning'|'info'|'primary'|'neutral')} [config.confirmButtonColor='primary']
     * @param {Function} onConfirmed 
     */
    const confirmAction = (config, onConfirmed) => {
        const buttonColors = {
            success: 'bg-green-600 hover:bg-green-700',
            danger: 'bg-red-600 hover:bg-red-700',
            warning: 'bg-amber-600 hover:bg-amber-700',
            info: 'bg-blue-600 hover:bg-blue-700',
            primary: 'bg-blue-600 hover:bg-blue-700',
            neutral: 'bg-neutral-600 hover:bg-neutral-700',
        };

        Swal.fire({
            title: config.title,
            text: config.text,
            icon: config.icon,
            showCancelButton: true,
            confirmButtonText: config.confirmButtonText || 'Confirmar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'swal2-dark',
                confirmButton: `${buttonColors[config.confirmButtonColor || 'primary']} text-white font-bold py-2 px-4 rounded`,
                cancelButton: 'bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ms-2',
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                onConfirmed();
            }
        });
    };

    return {
        confirmAction,
    };
}