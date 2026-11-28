import {
    apiRequest,
    clearSession,
    getStoredUser,
    getToken,
    storeSession,
} from "./client";

export async function login(credentials) {
    const response = await apiRequest("/login", {
        method: "POST",
        body: credentials,
    });
    storeSession(response.token, response.user);
    return response.user;
}

export async function register(details) {
    return apiRequest("/register", { method: "POST", body: details });
}

export async function sendPasswordReset(email) {
    return apiRequest("/forgot-password", { method: "POST", body: { email } });
}

export async function logout() {
    try {
        if (getToken())
            await apiRequest("/logout", {
                method: "POST",
                authenticated: true,
            });
    } finally {
        clearSession();
        window.location.assign("/");
    }
}

export function getCurrentUser() {
    return getStoredUser();
}

export function hasSession() {
    return Boolean(getToken());
}

export function redirectForRole(user) {
    if (user.role === "system_admin") {
        window.location.assign("/admin/dashboard");
        return;
    }

    if (user.role === "content_admin" || user.role === "provider") {
        window.location.assign("/workspace");
        return;
    }

    window.location.assign("/");
}
