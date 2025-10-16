/**
 * Logs Manager - Sistema Moderno PWA-Ready
 * Versión minificada para rendimiento <3s
 * Compatible con Bootstrap 5 y PWA
 */
class LogsManager {
    constructor() {
        this.routes = window.logsRoutes || {};
        this.csrfToken =
            window.logsCSRF ||
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") ||
            "";
        this.currentDate = window.logsCurrentDate || "";
        this.today = window.logsToday || "";
        this.modals = {};
        this.toasts = [];
        this.autoRefreshInterval = null;
        this.bootstrapReady = false;
        this.waitForBootstrap().then(() => {
            this.init();
        });
    }
    waitForBootstrap() {
        return new Promise((resolve) => {
            const checkBootstrap = () => {
                if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
                    this.bootstrapReady = true;
                    resolve();
                } else {
                    setTimeout(checkBootstrap, 100);
                }
            };
            checkBootstrap();
        });
    }
    init() {
        this.setupEventListeners();
        this.setupKeyboardShortcuts();
        this.animateOnLoad();
        this.setupAutoRefresh();
        console.log("LogsManager initialized with Bootstrap", bootstrap?.Modal);
    }
    setupEventListeners() {
        const self = this;
        document.querySelectorAll("[data-logs-action]").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                const action = e.currentTarget.getAttribute("data-logs-action");
                self.handleAction(action);
            });
        });
        const cleanupForm = document.getElementById("cleanupForm");
        if (cleanupForm) {
            cleanupForm.addEventListener("submit", (e) => {
                e.preventDefault();
                self.handleCleanupSubmit(e);
            });
        }
        const exportForm = document.getElementById("exportForm");
        if (exportForm) {
            exportForm.addEventListener("submit", (e) => {
                e.preventDefault();
                self.handleExportSubmit(e);
            });
        }
        this.setupMessageExpandButtons();
    }
    setupMessageExpandButtons() {
        const self = this;
        document.querySelectorAll(".logs-message-expand").forEach((btn) => {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                const message = this.getAttribute("data-message");
                if (message) {
                    self.showMessageModal(message);
                } else {
                    console.error("No message data found");
                }
            });
        });
    }
    setupKeyboardShortcuts() {
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                const openModals =
                    document.querySelectorAll(".logs-modal.show");
                if (openModals.length > 0) {
                    e.preventDefault();
                    this.closeAllModals();
                }
            }
            if (e.ctrlKey && e.key === "r") {
                e.preventDefault();
                this.refreshStats();
            }
        });
    }
    animateOnLoad() {
        const cards = document.querySelectorAll(".logs-stat-card");
        cards.forEach((card, idx) => {
            card.classList.add("animate-fadeInUp");
            card.classList.add(`animate-delay-${Math.min(idx + 1, 6)}`);
            card.style.opacity = "0";
        });
        const sections = document.querySelectorAll(".logs-section-card");
        sections.forEach((section, idx) => {
            section.classList.add("animate-fadeInUp");
            section.classList.add(`animate-delay-${Math.min(idx + 1, 4)}`);
            section.style.opacity = "0";
        });
    }
    setupAutoRefresh() {
        if (this.currentDate === this.today) {
            this.autoRefreshInterval = setInterval(() => {
                if (!document.querySelector(".logs-modal.show")) {
                    this.refreshStats();
                }
            }, 30000);
        }
    }
    handleAction(action) {
        const actions = {
            clear: () => this.confirmClearLog(),
            cleanup: () => this.showCleanupModal(),
            export: () => this.showExportModal(),
            refresh: () => this.refreshStats(),
        };
        if (actions[action]) {
            actions[action]();
        } else {
            console.warn(`Unknown action: ${action}`);
        }
    }
    confirmClearLog() {
        this.showConfirmDialog(
            "¿Limpiar Log Principal?",
            "Esta acción eliminará todo el contenido del archivo de log principal (laravel.log). Esta acción no se puede deshacer.",
            "warning",
            () => this.executeClearLog()
        );
    }
    async executeClearLog() {
        this.showLoading("Limpiando log principal...");
        try {
            const response = await fetch(this.routes.clear, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.csrfToken,
                    "Content-Type": "application/json",
                },
            });
            const data = await response.json();
            this.hideLoading();
            if (data.success) {
                this.showToast(
                    "success",
                    "Log Limpiado",
                    "El log principal se ha limpiado exitosamente"
                );
                setTimeout(() => location.reload(), 2000);
            } else {
                this.showToast(
                    "danger",
                    "Error al Limpiar",
                    "Error: " + data.message
                );
            }
        } catch (error) {
            this.hideLoading();
            this.showToast("danger", "Error de Conexión", error.message);
        }
    }
    showCleanupModal() {
        const modal = this.createModal(
            "cleanupModal",
            "Limpiar Logs Antiguos",
            `<form id="cleanupFormContent"><div class="mb-3"><label class="logs-filter-label">Eliminar logs más antiguos a:</label><select name="days" class="logs-filter-control" required><option value="7">7 días</option><option value="14">14 días</option><option value="30" selected>30 días</option><option value="60">60 días</option><option value="90">90 días</option></select></div><div class="logs-alert logs-alert-warning"><i class="bi bi-exclamation-triangle"></i><div><strong>Advertencia:</strong> Esta acción eliminará permanentemente los archivos de log antiguos.</div></div></form>`,
            "warning",
            () => this.handleCleanupFormSubmit()
        );
        this.showModal("cleanupModal");
    }
    async handleCleanupFormSubmit() {
        const form = document.getElementById("cleanupFormContent");
        if (!form) return;
        const formData = new FormData(form);
        this.closeModal("cleanupModal");
        this.showLoading("Limpiando logs antiguos...");
        try {
            const response = await fetch(this.routes.cleanup, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": this.csrfToken },
                body: formData,
            });
            const data = await response.json();
            this.hideLoading();
            if (data.success) {
                this.showToast(
                    "success",
                    "Limpieza Completada",
                    `Se eliminaron ${
                        data.deleted_count || 0
                    } archivos. Espacio liberado: ${data.space_freed || "N/A"}`
                );
                setTimeout(() => location.reload(), 2000);
            } else {
                this.showToast("danger", "Error en Limpieza", data.message);
            }
        } catch (error) {
            this.hideLoading();
            this.showToast("danger", "Error de Conexión", error.message);
        }
    }
    async handleCleanupSubmit(e) {
        const form = e.target;
        const formData = new FormData(form);
        const modal = bootstrap.Modal.getInstance(
            document.getElementById("cleanupModal")
        );
        if (modal) modal.hide();
        this.showLoading("Limpiando logs antiguos...");
        try {
            const response = await fetch(this.routes.cleanup, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": this.csrfToken },
                body: formData,
            });
            const data = await response.json();
            this.hideLoading();
            if (data.success) {
                this.showToast(
                    "success",
                    "Limpieza Completada",
                    `Se eliminaron ${
                        data.deleted_count || 0
                    } archivos. Espacio liberado: ${data.space_freed || "N/A"}`
                );
                setTimeout(() => location.reload(), 2000);
            } else {
                this.showToast("danger", "Error en Limpieza", data.message);
            }
        } catch (error) {
            this.hideLoading();
            this.showToast("danger", "Error de Conexión", error.message);
        }
    }
    showExportModal() {
        const modal = this.createModal(
            "exportModal",
            "Exportar Logs",
            `<form id="exportFormContent"><div class="row"><div class="col-md-6 mb-3"><label class="logs-filter-label">Fecha Inicio</label><input type="date" name="start_date" class="logs-filter-control" value="${this.getDateDaysAgo(
                7
            )}" required></div><div class="col-md-6 mb-3"><label class="logs-filter-label">Fecha Fin</label><input type="date" name="end_date" class="logs-filter-control" value="${
                this.today
            }" required></div></div><div class="mb-3"><label class="logs-filter-label">Nivel (Opcional)</label><select name="level" class="logs-filter-control"><option value="">Todos los niveles</option><option value="error">Solo Errores</option><option value="warning">Solo Warnings</option><option value="info">Solo Info</option><option value="debug">Solo Debug</option></select></div></form>`,
            "success",
            () => this.handleExportFormSubmit()
        );
        this.showModal("exportModal");
    }
    handleExportFormSubmit() {
        const form = document.getElementById("exportFormContent");
        if (!form) return;
        const formData = new FormData(form);
        this.closeModal("exportModal");
        const exportForm = document.createElement("form");
        exportForm.method = "POST";
        exportForm.action = this.routes.export;
        exportForm.target = "_blank";
        const csrfInput = document.createElement("input");
        csrfInput.type = "hidden";
        csrfInput.name = "_token";
        csrfInput.value = this.csrfToken;
        exportForm.appendChild(csrfInput);
        for (let [key, value] of formData.entries()) {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = key;
            input.value = value;
            exportForm.appendChild(input);
        }
        document.body.appendChild(exportForm);
        exportForm.submit();
        document.body.removeChild(exportForm);
        this.showToast(
            "info",
            "Exportación Iniciada",
            "Tu archivo de logs se descargará en breve"
        );
    }
    async handleExportSubmit(e) {
        const form = e.target;
        const formData = new FormData(form);
        const modal = bootstrap.Modal.getInstance(
            document.getElementById("exportModal")
        );
        if (modal) modal.hide();
        const exportForm = document.createElement("form");
        exportForm.method = "POST";
        exportForm.action = this.routes.export;
        exportForm.target = "_blank";
        const csrfInput = document.createElement("input");
        csrfInput.type = "hidden";
        csrfInput.name = "_token";
        csrfInput.value = this.csrfToken;
        exportForm.appendChild(csrfInput);
        for (let [key, value] of formData.entries()) {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = key;
            input.value = value;
            exportForm.appendChild(input);
        }
        document.body.appendChild(exportForm);
        exportForm.submit();
        document.body.removeChild(exportForm);
        this.showToast(
            "info",
            "Exportación Iniciada",
            "Tu archivo de logs se descargará en breve"
        );
    }
    showMessageModal(message) {
        if (!message) {
            console.error("No message provided to showMessageModal");
            return;
        }
        const modal = this.createModal(
            "messageModal",
            "Mensaje Completo del Log",
            `<div style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:12px;padding:1.5rem;max-height:500px;overflow-y:auto"><pre style="font-family:'Consolas','Monaco','Courier New',monospace;font-size:0.875rem;color:#212529;white-space:pre-wrap;word-wrap:break-word;margin:0;line-height:1.6">${this.escapeHtml(
                message
            )}</pre></div>`,
            "info",
            null,
            "lg"
        );
        this.showModal("messageModal");
    }
    async refreshStats() {
        const btn = document.querySelector('[data-logs-action="refresh"]');
        if (btn) {
            const icon = btn.querySelector("i");
            if (icon) icon.style.animation = "spin 1s linear infinite";
        }
        try {
            const response = await fetch(this.routes.stats);
            const data = await response.json();
            if (data.success) {
                this.showToast(
                    "success",
                    "Estadísticas Actualizadas",
                    "Los datos se han actualizado correctamente"
                );
                setTimeout(() => location.reload(), 1500);
            }
        } catch (error) {
            this.showToast(
                "warning",
                "Error al Actualizar",
                "No se pudieron actualizar las estadísticas"
            );
        } finally {
            if (btn) {
                const icon = btn.querySelector("i");
                if (icon) icon.style.animation = "";
            }
        }
    }
    showConfirmDialog(title, message, type, onConfirm) {
        if (!this.bootstrapReady) {
            console.error("Bootstrap not ready");
            return;
        }
        const iconMap = {
            info: "info-circle-fill",
            warning: "exclamation-triangle-fill",
            danger: "exclamation-triangle-fill",
            success: "check-circle-fill",
        };
        const colorMap = {
            info: "#17a2b8",
            warning: "#ffc107",
            danger: "#dc3545",
            success: "#28a745",
        };
        const modal = document.createElement("div");
        modal.className = "modal fade";
        modal.id = "confirmActionModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("role", "dialog");
        modal.setAttribute("aria-labelledby", "confirmActionModalLabel");
        modal.setAttribute("aria-hidden", "true");
        modal.innerHTML = `<div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content border-0 shadow-lg"><div class="modal-header border-0 logs-modal-header-${type}"><h5 class="modal-title" id="confirmActionModalLabel"><i class="bi bi-${
            iconMap[type]
        } me-2"></i>${title}</h5><button type="button" class="btn-close${
            type !== "warning" ? " btn-close-white" : ""
        }" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body p-4"><p class="mb-0">${message}</p></div><div class="modal-footer border-0"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancelar</button><button type="button" class="btn btn-${type}" id="confirmActionBtn"><i class="bi bi-check-circle me-1"></i>Confirmar</button></div></div></div>`;
        document.body.appendChild(modal);
        try {
            const bsModal = new bootstrap.Modal(modal, {
                backdrop: true,
                keyboard: true,
                focus: true,
            });
            bsModal.show();
            const confirmBtn = document.getElementById("confirmActionBtn");
            if (confirmBtn) {
                confirmBtn.addEventListener("click", () => {
                    bsModal.hide();
                    setTimeout(() => onConfirm(), 300);
                });
            }
            modal.addEventListener(
                "hidden.bs.modal",
                function () {
                    setTimeout(() => {
                        if (document.body.contains(modal)) {
                            document.body.removeChild(modal);
                        }
                    }, 500);
                },
                { once: true }
            );
        } catch (error) {
            console.error("Error creating modal:", error);
            if (confirm(message)) {
                onConfirm();
            }
        }
    }
    createModal(
        id,
        title,
        content,
        type = "primary",
        onConfirm = null,
        size = ""
    ) {
        const existingModal = document.getElementById(id);
        if (existingModal) {
            existingModal.remove();
        }
        const headerClass = type ? `logs-modal-header-${type}` : "";
        const sizeClass = size === "lg" ? "logs-modal-dialog-lg" : "";
        const modal = document.createElement("div");
        modal.className = "logs-modal";
        modal.id = id;
        modal.innerHTML = `<div class="logs-modal-dialog ${sizeClass}"><div class="logs-modal-header ${headerClass}"><h5 class="logs-modal-title">${title}</h5><button class="logs-modal-close" onclick="window.logsManager.closeModal('${id}')">&times;</button></div><div class="logs-modal-body">${content}</div><div class="logs-modal-footer"><button class="logs-modal-btn logs-modal-btn-secondary" onclick="window.logsManager.closeModal('${id}')">Cancelar</button>${
            onConfirm
                ? `<button class="logs-modal-btn logs-modal-btn-${type}" id="${id}ConfirmBtn">Confirmar</button>`
                : ""
        }</div></div>`;
        document.body.appendChild(modal);
        if (onConfirm) {
            const confirmBtn = document.getElementById(`${id}ConfirmBtn`);
            if (confirmBtn) {
                confirmBtn.addEventListener("click", () => {
                    onConfirm();
                });
            }
        }
        this.modals[id] = modal;
        return modal;
    }
    showModal(id) {
        const modal = this.modals[id] || document.getElementById(id);
        if (modal) {
            modal.classList.add("show");
            document.body.classList.add("modal-open");
        }
    }
    closeModal(id) {
        const modal = this.modals[id] || document.getElementById(id);
        if (modal) {
            modal.classList.remove("show");
            document.body.classList.remove("modal-open");
            if (this.modals[id]) {
                setTimeout(() => {
                    modal.remove();
                    delete this.modals[id];
                }, 300);
            }
        }
    }
    closeAllModals() {
        Object.keys(this.modals).forEach((id) => this.closeModal(id));
        document.body.classList.remove("modal-open");
    }
    showToast(type, title, message, duration = 5000) {
        const toast = document.createElement("div");
        toast.className = `logs-toast ${type}`;
        const icons = {
            success: "check-circle-fill",
            danger: "exclamation-triangle-fill",
            warning: "exclamation-triangle-fill",
            info: "info-circle-fill",
        };
        toast.innerHTML = `<i class="bi bi-${
            icons[type] || "info-circle-fill"
        } logs-toast-icon text-${type}"></i><div class="logs-toast-content"><h6>${title}</h6><p>${message}</p></div><button class="logs-toast-close">&times;</button>`;
        document.body.appendChild(toast);
        toast.querySelector(".logs-toast-close").onclick = () =>
            this.removeToast(toast);
        setTimeout(() => this.removeToast(toast), duration);
        this.toasts.push(toast);
    }
    removeToast(toast) {
        toast.style.animation = "slideInRight 0.4s ease reverse";
        setTimeout(() => {
            if (toast.parentElement) toast.parentElement.removeChild(toast);
            this.toasts = this.toasts.filter((t) => t !== toast);
        }, 400);
    }
    showLoading(message = "Cargando...") {
        let overlay = document.getElementById("logs-loading-overlay");
        if (!overlay) {
            overlay = document.createElement("div");
            overlay.id = "logs-loading-overlay";
            overlay.className = "logs-loading-overlay";
            overlay.innerHTML = `<div class="text-center"><div class="logs-loading-spinner"></div><p class="mt-3 fw-bold text-white">${message}</p></div>`;
            document.body.appendChild(overlay);
        } else {
            const p = overlay.querySelector("p");
            if (p) p.textContent = message;
        }
        overlay.classList.add("show");
    }
    hideLoading() {
        const overlay = document.getElementById("logs-loading-overlay");
        if (overlay) {
            overlay.classList.remove("show");
            setTimeout(() => {
                if (overlay.parentElement)
                    overlay.parentElement.removeChild(overlay);
            }, 300);
        }
    }
    escapeHtml(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }
    getDateDaysAgo(days) {
        const date = new Date();
        date.setDate(date.getDate() - days);
        return date.toISOString().split("T")[0];
    }
    destroy() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
        }
        this.closeAllModals();
        this.toasts.forEach((toast) => this.removeToast(toast));
    }
}
document.addEventListener("DOMContentLoaded", () => {
    window.logsManager = new LogsManager();
});
window.confirmarLimpiarLogs = () => window.logsManager?.handleAction("clear");
window.mostrarMensajeCompleto = (message) =>
    window.logsManager?.showMessageModal(message);
window.obtenerEstadisticas = () => window.logsManager?.refreshStats();
