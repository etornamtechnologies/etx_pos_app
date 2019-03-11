
const TOKEN_KEY = "api_token";

export function getApiToken() {
    return localStorage.getItem(TOKEN_KEY) || null;
}

export function setApiToken(token) {
    return localStorage.setItem(TOKEN_KEY, token)
}

export function removeApiToken() {
    if(localStorage.getItem(TOKEN_KEY)) {
        console.log('token exist');
        return localStorage.removeItem(TOKEN_KEY);
    }
}