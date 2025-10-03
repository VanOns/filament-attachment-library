window.fal = window.fal || {};

window.fal.__ = (key, fallback = '') => {
    const parts = key.split('.');
    let result = window?.filamentData?.fal || {};

    for (const part of parts) {
        if (result && typeof result === 'object' && part in result) {
            result = result[part];
        } else {
            return fallback || key;
        }
    }

    return result;
};
