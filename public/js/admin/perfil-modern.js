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
        this.setupImagePreview();
        this.setupForms();
        console.log("PerfilManager inicializado - Modo profesional activo");
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
        this.toastContainer.style.cssText =
            "position:fixed;top:20px;right:20px;z-index:10000;pointer-events:none";
        document.body.appendChild(this.toastContainer);
    }
    setupRealTime() {
        this.statsInterval = 15000;
        this.activityInterval = 10000;
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
            const r = await fetch("/admin/perfil/stats-realtime", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!r.ok) return;
            const d = await r.json();
            if (d.success) this.renderStats(d.stats);
        } catch (e) {
            console.error("Error actualizando stats:", e);
        }
    }
    renderStats(s) {
        const u = (id, v) => {
            const el = document.querySelector(`[data-stat="${id}"]`);
            if (el && el.textContent !== v.toString()) {
                const old = el.textContent;
                el.textContent = v;
                el.style.transform = "scale(1.15)";
                el.style.color = "var(--wine)";
                setTimeout(() => {
                    el.style.transform = "scale(1)";
                    el.style.color = "";
                }, 400);
            }
        };
        if (s.pedidos_cliente !== undefined)
            u("pedidos-cliente", s.pedidos_cliente);
        if (s.pedidos_vendedor !== undefined)
            u("pedidos-vendedor", s.pedidos_vendedor);
        if (s.total_referidos !== undefined)
            u("total-referidos", s.total_referidos);
    }
    async updateActivity() {
        try {
            const r = await fetch("/admin/perfil/activity-realtime", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!r.ok) return;
            const d = await r.json();
            if (d.success) this.renderActivity(d.actividad);
        } catch (e) {
            console.error("Error actualizando actividad:", e);
        }
    }
    renderActivity(a) {
        const c = document.getElementById("actividad-realtime");
        if (!c) return;
        if (!a || a.length === 0) {
            c.innerHTML =
                '<div class="text-center py-4"><i class="bi bi-activity fs-1 text-muted"></i><p class="text-muted mt-2">No hay actividad reciente</p></div>';
            return;
        }
        let h = "";
        a.slice(0, 10).forEach((act, i) => {
            const t = act.tiempo || "Reciente";
            h += `<div class="activity-item fade-in-up" style="animation-delay:${
                i * 50
            }ms"><div class="d-flex justify-content-between align-items-center"><div><small class="fw-semibold text-dark">${
                act.descripcion
            }</small><br><span class="activity-badge bg-${this.getActivityBadgeColor(
                act.tipo
            )} text-white">${
                act.tipo
            }</span></div><small class="text-muted">${t}</small></div></div>`;
        });
        c.innerHTML = h;
    }
    getActivityBadgeColor(t) {
        const c = {
            pedido: "warning",
            usuario: "success",
            comision: "info",
            sistema: "secondary",
        };
        return c[t] || "secondary";
    }
    setupImagePreview() {
        const input = document.querySelector('input[name="avatar"]');
        const container = document.getElementById("avatar-container");
        if (!input || !container) return;
        input.addEventListener("change", (e) => {
            const f = e.target.files[0];
            if (!f) return;
            if (f.size > 5 * 1024 * 1024) {
                this.showToast(
                    "Imagen muy grande. Máximo 5MB permitidos.",
                    "error",
                    4000
                );
                e.target.value = "";
                return;
            }
            if (!f.type.match("image.*")) {
                this.showToast(
                    "Formato inválido. Solo imágenes permitidas.",
                    "error",
                    4000
                );
                e.target.value = "";
                return;
            }
            const reader = new FileReader();
            reader.onload = (ev) => {
                let img = container.querySelector("img#user-avatar-form");
                const placeholder = container.querySelector(
                    "#user-avatar-placeholder-form"
                );
                const deleteBtn = container.querySelector(
                    "#eliminar-avatar-btn"
                );
                if (!img) {
                    img = document.createElement("img");
                    img.id = "user-avatar-form";
                    img.className = "perfil-avatar";
                    img.width = 150;
                    img.height = 150;
                    img.style.cssText =
                        "width:150px;height:150px;border-radius:50%;object-fit:cover;display:block;border:4px solid rgba(255,255,255,.3);box-shadow:0 8px 16px rgba(0,0,0,.2);transition:all .3s ease";
                    if (placeholder) {
                        placeholder.replaceWith(img);
                    } else {
                        container.innerHTML = "";
                        container.appendChild(img);
                    }
                }
                img.src = ev.target.result;
                img.alt = "Avatar Preview";
                img.style.transform = "scale(1.05)";
                setTimeout(() => (img.style.transform = "scale(1)"), 300);
                if (!deleteBtn) {
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className =
                        "btn btn-sm btn-danger position-absolute rounded-circle";
                    btn.id = "eliminar-avatar-btn-temp";
                    btn.title = "Eliminar foto";
                    btn.style.cssText =
                        "top:0;right:0;width:36px;height:36px;padding:0;z-index:10;box-shadow:0 2px 8px rgba(0,0,0,0.3)";
                    btn.innerHTML = '<i class="bi bi-trash3"></i>';
                    btn.addEventListener("click", () =>
                        this.confirmDeleteAvatar()
                    );
                    container.appendChild(btn);
                }
                const successMsg = document.getElementById("preview-success");
                if (successMsg) {
                    successMsg.classList.remove("d-none");
                    setTimeout(() => successMsg.classList.add("d-none"), 3000);
                }
                this.showToast(
                    "Vista previa cargada correctamente",
                    "success",
                    2000
                );
            };
            reader.readAsDataURL(f);
        });
    }
    setupForms() {
        const profileForm = document.getElementById("updateProfileForm");
        const passwordForm = document.getElementById("updatePasswordForm");
        const notifForm = document.getElementById("updateNotificationsForm");
        if (profileForm) {
            profileForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.showConfirmModal({
                    title: "Actualizar Información Personal",
                    message:
                        "¿Confirmas que deseas actualizar tu información personal?",
                    icon: "bi-person-check-fill",
                    iconColor: "#722F37",
                    confirmText: "Sí, Actualizar",
                    confirmClass: "perfil-modal-btn-primary",
                    onConfirm: () => this.submitProfileForm(profileForm),
                });
            });
        }
        if (passwordForm) {
            passwordForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.showConfirmModal({
                    title: "Cambiar Contraseña",
                    message:
                        "¿Estás seguro de cambiar tu contraseña? Deberás iniciar sesión nuevamente.",
                    icon: "bi-shield-lock-fill",
                    iconColor: "#f59e0b",
                    confirmText: "Sí, Cambiar",
                    confirmClass: "perfil-modal-btn-warning",
                    onConfirm: () => this.submitPasswordForm(passwordForm),
                });
            });
        }
        if (notifForm) {
            notifForm.addEventListener("submit", (e) => {
                e.preventDefault();
                this.showConfirmModal({
                    title: "Actualizar Notificaciones",
                    message:
                        "¿Deseas aplicar las nuevas preferencias de notificación?",
                    icon: "bi-bell-fill",
                    iconColor: "#10b981",
                    confirmText: "Sí, Actualizar",
                    confirmClass: "perfil-modal-btn-success",
                    onConfirm: () => this.submitNotifForm(notifForm),
                });
            });
        }
        const deleteBtn = document.getElementById("eliminar-avatar-btn");
        if (deleteBtn) {
            deleteBtn.addEventListener("click", () =>
                this.confirmDeleteAvatar()
            );
        }
    }
    showConfirmModal(opts) {
        const {
            title,
            message,
            icon,
            iconColor,
            confirmText,
            confirmClass,
            onConfirm,
        } = opts;
        this.createModal("confirm-action", {
            title: title,
            content: `<div class="text-center"><div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:80px;height:80px;background:${iconColor}15"><i class="${icon}" style="font-size:2.5rem;color:${iconColor}"></i></div><p class="mb-0" style="font-size:1.05rem;color:var(--gray-700)">${message}</p></div>`,
            buttons: [
                {
                    text: "Cancelar",
                    type: "secondary",
                    onClick: () => this.closeModal("confirm-action"),
                },
                {
                    text: confirmText,
                    type: confirmClass.replace("perfil-modal-btn-", ""),
                    id: "btn-confirm-action",
                    onClick: () => {
                        this.closeModal("confirm-action");
                        if (onConfirm) onConfirm();
                    },
                },
            ],
        });
        this.showModal("confirm-action");
    }
    async submitProfileForm(form) {
        this.showLoading("Actualizando perfil...");
        try {
            const formData = new FormData(form);
            const r = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": window.perfilCSRF,
                },
            });
            const d = await r.json();
            this.hideLoading();
            if (d.success) {
                this.showToast(
                    d.message || "Perfil actualizado correctamente",
                    "success",
                    3000
                );
                if (d.user && d.user.avatar) {
                    const avatars = document.querySelectorAll(".perfil-avatar");
                    avatars.forEach((av) => {
                        if (av.tagName === "IMG") av.src = d.user.avatar;
                    });
                }
                setTimeout(() => location.reload(), 2000);
            } else {
                this.showToast(
                    d.message || "Error al actualizar perfil",
                    "error",
                    4000
                );
            }
        } catch (e) {
            this.hideLoading();
            this.showToast(
                "Error de conexión. Intenta nuevamente.",
                "error",
                4000
            );
        }
    }
    async submitPasswordForm(form) {
        this.showLoading("Cambiando contraseña...");
        try {
            const formData = new FormData(form);
            const r = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": window.perfilCSRF,
                },
            });
            const d = await r.json();
            this.hideLoading();
            if (d.success) {
                this.showToast(
                    d.message || "Contraseña actualizada correctamente",
                    "success",
                    3000
                );
                form.reset();
                document
                    .getElementById("changePassword")
                    ?.classList.remove("show");
            } else {
                this.showToast(
                    d.message || "Error al cambiar contraseña",
                    "error",
                    4000
                );
            }
        } catch (e) {
            this.hideLoading();
            this.showToast(
                "Error de conexión. Intenta nuevamente.",
                "error",
                4000
            );
        }
    }
    async submitNotifForm(form) {
        this.showLoading("Actualizando notificaciones...");
        try {
            const formData = new FormData(form);
            const r = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": window.perfilCSRF,
                },
            });
            const d = await r.json();
            this.hideLoading();
            if (d.success) {
                this.showToast(
                    d.message || "Notificaciones actualizadas",
                    "success",
                    3000
                );
            } else {
                this.showToast(
                    d.message || "Error al actualizar",
                    "error",
                    4000
                );
            }
        } catch (e) {
            this.hideLoading();
            this.showToast("Error de conexión", "error", 4000);
        }
    }
    createModal(id, opts) {
        const { title, content, buttons = [] } = opts;
        const m = document.createElement("div");
        m.className = "perfil-modal";
        m.id = id;
        m.innerHTML = `<div class="perfil-modal-content"><div class="perfil-modal-header"><h3 class="perfil-modal-title">${title}</h3><button class="perfil-modal-close" aria-label="Cerrar"><i class="bi bi-x-lg"></i></button></div><div class="perfil-modal-body">${content}</div>${
            buttons.length
                ? `<div class="perfil-modal-footer">${buttons
                      .map(
                          (b) =>
                              `<button class="perfil-modal-btn perfil-modal-btn-${
                                  b.type
                              }" ${b.id ? `id="${b.id}"` : ""}><i class="bi ${
                                  b.type === "secondary"
                                      ? "bi-x-circle"
                                      : "bi-check-circle"
                              } me-1"></i>${b.text}</button>`
                      )
                      .join("")}</div>`
                : ""
        }</div>`;
        m.querySelector(".perfil-modal-close").addEventListener("click", () =>
            this.closeModal(id)
        );
        buttons.forEach((b) => {
            if (b.id) {
                const btn = m.querySelector(`#${b.id}`);
                if (btn && b.onClick) btn.addEventListener("click", b.onClick);
            }
        });
        this.modals.set(id, m);
        document.body.appendChild(m);
        return m;
    }
    showModal(id) {
        const m = this.modals.get(id);
        if (m) {
            this.backdrop.classList.add("active");
            m.classList.add("active");
            document.body.style.overflow = "hidden";
        }
    }
    closeModal(id) {
        const m = this.modals.get(id);
        if (m) {
            m.classList.remove("active");
            this.backdrop.classList.remove("active");
            document.body.style.overflow = "";
            setTimeout(() => {
                m.remove();
                this.modals.delete(id);
            }, 300);
        }
    }
    closeAllModals() {
        this.modals.forEach((m, id) => this.closeModal(id));
    }
    showToast(msg, type = "info", dur = 5000) {
        const t = document.createElement("div");
        t.className = `perfil-toast ${type}`;
        t.style.cssText = "pointer-events:all;margin-bottom:.75rem";
        const icons = {
            success: "bi-check-circle-fill",
            error: "bi-x-circle-fill",
            warning: "bi-exclamation-triangle-fill",
            info: "bi-info-circle-fill",
        };
        t.innerHTML = `<i class="bi ${icons[type]} perfil-toast-icon"></i><span class="perfil-toast-message">${msg}</span>`;
        this.toastContainer.appendChild(t);
        setTimeout(() => {
            t.style.opacity = "0";
            t.style.transform = "translateX(100px)";
            setTimeout(() => t.remove(), 300);
        }, dur);
    }
    showLoading(txt = "Cargando...") {
        let l = document.querySelector(".perfil-loading-overlay");
        if (!l) {
            l = document.createElement("div");
            l.className = "perfil-loading-overlay";
            l.innerHTML = `<div class="perfil-loading-spinner"></div><div class="perfil-loading-text">${txt}</div>`;
            document.body.appendChild(l);
        } else {
            l.querySelector(".perfil-loading-text").textContent = txt;
        }
        l.classList.add("active");
    }
    hideLoading() {
        const l = document.querySelector(".perfil-loading-overlay");
        if (l) l.classList.remove("active");
    }
    confirmDeleteAvatar() {
        this.createModal("confirm-delete-avatar", {
            title: "Eliminar Foto de Perfil",
            content:
                '<div class="text-center"><div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:80px;height:80px;background:#ef444415"><i class="bi bi-trash3-fill" style="font-size:2.5rem;color:#ef4444"></i></div><p class="mb-0" style="font-size:1.05rem;color:var(--gray-700)">¿Estás seguro de eliminar tu foto de perfil? Esta acción no se puede deshacer.</p></div>',
            buttons: [
                {
                    text: "Cancelar",
                    type: "secondary",
                    onClick: () => this.closeModal("confirm-delete-avatar"),
                },
                {
                    text: "Sí, Eliminar",
                    type: "danger",
                    id: "btn-delete-avatar",
                    onClick: () => this.deleteAvatar(),
                },
            ],
        });
        this.showModal("confirm-delete-avatar");
    }
    async deleteAvatar() {
        this.closeModal("confirm-delete-avatar");
        this.showLoading("Eliminando avatar...");
        try {
            const r = await fetch(window.perfilRoutes.deleteAvatar, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": window.perfilCSRF,
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            const d = await r.json();
            this.hideLoading();
            if (d.success) {
                this.showToast(
                    "Avatar eliminado correctamente",
                    "success",
                    3000
                );
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showToast(
                    d.message || "Error al eliminar avatar",
                    "error",
                    4000
                );
            }
        } catch (e) {
            this.hideLoading();
            this.showToast("Error de conexión", "error", 4000);
        }
    }
    setupActionButtons() {
        document.querySelectorAll("[data-perfil-action]").forEach((b) => {
            b.addEventListener("click", (e) => {
                e.preventDefault();
                const act = e.currentTarget.dataset.perfilAction;
                switch (act) {
                    case "show-activity":
                        this.showActivityModal();
                        break;
                    case "download-data":
                        this.downloadData();
                        break;
                }
            });
        });
    }
    async showActivityModal() {
        this.showLoading("Cargando actividad...");
        try {
            const r = await fetch(window.perfilRoutes.activity);
            const d = await r.json();
            this.hideLoading();
            if (d.success) {
                let html =
                    '<div class="perfil-activity-feed" style="max-height:400px;overflow-y:auto">';
                if (d.data.pedidos && d.data.pedidos.length > 0) {
                    html += '<h6 class="mb-3 text-wine">Pedidos Recientes</h6>';
                    d.data.pedidos.forEach((p) => {
                        const stColor =
                            p.estado === "entregado"
                                ? "success"
                                : p.estado === "cancelado"
                                ? "danger"
                                : "warning";
                        html += `<div class="perfil-activity-detail-card"><div class="d-flex justify-content-between align-items-center"><div><div class="perfil-activity-detail-title">${
                            p.numero_pedido
                        }</div><div class="perfil-activity-detail-subtitle"><span class="badge bg-${stColor}">${
                            p.estado
                        }</span> <span class="text-muted">$${this.formatCurrency(
                            p.total_final
                        )}</span></div></div><div class="perfil-activity-detail-meta">${new Date(
                            p.created_at
                        ).toLocaleDateString("es-CO")}</div></div></div>`;
                    });
                } else {
                    html +=
                        '<div class="perfil-empty-state"><div class="perfil-empty-state-icon"><i class="bi bi-inbox"></i></div><div class="perfil-empty-state-title">Sin actividad</div><div class="perfil-empty-state-text">No hay pedidos registrados</div></div>';
                }
                html += "</div>";
                this.createModal("activity-detail", {
                    title: "Actividad Completa",
                    content: html,
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
        } catch (e) {
            this.hideLoading();
            this.showToast("Error de conexión", "error");
        }
    }
    async downloadData() {
        this.showLoading("Generando PDF...");
        try {
            window.location.href = window.perfilRoutes.downloadData;
            this.hideLoading();
            this.showToast("Descarga iniciada correctamente", "success", 3000);
        } catch (e) {
            this.hideLoading();
            this.showToast("Error al descargar datos", "error");
        }
    }
    formatCurrency(a) {
        return new Intl.NumberFormat("es-CO").format(parseFloat(a) || 0);
    }
    destroy() {
        this.stopRealTime();
        this.toastContainer?.remove();
        this.backdrop?.remove();
        this.modals.forEach((m) => m.remove());
        this.modals.clear();
    }
}
window.PerfilManager = new PerfilManager();
document.addEventListener("DOMContentLoaded", () => {
    const m = window.PerfilManager;
    m.setupActionButtons();
});
window.addEventListener("beforeunload", () => {
    window.PerfilManager?.destroy();
});
