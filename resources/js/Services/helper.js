import * as DOMPurify from 'dompurify/dist/purify.min.js';

export const useHelper = () => {
    const removeSpecialCharacters = (str) => {
        return str.replace(/[^a-zA-Z0-9\s]/g, '');
    }

    const sanitizeAndTrim = (str) => {
        return DOMPurify.sanitize(str.trim())
    }

    return {removeSpecialCharacters, sanitizeAndTrim}
}
