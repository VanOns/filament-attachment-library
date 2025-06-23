window.fml = window.fml || {};

window.fml.__ = (key, fallback = '') => {
    const parts = key.split('.');
    let result = window?.filamentData?.fml || {};

    for (const part of parts) {
        if (result && typeof result === 'object' && part in result) {
            result = result[part];
        } else {
            return fallback || key;
        }
    }

    return result;
};
