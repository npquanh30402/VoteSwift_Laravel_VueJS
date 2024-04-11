import * as DOMPurify from 'dompurify/dist/purify.min.js';

export const useHelper = () => {
    const removeSpecialCharacters = (str) => {
        return str.replace(/[^a-zA-Z0-9\s]/g, '');
    }

    const sanitizeAndTrim = (str) => {
        return DOMPurify.sanitize(str.trim())
    }

    const extractFileName = (filePath) => {
        const parts = filePath.split('/');

        return parts[parts.length - 1];
    }

    const truncateFileName = (fileName, maxLength) => {
        if (fileName.length <= maxLength) {
            return fileName;
        } else {
            return '...' + fileName.substr(fileName.length - maxLength);
        }
    }

    return {removeSpecialCharacters, sanitizeAndTrim, extractFileName, truncateFileName}
}
