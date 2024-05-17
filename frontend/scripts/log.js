import {handleResponse} from "./utils.js";

export function frontendLog(level, message) {
    const logPayload = {
        level: level,
        message: message
    }

    fetch('http://localhost:8000/api/user/frontendlogger.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(logPayload)
    }).then(handleResponse)
}
