import Chart from "chart.js/auto";
import {
    Activity,
    Building2,
    CreditCard,
    TicketCheck,
    UserRound,
    createIcons,
} from "lucide";
import { apiRequest, clearSession, getToken } from "../api/client";

const statusOrder = [
    "completed",
    "confirmed",
    "pending",
    "cancelled",
    "rejected",
];
const statusLabels = {
    completed: "Completed",
    confirmed: "Confirmed",
    pending: "Pending",
    cancelled: "Cancelled",
    rejected: "Rejected",
};
const statusColors = ["#5b7f2b", "#0f4c5c", "#f0a93a", "#a8463f", "#566a71"];
const numberFormat = new Intl.NumberFormat("en-JO", {
    maximumFractionDigits: 0,
});
const moneyFormat = new Intl.NumberFormat("en-JO", {
    style: "currency",
    currency: "JOD",
    minimumFractionDigits: 3,
    maximumFractionDigits: 3,
});
const compactMoneyFormat = new Intl.NumberFormat("en-JO", {
    notation: "compact",
    maximumFractionDigits: 1,
});
Chart.defaults.font.family = '"IBM Plex Sans", "IBM Plex Sans Arabic", sans-serif';

let revenueChart;
let statusChart;
let dashboardRequestId = 0;
let dashboardController;
let lastLoadedPeriod = null;
let hasLoadedDashboard = false;
let initialized = false;
let dashboardMessageTimer;
const periodLabels = new Map([
    ["today", "Today"],
    ["7d", "Last 7 days"],
    ["30d", "Last 30 days"],
    ["90d", "Last 90 days"],
    ["this_month", "This month"],
]);
const revenueDateFormat = new Intl.DateTimeFormat("en-JO", {
    month: "short",
    day: "numeric",
    timeZone: "UTC",
});

function formatRevenueDate(value) {
    const date = new Date(`${value}T00:00:00Z`);
    return Number.isNaN(date.getTime()) ? value : revenueDateFormat.format(date);
}

function node(selector) {
    return document.querySelector(selector);
}

function setVisible(selector, visible) {
    node(selector)?.classList.toggle("hidden", !visible);
}

function userFromResponse(response) {
    return response?.data || response;
}

function setIdentity(user) {
    const fullName =
        [user.first_name, user.last_name].filter(Boolean).join(" ") ||
        user.email ||
        "JST Admin";
    const initial = fullName.trim().charAt(0).toLocaleUpperCase() || "J";

    document.querySelectorAll("[data-user-name]").forEach((element) => {
        element.textContent = fullName;
    });
    document.querySelectorAll("[data-user-initial]").forEach((element) => {
        element.textContent = initial;
    });
    document.querySelectorAll("[data-user-role]").forEach((element) => {
        element.textContent = "System administrator";
    });
}

function setMessage(message = "", kind = "info", duration = 0) {
    const element = node("[data-dashboard-message]");
    if (!element) return;
    window.clearTimeout(dashboardMessageTimer);
    element.textContent = message;
    element.classList.toggle("dashboard-message--error", kind === "error");
    element.classList.toggle("hidden", !message);
    if (message && duration > 0) {
        dashboardMessageTimer = window.setTimeout(() => {
            element.classList.add("hidden");
            element.textContent = "";
        }, duration);
    }
}

function showError(title, description) {
    setVisible("[data-dashboard-loading]", false);
    setVisible("[data-dashboard-data]", false);
    setVisible("[data-dashboard-error]", true);
    node("[data-error-title]").textContent = title;
    node("[data-error-description]").textContent = description;
}

function formatPeriodRange(range) {
    if (!range?.start || !range?.end) return "Data for the selected period";
    const asDate = (value) => new Date(`${value.slice(0, 10)}T00:00:00Z`);
    const start = asDate(range.start);
    const end = asDate(range.end);
    const formatter = new Intl.DateTimeFormat("en-JO", {
        month: "short",
        day: "numeric",
        year: "numeric",
        timeZone: "UTC",
    });
    return `${formatter.format(start)} – ${formatter.format(end)}`;
}

