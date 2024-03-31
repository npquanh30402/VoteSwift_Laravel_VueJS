export default function useFormattedDate() {
    return (date) => {
        return new Date(date).toLocaleString([], {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };
}
