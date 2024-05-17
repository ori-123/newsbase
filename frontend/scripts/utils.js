import {frontendLog} from "./log.js";

export function handleResponse(response) {
    if (!response.ok) {
        frontendLog('error', 'Network response not ok');
        throw new Error('Network response was not ok');
    }

    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
        return response.json();
    } else {
        return response.text();
    }
}
