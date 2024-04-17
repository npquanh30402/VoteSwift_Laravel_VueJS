import * as DOMPurify from "dompurify/dist/purify.min.js";
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

    const truncateText = (text, maxLength) => {
        return text.length <= maxLength
            ? text.substr(text.length - maxLength) + text
            : "...";
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

    function formatHour(date) {
        return new Date(date).toLocaleString("en-US", {
            hour: "numeric",
            minute: "numeric",
            hour12: true,
        });
    }

    const generateUniqueKey = () => {
        return `${Date.now()}-${Math.floor(Math.random() * 1000000)}`;
    };

    const isImage = (file) => {
        const extension = file.split(".").pop().toLowerCase();

        const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp"];

        return imageExtensions.includes(extension);
    };

    const formatWithThreshold = (count, threshold) => {
        return count > threshold ? `${threshold}+` : count.toString();
    };

    return {
        removeSpecialCharacters,
        sanitizeAndTrim,
        extractFileName,
        truncateFileName,
        formatHour,
        formattedDate,
        truncateText,
        generateUniqueKey,
        isImage,
        formatWithThreshold,
    };
};
