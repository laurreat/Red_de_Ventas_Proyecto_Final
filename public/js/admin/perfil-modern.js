class PerfilManager {
    static instance;
    constructor() {
        if (PerfilManager.instance) return PerfilManager.instance;
        PerfilManager.instance = this;
        this.init();
    }
    init() {
        this.setupEventListeners();
        this.setupModals();
        this.setupToast();
        this.setupRealTime();
        console.log("PerfilManager inicializado");
    }
    setupEventListeners() {
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") this.closeAllModals();
        });
        document.addEventListener("visibilitychange", () => {
            if (document.hidden) this.stopRealTime();
            else this.startRealTime();
        });
    }
    setupModals() {
        this.modals = new Map();
        this.backdrop = document.createElement("div");
        this.backdrop.className = "perfil-modal-backdrop";
        this.backdrop.addEventListener("click", () => this.closeAllModals());
        document.body.appendChild(this.backdrop);
    }
    setupToast() {
        this.toastContainer = document.createElement("div");
        this.toastContainer.className = "perfil-toast-container";
        document.body.appendChild(this.toastContainer);
    }
    setupRealTime() {
        this.statsInterval = 30000;
        this.activityInterval = 20000;
        this.statsTimer = null;
        this.activityTimer = null;
        this.startRealTime();
    }
    startRealTime() {
        this.updateStats();
        this.updateActivity();
        this.statsTimer = setInterval(
            () => this.updateStats(),
            this.statsInterval
        );
        this.activityTimer = setInterval(
            () => this.updateActivity(),
            this.activityInterval
        );
    }
    stopRealTime() {
        if (this.statsTimer) {
            clearInterval(this.statsTimer);
            this.statsTimer = null;
        }
        if (this.activityTimer) {
            clearInterval(this.activityTimer);
            this.activityTimer = null;
        }
    }
    async updateStats() {
        try {
            const response = await fetch("/admin/perfil/stats-realtime", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!response.ok) return;
            const data = await response.json();
            if (data.success) this.renderStats(data.stats);
        } catch (error) {
            console.error("Error actualizando stats:", error);
        }
    }
    renderStats(stats) {
        const updateStat = (id, value) => {
            const element = document.querySelector(`[data-stat="${id}"]`);
            if (element && element.textContent !== value.toString()) {
                element.textContent = value;
                element.classList.add("perfil-stat-update");
                setTimeout(
                    () => element.classList.remove("perfil-stat-update"),
                    600
                );
            }
        };
        if (stats.pedidos_cliente !== undefined)
            updateStat("pedidos-cliente", stats.pedidos_cliente);
        if (stats.pedidos_vendedor !== undefined)
            updateStat("pedidos-vendedor", stats.pedidos_vendedor);
        if (stats.total_referidos !== undefined)
            updateStat("total-referidos", stats.total_referidos);
    }
    async updateActivity() {
        try {
            const response = await fetch("/admin/perfil/activity-realtime", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!response.ok) return;
            const data = await response.json();
            if (data.success) this.renderActivity(data.actividad);
        } catch (error) {
            console.error("Error actualizando actividad:", error);
        }
    }
    renderActivity(actividades) {
        const container = document.getElementById("actividad-realtime");
        if (!container) return;
        if (!actividades || actividades.length === 0) {
            container.innerHTML = `<div class="text-center py-4"><i class="bi bi-activity fs-1 text-muted"></i><p class="text-muted mt-2">No hay actividad reciente</p></div>`;
            return;
        }
        let html = "";
        actividades.slice(0, 10).forEach((act, index) => {
            const timeAgo = act.tiempo || "Reciente";
            html += `<div class="activity-item" style="animation-delay:${
                index * 50
            }ms"><div class="d-flex justify-content-between align-items-center"><div><small class="fw-semibold" style="color:black">${
                act.descripcion
            }</small><br><span class="activity-badge bg-${this.getActivityBadgeColor(
                act.tipo
            )}">${
                act.tipo
            }</span></div><small class="text-muted">${timeAgo}</small></div></div>`;
        });
        container.innerHTML = html;
    }
    getActivityBadgeColor(tipo) {
        const colors = {
            pedido: "warning",
            usuario: "success",
            comision: "info",
            sistema: "secondary",
        };
        return colors[tipo] || "secondary";
    }
    createModal(id, options = {}) {
        const {
            title,
            content,
            buttons = [],
            type = "primary",
            onClose,
        } = options;
        const modal = document.createElement("div");
        modal.className = "perfil-modal";
        modal.id = id;
        modal.innerHTML = `<div class="perfil-modal-content"><div class="perfil-modal-header"><h3 class="perfil-modal-title">${title}</h3><button class="perfil-modal-close">&times;</button></div><div class="perfil-modal-body">${content}</div>${
            buttons.length
                ? `<div class="perfil-modal-footer">${buttons
                      .map(
                          (btn) =>
                              `<button class="perfil-modal-btn perfil-modal-btn-${
                                  btn.type
                              }" ${btn.id ? `id="${btn.id}"` : ""}>${
                                  btn.text
                              }</button>`
                      )
                      .join("")}</div>`
                : ""
        }</div>`;
        modal
            .querySelector(".perfil-modal-close")
            .addEventListener("click", () => this.closeModal(id));
        buttons.forEach((btn) => {
            if (btn.id) {
                const button = modal.querySelector(`#${btn.id}`);
                if (button && btn.onClick)
                    button.addEventListener("click", btn.onClick);
            }
        });
        if (onClose) modal.dataset.onClose = onClose;
        this.modals.set(id, modal);
        document.body.appendChild(modal);
        return modal;
    }
    showModal(id) {
        const modal = this.modals.get(id);
        if (modal) {
            this.backdrop.classList.add("active");
            modal.classList.add("active");
            document.body.style.overflow = "hidden";
        }
    }
    closeModal(id) {
        const modal = this.modals.get(id);
        if (modal) {
            modal.classList.remove("active");
            this.backdrop.classList.remove("active");
            document.body.style.overflow = "";
            if (modal.dataset.onClose) eval(modal.dataset.onClose);
        }
    }
    closeAllModals() {
        this.modals.forEach((modal, id) => this.closeModal(id));
    }
    showToast(message, type = "info", duration = 5000) {
        const toast = document.createElement("div");
        toast.className = `perfil-toast ${type}`;
        toast.innerHTML = `<span>${message}</span>`;
        this.toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
    showLoading() {
        let loading = document.querySelector(".perfil-loading");
        if (!loading) {
            loading = document.createElement("div");
            loading.className = "perfil-loading";
            loading.innerHTML = '<div class="perfil-spinner"></div>';
            document.body.appendChild(loading);
        }
        loading.classList.add("active");
    }
    hideLoading() {
        const loading = document.querySelector(".perfil-loading");
        if (loading) loading.classList.remove("active");
    }
    setupAvatarPreview() {
        const avatarInput = document.querySelector('input[name="avatar"]');
        if (avatarInput) {
            avatarInput.addEventListener("change", (e) => {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        this.showToast(
                            "Archivo muy grande. Máximo 2MB.",
                            "error"
                        );
                        e.target.value = "";
                        return;
                    }
                    if (!file.type.match("image.*")) {
                        this.showToast(
                            "Selecciona un archivo de imagen válido.",
                            "error"
                        );
                        e.target.value = "";
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const avatarContainer =
                            document.getElementById("avatar-container");
                        const currentAvatar =
                            document.getElementById("user-avatar") ||
                            document.getElementById("user-avatar-placeholder");
                        if (currentAvatar) {
                            if (currentAvatar.tagName === "DIV") {
                                const img = document.createElement("img");
                                img.src = e.target.result;
                                img.className = "perfil-avatar";
                                img.alt = "Avatar Preview";
                                img.id = "user-avatar";
                                currentAvatar.replaceWith(img);
                            } else {
                                currentAvatar.src = e.target.result;
                            }
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }
    setupFormConfirmations() {
        document
            .querySelectorAll(".needs-profile-confirmation")
            .forEach((form) => {
                form.addEventListener("submit", (e) => {
                    if (
                        !confirm(
                            form.dataset.confirmMessage || "¿Estás seguro?"
                        )
                    )
                        e.preventDefault();
                });
            });
    }
    setupActionButtons() {
        document.querySelectorAll("[data-perfil-action]").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                const action = e.target.dataset.perfilAction;
                switch (action) {
                    case "show-activity":
                        this.showActivityModal();
                        break;
                    case "download-data":
                        this.downloadData();
                        break;
                }
            });
        });
        if (document.getElementById("eliminar-avatar-btn")) {
            document
                .getElementById("eliminar-avatar-btn")
                .addEventListener("click", () => {
                    this.confirmDeleteAvatar();
                });
        }
    }
    async showActivityModal() {
        this.showLoading();
        try {
            const response = await fetch(window.perfilRoutes.activity);
            const data = await response.json();
            this.hideLoading();
            if (data.success) {
                this.createModal("activity-detail", {
                    title: "Actividad Completa",
                    content: `<div class="perfil-activity-feed">${
                        data.data.pedidos
                            ?.map(
                                (pedido) =>
                                    `<div class="perfil-activity-item"><div class="d-flex justify-content-between"><div><strong>${
                                        pedido.numero_pedido
                                    }</strong><br><span class="activity-badge bg-${
                                        pedido.estado === "entregado"
                                            ? "success"
                                            : pedido.estado === "cancelado"
                                            ? "danger"
                                            : "warning"
                                    }">${
                                        pedido.estado
                                    }</span><small class="text-muted ms-2">$${this.formatCurrency(
                                        pedido.total_final
                                    )}</small></div><small class="text-muted">${new Date(
                                        pedido.created_at
                                    ).toLocaleDateString()}</small></div></div>`
                            )
                            .join("") ||
                        '<p class="text-muted text-center">No hay actividad reciente</p>'
                    }</div>`,
                    buttons: [
                        {
                            text: "Cerrar",
                            type: "secondary",
                            onClick: () => this.closeModal("activity-detail"),
                        },
                    ],
                });
                this.showModal("activity-detail");
            } else {
                this.showToast("Error al cargar actividad", "error");
            }
        } catch (error) {
            this.hideLoading();
            this.showToast("Error de conexión", "error");
        }
    }
    async downloadData() {
        this.showLoading();
        try {
            const response = await fetch(window.perfilRoutes.downloadData);
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "perfil-datos.pdf";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            this.hideLoading();
            this.showToast("Datos descargados exitosamente", "success");
        } catch (error) {
            this.hideLoading();
            this.showToast("Error al descargar datos", "error");
        }
    }
    confirmDeleteAvatar() {
        this.createModal("confirm-delete-avatar", {
            title: "Eliminar Avatar",
            content:
                "<p>¿Estás seguro de que quieres eliminar tu foto de perfil?</p>",
            type: "warning",
            buttons: [
                {
                    text: "Eliminar",
                    type: "danger",
                    id: "confirm-delete",
                    onClick: () => {
                        this.deleteAvatar();
                    },
                },
                {
                    text: "Cancelar",
                    type: "secondary",
                    onClick: () => this.closeModal("confirm-delete-avatar"),
                },
            ],
        });
        this.showModal("confirm-delete-avatar");
    }
    async deleteAvatar() {
        try {
            const response = await fetch(window.perfilRoutes.deleteAvatar, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": window.perfilCSRF },
            });
            const data = await response.json();
            if (data.success) {
                this.closeModal("confirm-delete-avatar");
                document.getElementById("user-avatar")?.remove();
                document.getElementById("user-avatar-form")?.remove();
                const placeholder = document.getElementById(
                    "user-avatar-placeholder"
                );
                if (placeholder) placeholder.style.display = "flex";
                this.showToast("Avatar eliminado correctamente", "success");
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showToast("Error al eliminar avatar", "error");
            }
        } catch (error) {
            this.showToast("Error de conexión", "error");
        }
    }
    formatCurrency(amount) {
        return new Intl.NumberFormat("es-CO").format(parseFloat(amount) || 0);
    }
    destroy() {
        this.stopRealTime();
        this.toastContainer?.remove();
        this.backdrop?.remove();
        this.modals.forEach((modal) => modal.remove());
        this.modals.clear();
    }
}
window.PerfilManager = new PerfilManager();
document.addEventListener("DOMContentLoaded", () => {
    const manager = window.PerfilManager;
    manager.setupAvatarPreview();
    manager.setupFormConfirmations();
    manager.setupActionButtons();
});
window.addEventListener("beforeunload", () => {
    window.PerfilManager?.destroy();
});
