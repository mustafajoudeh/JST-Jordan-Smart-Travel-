import {
    Activity,
    ArrowDownWideNarrow,
    Building2,
    CalendarDays,
    CircleAlert,
    Eye,
    Pencil,
    Plus,
    Search,
    Settings,
    ShieldCheck,
    Tags,
    Trash2,
    X,
    createIcons,
} from "lucide";
import { apiRequest, getToken } from "../api/client";

const iconSet = {
    Activity,
    ArrowDownWideNarrow,
    Building2,
    CalendarDays,
    CircleAlert,
    Eye,
    Pencil,
    Plus,
    Search,
    Settings,
    ShieldCheck,
    Tags,
    Trash2,
    X,
};

const modules = {
    governorates: {
        endpoint: "/admin/governorates",
        createLabel: "Add governorate",
        createdLabel: "Governorate created.",
        emptyTitle: "No governorates yet",
        emptyDescription: "Add the regions used to organize destinations and events.",
        searchDescription: "No governorates match these filters.",
        nameLabel: "Governorate name",
    },
    categories: {
        endpoint: "/admin/categories",
        createLabel: "Add category",
        createdLabel: "Category created.",
        emptyTitle: "No categories yet",
        emptyDescription: "Add a classification for tourism or service content.",
        searchDescription: "No categories match these filters.",
        nameLabel: "Category name",
    },
    "platform-settings": {
        endpoint: "/admin/platform-settings",
        createLabel: "Add setting",
        createdLabel: "Setting created.",
        emptyTitle: "No settings yet",
        emptyDescription: "Add a platform default when the application needs one.",
        searchDescription: "No settings match these filters.",
        nameLabel: "Setting name",
    },
};

const numberFormat = new Intl.NumberFormat("en-JO");
const dateFormat = new Intl.DateTimeFormat("en-JO", {
    dateStyle: "medium",
    timeZone: "Asia/Amman",
});
let initialized = false;
let toastTimer;

function node(selector, root = document) {
    return root.querySelector(selector);
}

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, (character) => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
    })[character]);
}

function recordKey(module, record) {
    return module === "platform-settings" ? String(record.key) : String(record.id);
}

function displayDate(value) {
    if (!value) return "—";
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? "—" : dateFormat.format(date);
}

function countValue(value) {
    return numberFormat.format(Number(value) || 0);
}

function activeBadge(isActive) {
    return `<span class="management-status management-status--${isActive ? "active" : "inactive"}">${isActive ? "Active" : "Inactive"}</span>`;
}

function categoryKind(kind) {
    return kind === "service" ? "Services" : "Tourism";
}

function valueDisplay(record) {
    if (record.is_sensitive) return record.has_value ? "Saved value hidden" : "Not set";
    if (record.value_type === "boolean") {
        return ["true", "1"].includes(String(record.value).toLowerCase()) ? "Enabled" : "Disabled";
    }
    return record.value === null || record.value === "" ? "—" : String(record.value);
}

function totalRelated(record) {
    return Object.values(record.related_counts || {}).reduce((total, count) => total + Number(count || 0), 0);
}

function canDelete(module, record) {
    if (module === "platform-settings") return !record.is_protected;
    return totalRelated(record) === 0;
}

function actionButtons(module, record) {
    const key = escapeHtml(recordKey(module, record));
    const name = escapeHtml(record.name || record.label || "record");
    return `<div class="management-row-actions">
        <button type="button" class="management-icon-button" data-action="view" data-record-key="${key}" aria-label="View ${name}" title="View details"><i data-lucide="eye" aria-hidden="true"></i></button>
        <button type="button" class="management-icon-button" data-action="edit" data-record-key="${key}" aria-label="Edit ${name}" title="Edit"><i data-lucide="pencil" aria-hidden="true"></i></button>
        <button type="button" class="management-icon-button management-icon-button--danger" data-action="delete" data-record-key="${key}" aria-label="Delete ${name}" title="${canDelete(module, record) ? "Delete" : "Record is in use"}" ${canDelete(module, record) ? "" : "disabled"}><i data-lucide="trash-2" aria-hidden="true"></i></button>
    </div>`;
}

function governorateRow(record) {
    const counts = record.related_counts || {};
    return `<tr>
        <td><div class="management-primary-cell"><strong>${escapeHtml(record.name)}</strong><span>${escapeHtml(record.description || "No description")}</span></div></td>
        <td>${countValue(counts.destinations)}</td>
        <td>${countValue(counts.events)}</td>
        <td>${countValue(counts.providers)}</td>
        <td>${activeBadge(record.is_active)}</td>
        <td>${actionButtons("governorates", record)}</td>
    </tr>`;
}