function renderKpis(kpis) {
    document.querySelectorAll("[data-kpi]").forEach((element) => {
        const value = Number(kpis[element.dataset.kpi] ?? 0);
        element.textContent = numberFormat.format(
            Number.isFinite(value) ? value : 0,
        );
    });

    document.querySelectorAll("[data-kpi-money]").forEach((element) => {
        const value = Number(kpis[element.dataset.kpiMoney] ?? 0);
        element.textContent = moneyFormat.format(
            Number.isFinite(value) ? value : 0,
        );
    });
}

function renderRevenue(chartData) {
    const labels = Array.isArray(chartData?.labels) ? chartData.labels : [];
    const valuesForLabels = (values) =>
        labels.map((_, index) => {
            const value = Number(values?.[index] ?? 0);
            return Number.isFinite(value) ? value : 0;
        });
    const gmv = valuesForLabels(chartData?.bookingGMV);
    const commission = valuesForLabels(chartData?.platformCommission);
    const hasRevenue = [...gmv, ...commission].some((value) => value > 0);
    const canvas = node("[data-revenue-chart]");
    const empty = node("[data-revenue-empty]");

    const legend = node(".revenue-panel .chart-legend");
    node(".revenue-panel .chart-wrap")?.classList.toggle("is-empty", !hasRevenue);
    legend?.classList.toggle("hidden", !hasRevenue);
    canvas.classList.toggle("hidden", !hasRevenue);
    empty.classList.toggle("hidden", hasRevenue);
    if (!revenueChart && hasRevenue) {
        revenueChart = new Chart(canvas, {
            type: "line",
            data: {
                labels: [],
                datasets: [
                    {
                        label: "Booking GMV",
                        data: [],
                        borderColor: "#0f4c5c",
                        backgroundColor: "rgba(15, 76, 92, 0.08)",
                        fill: true,
                        tension: 0.28,
                        borderWidth: 2.25,
                        pointRadius: 1.75,
                        pointHoverRadius: 5,
                    },
                    {
                        label: "Platform commission",
                        data: [],
                        borderColor: "#d79224",
                        backgroundColor: "rgba(240, 169, 58, 0.08)",
                        fill: false,
                        tension: 0.28,
                        borderWidth: 2,
                        pointRadius: 1.5,
                        pointHoverRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? 0 : 220 },
                interaction: { intersect: false, mode: "index" },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) =>
                                `${context.dataset.label}: ${moneyFormat.format(Number(context.raw || 0))}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            color: "#718185",
                            maxTicksLimit: 7,
                            maxRotation: 0,
                            autoSkip: true,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: "rgba(25, 55, 61, 0.08)" },
                        ticks: {
                            color: "#718185",
                            callback: (value) =>
                                value === 0 ? "0" : compactMoneyFormat.format(value),
                        },
                    },
                },
            },
        });
    }

    if (revenueChart) {
        revenueChart.data.labels = labels.map(formatRevenueDate);
        revenueChart.data.datasets[0].data = gmv;
        revenueChart.data.datasets[1].data = commission;
        revenueChart.update("none");
    }
}

function renderStatuses(statuses) {
    const values = statusOrder.map((key) => {
        const value = Number(statuses?.[key] ?? 0);
        return Number.isFinite(value) ? Math.max(0, value) : 0;
    });
    const total = values.reduce((sum, value) => sum + value, 0);
    const canvas = node("[data-status-chart]");
    const empty = node("[data-status-empty]");
    node(".status-chart-wrap")?.classList.toggle("is-empty", total === 0);
    node("[data-booking-total]").textContent = numberFormat.format(total);
    canvas.classList.toggle("hidden", total === 0);
    empty.classList.toggle("hidden", total > 0);

    node(".status-chart-center")?.classList.toggle("hidden", total === 0);
    if (!statusChart && total > 0) {
        statusChart = new Chart(canvas, {
            type: "doughnut",
            data: {
                labels: statusOrder.map((status) => statusLabels[status]),
                datasets: [
                    {
                        data: values,
                        backgroundColor: statusColors,
                        borderColor: "#fff",
                        borderWidth: 3,
                        hoverOffset: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "76%",
                animation: { duration: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? 0 : 220 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) =>
                                `${context.label}: ${numberFormat.format(context.raw)}`,
                        },
                    },
                },
            },
        });
    }

    if (statusChart) {
        statusChart.data.datasets[0].data = values;
        statusChart.update("none");
    }

    const legend = node("[data-status-legend]");
    legend.replaceChildren();
    legend.classList.toggle("hidden", total === 0);
    statusOrder.forEach((status, index) => {
        const item = document.createElement("li");
        const label = document.createElement("span");
        const marker = document.createElement("i");
        const value = document.createElement("strong");
        marker.className = "legend-dot";
        marker.style.backgroundColor = statusColors[index];
        label.append(
            marker,
            document.createTextNode(statusLabels[status]),
        );
        value.textContent = numberFormat.format(values[index]);
        item.append(label, value);
        legend.append(item);
    });
}

function renderProviders(providers) {
    const body = node("[data-provider-rows]");
    const empty = node("[data-provider-empty]");
    const table = node("[data-provider-table-wrap]");
    body.replaceChildren();
    const rows = Array.isArray(providers) ? providers : [];

    rows.forEach((provider, index) => {
        const row = document.createElement("tr");
        const nameCell = document.createElement("td");
        const nameWrap = document.createElement("div");
        const initials = document.createElement("span");
        const name = document.createElement("strong");
        const count = document.createElement("td");
        const gmv = document.createElement("td");
        nameWrap.className = "provider-name";
        initials.className = `provider-initial provider-initial--${index % 4}`;
        initials.textContent = String(provider.name || "P")
            .trim()
            .charAt(0)
            .toLocaleUpperCase();
        name.textContent = provider.name || "Provider";
        nameWrap.append(initials, name);
        nameCell.append(nameWrap);
        count.textContent = numberFormat.format(
            Number(provider.totalBookings || 0),
        );
        gmv.textContent = moneyFormat.format(Number(provider.totalGMV || 0));
        row.append(nameCell, count, gmv);
        body.append(row);
    });

    table.classList.toggle("hidden", rows.length === 0);
    empty.classList.toggle("hidden", rows.length > 0);
}

function renderActivities(activities) {
    const list = node("[data-activity-list]");
    const empty = node("[data-activity-empty]");
    list.replaceChildren();
    const items = Array.isArray(activities) ? activities : [];
    const icons = {
        booking: "ticket-check",
        payment: "credit-card",
        user: "user-round",
        provider: "building-2",
    };

    items.forEach((activity) => {
        const item = document.createElement("li");
        const mark = document.createElement("span");
        const icon = document.createElement("i");
        const content = document.createElement("span");
        const title = document.createElement("strong");
        const description = document.createElement("small");
        const date = document.createElement("time");
        mark.className = `activity-mark activity-mark--${icons[activity.type] ? activity.type : "user"}`;
        icon.setAttribute("data-lucide", icons[activity.type] || "activity");
        icon.setAttribute("aria-hidden", "true");
        mark.append(icon);
        content.className = "activity-copy";
        title.textContent = activity.title || "Platform update";
        description.textContent = activity.description || "";
        content.append(title, description);
        const activityDate = activity.createdAt
            ? new Date(`${activity.createdAt.replace(" ", "T")}Z`)
            : null;
        date.textContent = activityDate && !Number.isNaN(activityDate.getTime())
            ? new Intl.DateTimeFormat("en-JO", {
                  month: "short",
                  day: "numeric",
                  timeZone: "Asia/Amman",
              }).format(activityDate)
            : "";
        item.append(mark, content, date);
        list.append(item);
    });

    createIcons({
        icons: { Activity, Building2, CreditCard, TicketCheck, UserRound },
        root: list,
        attrs: { "stroke-width": 1.75 },
    });

    empty.classList.toggle("hidden", items.length > 0);
}

function renderAlerts(alerts) {
    const alert = Array.isArray(alerts) ? alerts[0] : null;
    setVisible("[data-alerts-panel]", Boolean(alert));
    if (!alert) return;
    node("[data-alert-title]").textContent = alert.title || "Platform alert";
    node("[data-alert-description]").textContent = alert.description || "";
    node("[data-alert-count]").textContent = numberFormat.format(
        Number(alert.count || 0),
    );
}

function renderDashboard(response) {
    const data = response?.data || response;
    renderKpis(data.kpis || {});
    renderRevenue(data.revenue_chart || {});
    renderStatuses(data.booking_statuses || {});
    renderProviders(data.top_providers || []);
    renderActivities(data.recent_activities || []);
    renderAlerts(data.alerts || []);
    node("[data-date-range]").textContent = formatPeriodRange(data.date_range);
}

function setSelectedPeriod(value) {
    const input = node("[data-period]");
    input.value = value;
    node("[data-period-label]").textContent = periodLabels.get(value) || periodLabels.get("30d");
    document.querySelectorAll("[data-period-option]").forEach((option) => {
        const selected = option.dataset.periodOption === value;
        option.classList.toggle("is-selected", selected);
        option.setAttribute("aria-checked", String(selected));
    });
}

async function loadDashboard(force = false) {
    const period = node("[data-period]").value;
    if (!force && hasLoadedDashboard && period === lastLoadedPeriod) return;

    dashboardController?.abort();
    const controller = new AbortController();
    dashboardController = controller;
    const requestId = ++dashboardRequestId;
    const dashboardData = node("[data-dashboard-data]");

    setVisible("[data-dashboard-error]", false);
    if (hasLoadedDashboard) {
        dashboardData.classList.add("is-updating");
        dashboardData.setAttribute("aria-busy", "true");
        setMessage(`Updating ${periodLabels.get(period)?.toLowerCase() || "dashboard"}…`);
    } else {
        setVisible("[data-dashboard-loading]", true);
        setVisible("[data-dashboard-data]", false);
    }

    try {
        const response = await apiRequest(
            `/admin/dashboard?period=${encodeURIComponent(period)}`,
            { authenticated: true, signal: controller.signal },
        );
        if (requestId !== dashboardRequestId) return;
        renderDashboard(response);
        lastLoadedPeriod = period;
        hasLoadedDashboard = true;
        setVisible("[data-dashboard-loading]", false);
        setVisible("[data-dashboard-data]", true);
        dashboardData.classList.remove("is-updating");
        dashboardData.removeAttribute("aria-busy");
        setMessage();
        requestAnimationFrame(() => {
            revenueChart?.resize();
            statusChart?.resize();
        });
    } catch (error) {
        if (requestId !== dashboardRequestId || error.name === "AbortError") return;
        if (error.status === 401) return;
        if (error.status === 403) {
            window.location.assign("/workspace");
            return;
        }
        if (hasLoadedDashboard) {
            setSelectedPeriod(lastLoadedPeriod);
            dashboardData.classList.remove("is-updating");
            dashboardData.removeAttribute("aria-busy");
            setMessage(`Couldn’t load ${periodLabels.get(period)}. Showing ${periodLabels.get(lastLoadedPeriod)}.`, "error", 6000);
        } else {
            showError(
                error.status === 0
                    ? "You appear to be offline."
                    : "We couldn’t load the dashboard.",
                error.message || "Check your connection and try again.",
            );
        }
    } finally {
        if (requestId === dashboardRequestId) dashboardController = null;
    }
}

function bindPeriodControl() {
    const control = node("[data-period-control]");
    const trigger = node("[data-period-trigger]");
    const menu = node("#dashboard-period-options");
    const input = node("[data-period]");
    const options = Array.from(menu.querySelectorAll("[data-period-option]"));

    const closeMenu = (returnFocus = false) => {
        menu.hidden = true;
        trigger.setAttribute("aria-expanded", "false");
        if (returnFocus) trigger.focus();
    };

    const openMenu = (focusSelected = true) => {
        menu.hidden = false;
        trigger.setAttribute("aria-expanded", "true");
        const active = options.find((option) => option.dataset.periodOption === input.value) || options[0];
        if (focusSelected) active.focus();
    };

    const focusRelativeOption = (direction) => {
        const current = options.indexOf(document.activeElement);
        const next = (current + direction + options.length) % options.length;
        options[next].focus();
    };

    trigger.addEventListener("click", () => {
        if (menu.hidden) openMenu();
        else closeMenu();
    });
    trigger.addEventListener("keydown", (event) => {
        if (event.key === "ArrowDown" || event.key === "ArrowUp") {
            event.preventDefault();
            openMenu();
        }
    });
    menu.addEventListener("click", (event) => {
        const option = event.target.closest("[data-period-option]");
        if (!option) return;
        const value = option.dataset.periodOption;
        const changed = value !== input.value;
        setSelectedPeriod(value);
        closeMenu(true);
        if (changed) loadDashboard();
    });
    menu.addEventListener("keydown", (event) => {
        if (event.key === "ArrowDown") {
            event.preventDefault();
            focusRelativeOption(1);
        } else if (event.key === "ArrowUp") {
            event.preventDefault();
            focusRelativeOption(-1);
        } else if (event.key === "Home" || event.key === "End") {
            event.preventDefault();
            options[event.key === "Home" ? 0 : options.length - 1].focus();
        } else if (event.key === "Escape") {
            event.preventDefault();
            closeMenu(true);
        } else if (event.key === "Tab") {
            window.setTimeout(() => {
                if (!control.contains(document.activeElement)) closeMenu();
            }, 0);
        }
    });
    document.addEventListener("pointerdown", (event) => {
        if (!control.contains(event.target)) closeMenu();
    });
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !menu.hidden) closeMenu(true);
    });
}

function setExportStatus(message) {
    node("[data-export-status]").textContent = message;
}

async function exportCsv() {
    const button = node("[data-export]");
    const label = node("[data-export-label]");
    const period = node("[data-period]").value;
    const token = getToken();

    if (!token) return;
    button.disabled = true;
    button.setAttribute("aria-busy", "true");
    label.textContent = "Preparing CSV…";
    setExportStatus("Preparing the selected period export.");

    try {
        const response = await fetch(
            `${document.body.dataset.apiBase}/admin/dashboard/export?period=${encodeURIComponent(period)}`,
            {
                headers: { Accept: "text/csv", Authorization: `Bearer ${token}` },
                credentials: "omit",
            },
        );

        if (response.status === 401) {
            clearSession();
            window.dispatchEvent(new CustomEvent("jst:unauthorized"));
            return;
        }
        if (!response.ok) throw new Error("The CSV export could not be prepared.");

        const blob = await response.blob();
        const disposition = response.headers.get("content-disposition") || "";
        const filename = disposition.match(/filename="?([^";]+)"?/i)?.[1] || "jst-bookings.csv";
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = filename;
        document.body.append(link);
        link.click();
        link.remove();
        window.setTimeout(() => URL.revokeObjectURL(url), 1000);
        setExportStatus(`${periodLabels.get(period)} CSV downloaded.`);
        setMessage(`${periodLabels.get(period)} CSV downloaded.`, "info", 3500);
    } catch (error) {
        setExportStatus("CSV export failed.");
        setMessage(error.message || "CSV export failed. Try again.", "error", 6000);
    } finally {
        button.disabled = false;
        button.removeAttribute("aria-busy");
        label.textContent = "Export CSV";
    }
}

function bindControls() {
    node("[data-export]")?.addEventListener("click", exportCsv);
    node("[data-retry]")?.addEventListener("click", () => loadDashboard(true));

    document.querySelectorAll("[data-logout]").forEach((button) => {
        button.addEventListener("click", async () => {
            try {
                await apiRequest("/logout", {
                    method: "POST",
                    authenticated: true,
                });
            } catch {
                clearSession();
            }
            clearSession();
            window.location.assign("/");
        });
    });

    window.addEventListener("jst:unauthorized", () =>
        window.location.assign("/login"),
    );
}

export async function initAdminDashboard() {
    if (initialized) return;
    initialized = true;
    bindPeriodControl();
    bindControls();

    if (!getToken()) {
        window.location.assign("/login");
        return;
    }

    try {
        const response = await apiRequest("/user", { authenticated: true });
        const user = userFromResponse(response);

        if (user.role !== "system_admin") {
            window.location.assign(
                user.role === "content_admin" || user.role === "provider"
                    ? "/workspace"
                    : "/",
            );
            return;
        }

        setIdentity(user);
        node("[data-dashboard-app]").classList.remove("hidden");
        setVisible("[data-dashboard-gate]", false);
        await loadDashboard();
    } catch (error) {
        if (error.status !== 401) {
            node("[data-dashboard-gate]").innerHTML =
                '<span class="dashboard-gate__mark">J</span><p>We couldn’t confirm your JST account.</p><a class="button button--outline" href="/login">Return to log in</a>';
        }
    }
}
