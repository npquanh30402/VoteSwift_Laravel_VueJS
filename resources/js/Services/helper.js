import * as DOMPurify from "dompurify/dist/purify.min.js";
import { computed } from "vue";
import { formatDistanceToNow } from "date-fns/formatDistanceToNow";

export const useHelper = () => {
    const removeSpecialCharacters = (str) => {
        return str.replace(/[^a-zA-Z0-9\s]/g, "");
    };

    const sanitizeAndTrim = (str) => {
        return DOMPurify.sanitize(str.trim());
    };

    const extractFileName = (filePath) => {
        const parts = filePath.split("/");

        return parts[parts.length - 1];
    };

    const truncateFileName = (fileName, maxLength) => {
        if (fileName.length <= maxLength) {
            return fileName;
        } else {
            return "..." + fileName.substr(fileName.length - maxLength);
        }
    };

    const formattedDate = (date) => {
        return formatDistanceToNow(new Date(date), {
            includeSeconds: true,
            addSuffix: true,
        });
    };

    return {
        removeSpecialCharacters,
        sanitizeAndTrim,
        extractFileName,
        truncateFileName,
        formattedDate,
    };
};