function categoryRow(record) {
    const counts = record.related_counts || {};
    return `<tr>
        <td><div class="management-primary-cell"><strong>${escapeHtml(record.name)}</strong><span>${escapeHtml(record.description || "No description")}</span></div></td>
        <td><span class="management-kind management-kind--${record.kind === "service" ? "service" : "tourism"}">${categoryKind(record.kind)}</span></td>
        <td>${countValue(counts.destinations)}</td>
        <td>${countValue((counts.activities || 0) + (counts.services || 0))}</td>
        <td>${activeBadge(record.is_active)}</td>
        <td>${actionButtons("categories", record)}</td>
    </tr>`;
}

function settingRow(record) {
    return `<tr>
        <td><div class="management-primary-cell"><strong>${escapeHtml(record.name)}</strong><span>${escapeHtml(record.description || "No description")}</span></div></td>
        <td><span class="management-group-label">${escapeHtml(record.group_label)}</span></td>
        <td><span class="management-type-label">${escapeHtml(record.value_type)}</span></td>
        <td><span class="management-value-preview" title="${escapeHtml(valueDisplay(record))}">${escapeHtml(valueDisplay(record))}</span></td>
        <td>${record.is_protected ? '<span class="management-scope management-scope--core"><i data-lucide="shield-check" aria-hidden="true"></i>Core</span>' : '<span class="management-scope">Custom</span>'}</td>
        <td>${actionButtons("platform-settings", record)}</td>
    </tr>`;
}

function dependencySummary(record) {
    const counts = record.related_counts || {};
    if (record.is_protected) return "This core setting is required by the platform.";
    if (Object.values(counts).some((count) => Number(count) > 0)) {
        const labels = {
            destinations: "destinations",
            events: "events",
            providers: "providers",
            activities: "activities",
            services: "services",
            travelers: "travelers with this interest",
        };
        return Object.entries(counts)
            .filter(([, count]) => Number(count) > 0)
            .map(([key, count]) => `${countValue(count)} ${labels[key] || key}`)
            .join(" · ");
    }
    return "No related records use this item.";
}

