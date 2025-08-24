/**
 * Composable para formatar datas e horas.
 *
 * @returns {{formatDateTime: Function, formatDate: Function, formatTime: Function}}
 */
export default function useDateTimeFormatter() {
    /**
     * Formata uma string de data/hora para o formato local completo (pt-BR).
     * 
     * @param {string|Date|null} dateInput
     * @returns {string} 
     */
    const formatDateTime = (dateInput) => {
        if (!dateInput) return '-'
        const date = new Date(dateInput)
        if (isNaN(date.getTime())) return '-'
        const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }
        return date.toLocaleString('pt-BR', options)
    }

    /**
     * Formata uma string de data para o formato local de data (pt-BR).
     * 
     * @param {string|Date|null} dateInput 
     * @returns {string} 
     */
    const formatDate = (dateInput) => {
        if (!dateInput) return '-'
        const date = new Date(dateInput)
        if (isNaN(date.getTime())) return '-'
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' }
        return date.toLocaleDateString('pt-BR', options)
    }

    /**
     * Formata uma string de data/hora para o formato local de hora (pt-BR).
     * 
     * @param {string|Date|null} dateInput 
     * @returns {string} 
     */
    const formatTime = (dateInput) => {
        if (!dateInput) return '-'
        const date = new Date(dateInput)
        if (isNaN(date.getTime())) return '-'
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' }
        return date.toLocaleTimeString('pt-BR', options)
    }

    return {
        formatDateTime,
        formatDate,
        formatTime,
    }
}