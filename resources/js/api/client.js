const TOKEN_KEY = "jst.access-token";
const USER_KEY = "jst.user";

export class ApiError extends Error {
    constructor(status, message, errors = {}) {
        super(message);
        this.name = "ApiError";
        this.status = status;
        this.errors = errors;
    }
}

export function getToken() {
    return window.sessionStorage.getItem(TOKEN_KEY);
}

export function getStoredUser() {
    try {
        const value = window.sessionStorage.getItem(USER_KEY);
        return value ? JSON.parse(value) : null;
    } catch {
        return null;
    }
}

export function storeSession(token, user) {
    window.sessionStorage.setItem(TOKEN_KEY, token);
    window.sessionStorage.setItem(USER_KEY, JSON.stringify(user));
}

export function clearSession() {
    window.sessionStorage.removeItem(TOKEN_KEY);
    window.sessionStorage.removeItem(USER_KEY);
}

function safeMessage(status, payload) {
    if (status >= 500)
        return "JST is having trouble right now. Please try again shortly.";
    if (status === 429)
        return "Too many attempts. Please wait a moment and try again.";
    if (status === 403)
        return "Your account does not have permission to view this page.";
    if (status === 404) return "We could not find the requested information.";
    if (status === 409)
        return (
            payload?.message ||
            "This request conflicts with the current account state."
        );
    if (status === 401) return "Your session has ended. Please log in again.";
    return payload?.message || "Please review the fields and try again.";
}

export async function apiRequest(
    path,
    { method = "GET", body, authenticated = false, signal } = {},
) {
    const headers = { Accept: "application/json" };

    if (body !== undefined) headers["Content-Type"] = "application/json";

    if (authenticated) {
        const token = getToken();
        if (!token) throw new ApiError(401, "Please log in to continue.");
        headers.Authorization = `Bearer ${token}`;
    }

    let response;

    try {
        response = await fetch(`${document.body.dataset.apiBase}${path}`, {
            method,
            headers,
            body: body === undefined ? undefined : JSON.stringify(body),
            credentials: "omit",
            signal,
        });
    } catch (error) {
        if (error.name === "AbortError") throw error;
        throw new ApiError(
            0,
            "We could not reach JST. Check your internet connection and try again.",
        );
    }

    let payload = null;
    const contentType = response.headers.get("content-type") || "";

    if (response.status !== 204 && contentType.includes("application/json")) {
        try {
            payload = await response.json();
        } catch {
            payload = null;
        }
    }

    if (!response.ok) {
        if (response.status === 401 && authenticated) {
            clearSession();
            window.dispatchEvent(new CustomEvent("jst:unauthorized"));
        }

        throw new ApiError(
            response.status,
            safeMessage(response.status, payload),
            payload?.errors || {},
        );
    }

    return payload;
}

export function fieldMessage(error, field) {
    return error.errors?.[field]?.[0] || "";
}