function detailRows(items) {
    return `<dl class="management-detail-grid">${items.map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${value}</dd></div>`).join("")}</dl>`;
}

function descriptionBlock(record) {
    return `<section class="management-detail-section"><h3>Description</h3><p>${escapeHtml(record.description || "No description provided.")}</p></section>`;
}

function relatedBlock(labels, counts) {
    return `<section class="management-detail-section"><h3>Related records</h3><div class="management-related-grid">${labels.map(([key, label]) => `<div><strong>${countValue(counts[key])}</strong><span>${escapeHtml(label)}</span></div>`).join("")}</div></section>`;
}

function dialogHead(title, eyebrow = "Record details") {
    return `<header class="management-dialog__header"><div><p>${escapeHtml(eyebrow)}</p><h2 id="management-dialog-title">${escapeHtml(title)}</h2></div><button type="button" class="management-icon-button" data-dialog-close aria-label="Close dialog"><i data-lucide="x" aria-hidden="true"></i></button></header>`;
}

function deleteAvailability(module, record) {
    if (canDelete(module, record)) {
        return `<button type="button" class="button button--danger" data-detail-delete>Delete</button>`;
    }
    return `<p class="management-protection-note"><i data-lucide="circle-alert" aria-hidden="true"></i>${escapeHtml(dependencySummary(record))}</p>`;
}

function detailMarkup(module, record) {
    let content = "";
    if (module === "governorates") {
        const counts = record.related_counts || {};
        content = `${detailRows([
            ["Status", activeBadge(record.is_active)],
            ["Last updated", escapeHtml(displayDate(record.updated_at))],
        ])}${descriptionBlock(record)}${relatedBlock([
            ["destinations", "Destinations"], ["events", "Events"], ["providers", "Providers"],
        ], counts)}`;
    } else if (module === "categories") {
        const counts = record.related_counts || {};
        content = `${detailRows([
            ["Type", `<span class="management-kind management-kind--${record.kind === "service" ? "service" : "tourism"}">${categoryKind(record.kind)}</span>`],
            ["Status", activeBadge(record.is_active)],
            ["Last updated", escapeHtml(displayDate(record.updated_at))],
        ])}${descriptionBlock(record)}${relatedBlock([
            ["destinations", "Destinations"], ["activities", "Activities"], ["services", "Services"],
            ["providers", "Providers"], ["travelers", "Travelers interested"],
        ], counts)}`;
    } else {
        const value = record.is_sensitive
            ? (record.has_value ? "Saved value is hidden from this view." : "No value is saved.")
            : escapeHtml(valueDisplay(record));
        content = `${detailRows([
            ["Group", escapeHtml(record.group_label)],
            ["Type", escapeHtml(record.value_type)],
            ["Current value", value],
            ["Scope", record.is_protected ? '<span class="management-scope management-scope--core">Core setting</span>' : '<span class="management-scope">Custom setting</span>'],
            ["Last updated", escapeHtml(displayDate(record.updated_at))],
        ])}${descriptionBlock(record)}${record.is_sensitive ? '<p class="management-field-hint">Sensitive values are never returned by the settings API.</p>' : ""}`;
    }

    return `${dialogHead(record.name)}<div class="management-dialog__body">${content}</div><footer class="management-dialog__footer management-dialog__footer--split"><div>${deleteAvailability(module, record)}</div><div><button type="button" class="button button--outline" data-dialog-close>Close</button><button type="button" class="button button--primary" data-detail-edit>Edit</button></div></footer>`;
}

function fieldMarkup({ name, label, value = "", type = "text", hint = "", required = false, maxLength, options, disabled = false }) {
    const id = `management-field-${name}`;
    const requiredAttribute = required ? "required" : "";
    const disabledAttribute = disabled ? "disabled" : "";
    const maxAttribute = maxLength ? `maxlength="${maxLength}"` : "";
    let input;

    if (type === "textarea") {
        input = `<textarea class="text-input management-textarea" id="${id}" name="${name}" ${requiredAttribute} ${maxAttribute} ${disabledAttribute}>${escapeHtml(value)}</textarea>`;
    } else if (type === "select") {
        input = `<select class="text-input" id="${id}" name="${name}" ${requiredAttribute} ${disabledAttribute}>${options.map(([optionValue, optionLabel]) => `<option value="${escapeHtml(optionValue)}" ${String(value) === String(optionValue) ? "selected" : ""}>${escapeHtml(optionLabel)}</option>`).join("")}</select>`;
    } else {
        const inputType = type === "decimal" || type === "integer" ? "number" : type;
        const step = type === "decimal" ? 'step="any"' : type === "integer" ? 'step="1"' : "";
        input = `<input class="text-input" id="${id}" name="${name}" type="${inputType}" value="${escapeHtml(value)}" ${requiredAttribute} ${maxAttribute} ${step} ${disabledAttribute}>`;
    }

    return `<div class="management-field" data-field-wrap="${name}"><label for="${id}">${escapeHtml(label)}</label>${input}${hint ? `<p class="management-field-hint">${escapeHtml(hint)}</p>` : ""}<p class="management-field-error" data-field-error="${name}"></p></div>`;
}

function activeField(isActive) {
    return `<label class="management-switch"><span><strong>Active</strong><small>Make this record available in JST.</small></span><input type="checkbox" name="is_active" ${isActive ? "checked" : ""}><span class="management-switch__track" aria-hidden="true"></span></label>`;
}

function settingValueInput(type, value, isSensitive, hasValue, required) {
    const hint = isSensitive
        ? (hasValue ? "Leave blank to keep the saved value. The existing value is never displayed." : "Sensitive values are hidden after saving.")
        : "";
    if (type === "boolean") {
        return fieldMarkup({
            name: "value",
            label: "Value",
            value: value === "false" || value === "0" ? "false" : "true",
            type: "select",
            options: [["true", "Enabled"], ["false", "Disabled"]],
            hint,
            required,
        });
    }
    return fieldMarkup({
        name: "value",
        label: "Value",
        value,
        type: type === "integer" || type === "decimal" ? type : "text",
        hint,
        required,
        maxLength: 255,
    });
}

function formMarkup(module, mode, record) {
    const isEditing = mode === "edit";
    let fields = "";
    let title = isEditing ? "Edit record" : modules[module].createLabel;

    if (module === "governorates") {
        title = isEditing ? `Edit ${record.name}` : modules[module].createLabel;
        fields = `${fieldMarkup({ name: "name", label: "Governorate name", value: record?.name || "", required: true, maxLength: 50 })}
            ${fieldMarkup({ name: "description", label: "Description", value: record?.description || "", type: "textarea", maxLength: 5000, hint: "A concise overview for administration." })}
            ${activeField(record?.is_active ?? true)}`;
    } else if (module === "categories") {
        title = isEditing ? `Edit ${record.name}` : modules[module].createLabel;
        fields = `${fieldMarkup({ name: "kind", label: "Category type", value: record?.kind || "tourism", type: "select", required: true, options: [["tourism", "Tourism"], ["service", "Services"]] })}
            ${fieldMarkup({ name: "name", label: "Category name", value: record?.name || "", required: true, maxLength: 60 })}
            ${fieldMarkup({ name: "description", label: "Description", value: record?.description || "", type: "textarea", maxLength: 255 })}
            ${activeField(record?.is_active ?? true)}`;
    } else {
        title = isEditing ? `Edit ${record.name}` : modules[module].createLabel;
        const valueType = record?.value_type || "string";
        fields = `${isEditing
            ? `<div class="management-setting-name"><span>Setting name</span><strong>${escapeHtml(record.name)}</strong></div>`
            : fieldMarkup({ name: "name", label: "Setting name", value: "", required: true, maxLength: 60, hint: "Use a clear name, such as Default region.", })}
            ${fieldMarkup({ name: "value_type", label: "Value type", value: valueType, type: "select", required: true, options: [["string", "Text"], ["decimal", "Decimal number"], ["integer", "Whole number"], ["boolean", "On or off"]], disabled: Boolean(record?.is_protected) })}
            ${record?.is_protected ? `<input type="hidden" name="value_type" value="${escapeHtml(valueType)}">` : ""}
            <div data-setting-value-field>${settingValueInput(valueType, record?.is_sensitive ? "" : record?.value || "", Boolean(record?.is_sensitive), Boolean(record?.has_value), !record?.is_sensitive || !isEditing)}</div>
            ${fieldMarkup({ name: "description", label: "Description", value: record?.description || "", type: "textarea", maxLength: 255 })}
            ${record?.is_protected ? '<p class="management-field-hint">Core settings can be edited but cannot be removed or retyped.</p>' : ""}`;
    }

    return `${dialogHead(title, isEditing ? "Update record" : "New record")}
        <form class="management-form" data-record-form data-mode="${mode}">
            <div class="management-dialog__body">
                <p class="management-form-message hidden" data-form-message role="alert"></p>
                ${fields}
            </div>
            <footer class="management-dialog__footer">
                <button type="button" class="button button--outline" data-dialog-close>Cancel</button>
                <button type="submit" class="button button--primary" data-save>
                    <span class="button-spinner" aria-hidden="true"></span>
                    <span data-save-label>${isEditing ? "Save changes" : "Create"}</span>
                </button>
            </footer>
        </form>`;
}

function deleteMarkup(module, record) {
    return `${dialogHead(`Delete ${record.name}?`, "Confirm deletion")}
        <div class="management-dialog__body">
            <p class="management-delete-copy">This action permanently removes <strong>${escapeHtml(record.name)}</strong>.</p>
            <p class="management-delete-note">${escapeHtml(dependencySummary(record))}</p>
            <p class="management-form-message hidden" data-form-message role="alert"></p>
        </div>
        <footer class="management-dialog__footer">
            <button type="button" class="button button--outline" data-dialog-close>Cancel</button>
            <button type="button" class="button button--danger" data-confirm-delete data-record-key="${escapeHtml(recordKey(module, record))}">
                <span class="button-spinner" aria-hidden="true"></span><span data-delete-label>Delete</span>
            </button>
        </footer>`;
}

function showToast(root, message, kind = "success") {
    const toast = node("[data-management-toast]", root);
    window.clearTimeout(toastTimer);
    toast.textContent = message;
    toast.className = `management-toast management-toast--${kind}`;
    toastTimer = window.setTimeout(() => toast.classList.add("hidden"), 3600);
}

function setFeedback(root, message = "", kind = "error") {
    const element = node("[data-form-message]", root);
    if (!element) return;
    element.textContent = message;
    element.classList.toggle("hidden", !message);
    element.classList.toggle("management-form-message--error", kind === "error");
    element.classList.toggle("management-form-message--success", kind === "success");
}

function clearFieldErrors(form) {
    form.querySelectorAll("[data-field-error]").forEach((element) => {
        element.textContent = "";
    });
    form.querySelectorAll("[aria-invalid=true]").forEach((element) => element.removeAttribute("aria-invalid"));
}

function showFieldErrors(form, errors) {
    clearFieldErrors(form);
    Object.entries(errors || {}).forEach(([field, messages]) => {
        const error = node(`[data-field-error="${field}"]`, form);
        const input = node(`[name="${field}"]`, form);
        if (error) error.textContent = Array.isArray(messages) ? messages[0] : String(messages);
        if (input) input.setAttribute("aria-invalid", "true");
    });
    const firstInvalid = form.querySelector('[aria-invalid="true"]');
    firstInvalid?.focus();
}

function tableStateMarkup(title, description, action = "") {
    return `<div class="management-empty-state"><span class="management-empty-mark"><i data-lucide="circle-alert" aria-hidden="true"></i></span><strong>${escapeHtml(title)}</strong><p>${escapeHtml(description)}</p>${action}</div>`;
}

function queryHasFilters(state) {
    return Boolean(state.search || state.status !== "all" || state.kind !== "all" || state.group !== "all");
}

function setLoading(root, loading, message = "Loading records…") {
    const state = node("[data-table-state]", root);
    state.innerHTML = loading
        ? `<span class="management-spinner" aria-hidden="true"></span><span>${escapeHtml(message)}</span>`
        : "";
    state.classList.toggle("hidden", !loading);
}

function renderIcons(root) {
    createIcons({ icons: iconSet, root, attrs: { "stroke-width": 1.75 } });
}

function setIdentity(user, role) {
    const name = [user.first_name, user.last_name].filter(Boolean).join(" ") || user.email || "JST Admin";
    document.querySelectorAll("[data-user-name]").forEach((element) => {
        element.textContent = name;
    });
    document.querySelectorAll("[data-user-initial]").forEach((element) => {
        element.textContent = name.trim().charAt(0).toLocaleUpperCase() || "J";
    });
    document.querySelectorAll("[data-user-role]").forEach((element) => {
        element.textContent = role === "content_admin" ? "Content administrator" : "System administrator";
    });
    node("[data-workspace-label]").textContent = role === "content_admin" ? "Content administration" : "System administration";
    if (role !== "system_admin") {
        document.querySelectorAll("[data-system-admin-only], [data-platform-settings-group]").forEach((element) => {
            element.hidden = true;
        });
    }
}

function buildRow(module, record) {
    if (module === "governorates") return governorateRow(record);
    if (module === "categories") return categoryRow(record);
    return settingRow(record);
}

function clearFilters(root, state) {
    state.search = "";
    state.status = "all";
    state.kind = "all";
    state.group = "all";
    state.page = 1;
    node("[data-search]", root).value = "";
    if (node("[data-status-filter]", root)) node("[data-status-filter]", root).value = "all";
    if (node("[data-kind-filter]", root)) node("[data-kind-filter]", root).value = "all";
    if (node("[data-group-filter]", root)) node("[data-group-filter]", root).value = "all";
}

function initModule(root, module) {
    const config = modules[module];
    if (!config || root.dataset.initialized === "true") return;
    root.dataset.initialized = "true";

    const dialog = node("[data-management-dialog]", root);
    const dialogContent = node("[data-dialog-content]", dialog);
    const tbody = node("[data-record-rows]", root);
    const tableScroll = node(".management-table-scroll", root);
    const tableState = node("[data-table-state]", root);
    const pagination = node("[data-pagination]", root);
    const state = {
        search: "",
        status: "all",
        kind: "all",
        group: "all",
        page: 1,
        perPage: 10,
        sort: module === "platform-settings" ? "key" : "name",
        direction: "asc",
        records: new Map(),
        hasLoaded: false,
        currentDialogRecord: null,
        currentDialogMode: null,
        focusReturn: null,
        saving: false,
        request: null,
        requestNumber: 0,
        debounce: null,
        lastQuery: "",
    };

    function endpointFor(record) {
        return `${config.endpoint}/${encodeURIComponent(recordKey(module, record))}`;
    }

    function showDialog(markup, mode, record = null, focusSelector = "input:not([type=hidden]), select, textarea, button") {
        if (!dialog.open) {
            state.focusReturn = document.activeElement;
            dialog.showModal();
        }
        state.currentDialogMode = mode;
        state.currentDialogRecord = record;
        dialogContent.innerHTML = markup;
        renderIcons(dialogContent);
        requestAnimationFrame(() => node(focusSelector, dialogContent)?.focus());
    }

    function closeDialog(force = false) {
        if ((state.saving && !force) || !dialog.open) return;
        dialog.close();
        state.currentDialogRecord = null;
        state.currentDialogMode = null;
        requestAnimationFrame(() => state.focusReturn?.focus?.());
    }

    function setTableEmpty(title, description, action = "") {
        tbody.replaceChildren();
        state.records.clear();
        tableScroll.classList.add("hidden");
        pagination.classList.add("hidden");
        tableState.innerHTML = tableStateMarkup(title, description, action);
        tableState.classList.remove("hidden");
        renderIcons(tableState);
    }

    function renderPagination(meta = {}) {
        const total = Number(meta.total || 0);
        if (total === 0) {
            pagination.classList.add("hidden");
            return;
        }
        const current = Number(meta.current_page || state.page);
        const last = Number(meta.last_page || 1);
        const from = Number(meta.from || 0);
        const to = Number(meta.to || 0);
        node("[data-pagination-summary]", root).textContent = `Showing ${numberFormat.format(from)}–${numberFormat.format(to)} of ${numberFormat.format(total)}`;
        node("[data-page-number]", root).textContent = `Page ${current} of ${last}`;
        node("[data-page-previous]", root).disabled = current <= 1;
        node("[data-page-next]", root).disabled = current >= last;
        pagination.classList.remove("hidden");
    }

    function renderList(payload) {
        const records = Array.isArray(payload?.data) ? payload.data : [];
        if (records.length === 0) {
            const hasFilters = queryHasFilters(state);
            const action = hasFilters
                ? '<button type="button" class="button button--outline" data-empty-reset>Clear filters</button>'
                : `<button type="button" class="button button--primary" data-empty-create>${escapeHtml(config.createLabel)}</button>`;
            setTableEmpty(hasFilters ? "No matching records" : config.emptyTitle, hasFilters ? config.searchDescription : config.emptyDescription, action);
            renderPagination(payload?.meta || {});
            return;
        }

        state.records = new Map(records.map((record) => [recordKey(module, record), record]));
        tbody.innerHTML = records.map((record) => buildRow(module, record)).join("");
        renderIcons(tbody);
        tableState.classList.add("hidden");
        tableScroll.classList.remove("hidden");
        renderPagination(payload?.meta || {});
    }

    function buildQuery() {
        const params = new URLSearchParams({
            page: String(state.page),
            per_page: String(state.perPage),
            sort: state.sort,
            direction: state.direction,
        });
        if (state.search) params.set("search", state.search);
        if (module !== "platform-settings") params.set("status", state.status);
        if (module === "categories") params.set("kind", state.kind);
        if (module === "platform-settings") params.set("group", state.group);
        return params.toString();
    }

    async function loadList(force = false) {
        const query = buildQuery();
        if (!force && state.hasLoaded && query === state.lastQuery) return;
        state.request?.abort();
        const controller = new AbortController();
        state.request = controller;
        const requestNumber = ++state.requestNumber;
        state.lastQuery = query;
        if (!state.hasLoaded) {
            setLoading(root, true);
        } else {
            node("[data-list-feedback]", root).textContent = "Updating records…";
            node("[data-list-feedback]", root).classList.remove("hidden");
            node("[data-list-feedback]", root).classList.remove("management-feedback--error");
            node("[data-list-feedback]", root).classList.add("management-feedback--loading");
        }

        try {
            const response = await apiRequest(`${config.endpoint}?${query}`, {
                authenticated: true,
                signal: controller.signal,
            });
            if (requestNumber !== state.requestNumber) return;
            state.hasLoaded = true;
            node("[data-list-feedback]", root).classList.add("hidden");
            renderList(response);
        } catch (error) {
            if (requestNumber !== state.requestNumber || error.name === "AbortError") return;
            node("[data-list-feedback]", root).classList.remove("hidden", "management-feedback--loading");
            node("[data-list-feedback]", root).classList.add("management-feedback--error");
            node("[data-list-feedback]", root).textContent = error.message || "We could not load these records.";
            setTableEmpty("Records could not be loaded", error.message || "Check your connection and try again.", '<button type="button" class="button button--outline" data-list-retry>Try again</button>');
        } finally {
            if (requestNumber === state.requestNumber) state.request = null;
        }
    }

    async function fetchRecord(key) {
        return apiRequest(`${config.endpoint}/${encodeURIComponent(key)}`, { authenticated: true });
    }

    async function openDetails(key) {
        const summary = state.records.get(key);
        showDialog(`${dialogHead(summary?.name || "Record")}<div class="management-dialog__body"><span class="management-spinner" aria-hidden="true"></span><p>Loading details…</p></div>`, "details", summary, "[data-dialog-close]");
        try {
            const response = await fetchRecord(key);
            const record = response?.data || response;
            state.currentDialogRecord = record;
            dialogContent.innerHTML = detailMarkup(module, record);
            renderIcons(dialogContent);
            node("[data-detail-edit]", dialogContent)?.focus();
        } catch (error) {
            if (!dialog.open) return;
            dialogContent.innerHTML = `${dialogHead("Details unavailable")}<div class="management-dialog__body"><p class="management-form-message management-form-message--error">${escapeHtml(error.message || "We could not load this record.")}</p></div><footer class="management-dialog__footer"><button type="button" class="button button--outline" data-dialog-close>Close</button></footer>`;
            renderIcons(dialogContent);
        }
    }

    function openForm(mode, record = null) {
        state.currentDialogRecord = record;
        showDialog(formMarkup(module, mode, record), mode, record, mode === "create" ? "input:not([type=hidden]), select" : "input:not([type=hidden]), select");
    }

    async function openEdit(key) {
        const current = state.records.get(key);
        showDialog(`${dialogHead(`Edit ${current?.name || "record"}`, "Update record")}<div class="management-dialog__body"><span class="management-spinner" aria-hidden="true"></span><p>Loading record…</p></div>`, "edit-loading", current, "[data-dialog-close]");
        try {
            const response = await fetchRecord(key);
            if (!dialog.open) return;
            const record = response?.data || response;
            openForm("edit", record);
        } catch (error) {
            if (!dialog.open) return;
            dialogContent.innerHTML = `${dialogHead("Record unavailable")}<div class="management-dialog__body"><p class="management-form-message management-form-message--error">${escapeHtml(error.message || "We could not load this record.")}</p></div><footer class="management-dialog__footer"><button type="button" class="button button--outline" data-dialog-close>Close</button></footer>`;
            renderIcons(dialogContent);
        }
    }

    function openDelete(record) {
        if (!canDelete(module, record)) {
            showToast(root, dependencySummary(record), "error");
            return;
        }
        showDialog(deleteMarkup(module, record), "delete", record, "[data-dialog-close]");
        node("[data-confirm-delete]", dialogContent)?.focus();
    }

    function readForm(form) {
        const values = Object.fromEntries(new FormData(form).entries());
        if (module === "governorates" || module === "categories") {
            values.is_active = Boolean(form.querySelector('[name="is_active"]')?.checked);
        }
        return values;
    }

    function setSaving(isSaving, mode) {
        state.saving = isSaving;
        dialog.dataset.saving = String(isSaving);
        dialog.querySelectorAll("[data-dialog-close]").forEach((button) => {
            button.disabled = isSaving;
        });
        const submit = mode === "delete" ? node("[data-confirm-delete]", dialogContent) : node("[data-save]", dialogContent);
        if (submit) {
            submit.disabled = isSaving;
            submit.classList.toggle("is-loading", isSaving);
            submit.setAttribute("aria-busy", String(isSaving));
        }
        const label = mode === "delete" ? node("[data-delete-label]", dialogContent) : node("[data-save-label]", dialogContent);
        if (label) {
            if (isSaving) label.dataset.originalLabel = label.textContent;
            label.textContent = isSaving ? (mode === "delete" ? "Deleting…" : "Saving…") : (label.dataset.originalLabel || (mode === "delete" ? "Delete" : "Save changes"));
        }
    }

    async function submitForm(form) {
        if (state.saving) return;
        clearFieldErrors(form);
        setFeedback(dialogContent, "");
        const mode = form.dataset.mode;
        const payload = readForm(form);
        const record = state.currentDialogRecord;
        const url = mode === "edit" ? endpointFor(record) : config.endpoint;
        const method = mode === "edit" ? "PUT" : "POST";
        setSaving(true, mode);

        try {
            await apiRequest(url, { method, body: payload, authenticated: true });
            closeDialog(true);
            showToast(root, mode === "edit" ? "Changes saved." : config.createdLabel, "success");
            await loadList(true);
        } catch (error) {
            if (error.status === 422 && Object.keys(error.errors || {}).length > 0) {
                showFieldErrors(form, error.errors);
                setFeedback(dialogContent, "Review the highlighted fields and try again.");
            } else {
                setFeedback(dialogContent, error.message || "The record could not be saved.");
            }
        } finally {
            setSaving(false, mode);
        }
    }

    async function submitDelete(button) {
        if (state.saving || !state.currentDialogRecord) return;
        const record = state.currentDialogRecord;
        setSaving(true, "delete");
        try {
            await apiRequest(endpointFor(record), { method: "DELETE", authenticated: true });
            closeDialog(true);
            showToast(root, "Record deleted.", "success");
            if (state.page > 1 && state.records.size === 1) state.page -= 1;
            await loadList(true);
        } catch (error) {
            setFeedback(dialogContent, error.message || "The record could not be deleted.");
        } finally {
            setSaving(false, "delete");
        }
    }

    function onClick(event) {
        const target = event.target.closest("button");
        if (!target) return;

        if (target.matches("[data-create], [data-empty-create]")) {
            openForm("create");
            return;
        }
        if (target.matches("[data-dialog-close]")) {
            closeDialog();
            return;
        }
        if (target.matches("[data-empty-reset]")) {
            clearFilters(root, state);
            loadList(true);
            return;
        }
        if (target.matches("[data-list-retry]")) {
            loadList(true);
            return;
        }
        if (target.matches("[data-page-previous]") && state.page > 1) {
            state.page -= 1;
            loadList(true);
            return;
        }
        if (target.matches("[data-page-next]") && !target.disabled) {
            state.page += 1;
            loadList(true);
            return;
        }
        if (target.matches("[data-sort]")) {
            const sort = target.dataset.sort;
            state.direction = state.sort === sort && state.direction === "asc" ? "desc" : "asc";
            state.sort = sort;
            state.page = 1;
            loadList(true);
            return;
        }
        if (target.matches("[data-detail-edit]") && state.currentDialogRecord) {
            openForm("edit", state.currentDialogRecord);
            return;
        }
        if (target.matches("[data-detail-delete]") && state.currentDialogRecord) {
            openDelete(state.currentDialogRecord);
            return;
        }
        if (target.matches("[data-confirm-delete]")) {
            submitDelete(target);
            return;
        }
        if (target.matches("[data-action]")) {
            const key = target.dataset.recordKey;
            if (target.dataset.action === "view") openDetails(key);
            if (target.dataset.action === "edit") openEdit(key);
            if (target.dataset.action === "delete") {
                const record = state.records.get(key);
                if (record) openDelete(record);
            }
        }
    }

    function onSubmit(event) {
        const form = event.target.closest("[data-record-form]");
        if (!form) return;
        event.preventDefault();
        submitForm(form);
    }

    function onChange(event) {
        const target = event.target;
        if (target.matches("[data-status-filter]")) {
            state.status = target.value;
            state.page = 1;
            loadList(true);
        } else if (target.matches("[data-kind-filter]")) {
            state.kind = target.value;
            state.page = 1;
            loadList(true);
        } else if (target.matches("[data-group-filter]")) {
            state.group = target.value;
            state.page = 1;
            loadList(true);
        } else if (target.name === "value_type") {
            const setting = state.currentDialogRecord;
            const isSensitive = Boolean(setting?.is_sensitive) || (module === "platform-settings" && /(?:password|secret|token|api[_-]?key|private[_-]?key|credential)/i.test(node('[name="name"]', dialogContent)?.value || ""));
            const saved = node('[name="value"]', dialogContent)?.value || "";
            const container = node("[data-setting-value-field]", dialogContent);
            container.innerHTML = settingValueInput(target.value, saved, isSensitive, Boolean(setting?.has_value), !isSensitive || !setting);
            renderIcons(container);
        }
    }

    root.addEventListener("click", onClick);
    root.addEventListener("submit", onSubmit);
    root.addEventListener("change", onChange);
    node("[data-search]", root).addEventListener("input", (event) => {
        window.clearTimeout(state.debounce);
        state.debounce = window.setTimeout(() => {
            state.search = event.target.value.trim();
            state.page = 1;
            loadList(true);
        }, 280);
    });
    dialog.addEventListener("cancel", (event) => {
        if (state.saving) event.preventDefault();
    });
    dialog.addEventListener("click", (event) => {
        if (event.target === dialog && !state.saving) closeDialog();
    });

    if (node("[data-logout]")) {
        document.querySelectorAll("[data-logout]").forEach((button) => {
            button.addEventListener("click", async () => {
                try {
                    await apiRequest("/logout", { method: "POST", authenticated: true });
                } catch {
                    window.sessionStorage.removeItem("jst.access-token");
                    window.sessionStorage.removeItem("jst.user");
                }
                window.sessionStorage.removeItem("jst.access-token");
                window.sessionStorage.removeItem("jst.user");
                window.location.assign("/");
            });
        });
    }

    window.addEventListener("jst:unauthorized", () => window.location.assign("/login"), { once: true });

    if (!getToken()) {
        window.location.assign("/login");
        return;
    }

    apiRequest("/user", { authenticated: true })
        .then((response) => {
            const user = response?.data || response;
            const allowedRoles = module === "platform-settings" ? ["system_admin"] : ["system_admin", "content_admin"];
            if (!allowedRoles.includes(user.role)) {
                window.location.assign(user.role === "content_admin" ? "/workspace" : "/");
                return;
            }
            setIdentity(user, user.role);
            node("[data-dashboard-app]").classList.remove("hidden");
            node("[data-dashboard-gate]")?.classList.add("hidden");
            loadList(true);
        })
        .catch((error) => {
            if (error.status === 401) return;
            node("[data-dashboard-gate]").innerHTML = `<span class="brand-mark" aria-hidden="true"><img src="/images/jst-symbol.svg" alt=""></span><p>${escapeHtml(error.message || "We could not confirm your account.")}</p><a class="button button--outline" href="/login">Return to log in</a>`;
        });
}

export function initAdminManagement() {
    if (initialized) return;
    const root = node("[data-management-page]");
    if (!root) return;
    const module = root.dataset.module;
    if (!modules[module]) return;
    initialized = true;
    renderIcons(root);
    initModule(root, module);
}
