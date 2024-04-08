export const useHelper = () => {
    const removeSpecialCharacters = (str) => {
        return str.replace(/[^a-zA-Z0-9\s]/g, '');
    }

    return {removeSpecialCharacters}
}
