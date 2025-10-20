/**
 * Mi Perfil - Professional JavaScript
 * Sistema de Red de Ventas - Vendedor
 */

// Tab Management
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    initializeAnimations();
});

// Tab System
function initializeTabs() {
    const tabs = document.querySelectorAll('.profile-tab');
    const contents = document.querySelectorAll('.profile-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.getAttribute('data-tab');
            
            // Remove active from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            // Add active to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetId)?.classList.add('active');
        });
    });
}

// Animations on Scroll
function initializeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe stat cards
    document.querySelectorAll('.stat-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
}

// Toast Notification System
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icon = getToastIcon(type);
    
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="bi ${icon}"></i>
        </div>
        <div class="toast-message">${message}</div>
    `;
    
    container.appendChild(toast);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    document.body.appendChild(container);
    return container;
}

function getToastIcon(type) {
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    return icons[type] || icons.info;
}

// Modal Management
function showPasswordModal() {
    const modal = document.getElementById('passwordModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closePasswordModal() {
    const modal = document.getElementById('passwordModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        // Clear form
        document.getElementById('passwordForm')?.reset();
    }
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closePasswordModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePasswordModal();
    }
});

// Copy to Clipboard
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text)
            .then(() => {
                showToast('Código copiado al portapapeles', 'success');
            })
            .catch(() => {
                fallbackCopyToClipboard(text);
            });
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();
    
    try {
        document.execCommand('copy');
        showToast('Código copiado al portapapeles', 'success');
    } catch (err) {
        showToast('Error al copiar el código', 'error');
    }
    
    document.body.removeChild(textArea);
}

// Settings Save Function
function saveSettings() {
    const settings = {
        perfil_publico: document.getElementById('perfil_publico')?.checked || false,
        mostrar_telefono: document.getElementById('mostrar_telefono')?.checked || false,
        mostrar_stats: document.getElementById('mostrar_stats')?.checked || false
    };

    showToast('Guardando configuración...', 'info');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        showToast('Error: Token de seguridad no encontrado', 'error');
        return;
    }

    fetch(window.location.origin + '/vendedor/perfil', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast('Configuración guardada exitosamente', 'success');
        } else {
            showToast(data.message || 'Error al guardar configuración', 'error');
        }
    })
    .catch(error => {
        showToast('Error de conexión al guardar', 'error');
        console.error('Error:', error);
    });
}

// Avatar Upload Handler
function handleAvatarUpload(input) {
    if (!input.files || !input.files[0]) return;
    
    const file = input.files[0];
    const maxSize = 2 * 1024 * 1024; // 2MB
    
    // Validate file size
    if (file.size > maxSize) {
        showToast('La imagen no debe superar 2MB', 'error');
        input.value = '';
        return;
    }
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        showToast('Solo se permiten imágenes JPG, PNG o GIF', 'error');
        input.value = '';
        return;
    }
    
    const formData = new FormData();
    formData.append('avatar', file);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        showToast('Error: Token de seguridad no encontrado', 'error');
        return;
    }
    
    formData.append('_token', csrfToken);
    formData.append('_method', 'PUT');
    
    showToast('Subiendo avatar...', 'info');
    
    fetch(window.location.origin + '/vendedor/perfil', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast('Avatar actualizado exitosamente', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error al actualizar avatar', 'error');
        }
    })
    .catch(error => {
        showToast('Error al subir la imagen', 'error');
        console.error('Error:', error);
    });
}

// Export Data
function exportData() {
    showToast('Preparando descarga de datos...', 'info');
    window.location.href = window.location.origin + '/vendedor/perfil/exportar-datos';
}

// Refresh Activity
function refreshActivity() {
    showToast('Actualizando actividad...', 'info');
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Form Validation for Password Change
document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password')?.value;
            const confirmation = document.querySelector('input[name="password_confirmation"]')?.value;
            
            if (password !== confirmation) {
                e.preventDefault();
                showToast('Las contraseñas no coinciden', 'error');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                showToast('La contraseña debe tener al menos 8 caracteres', 'error');
                return false;
            }
        });
    }
});

// Smooth Scroll for Quick Actions
document.querySelectorAll('.quick-action').forEach(link => {
    link.addEventListener('click', function(e) {
        // Add a subtle animation
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
});

// Progress Bar Animation
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const width = entry.target.style.width;
                entry.target.style.width = '0%';
                setTimeout(() => {
                    entry.target.style.width = width;
                }, 100);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    progressBars.forEach(bar => observer.observe(bar));
}

// Initialize progress bar animations
document.addEventListener('DOMContentLoaded', animateProgressBars);

// Add keyboard navigation for tabs
document.addEventListener('keydown', function(e) {
    if (!e.target.classList.contains('profile-tab')) return;
    
    const tabs = Array.from(document.querySelectorAll('.profile-tab'));
    const currentIndex = tabs.indexOf(e.target);
    
    let nextIndex;
    
    if (e.key === 'ArrowLeft') {
        e.preventDefault();
        nextIndex = currentIndex > 0 ? currentIndex - 1 : tabs.length - 1;
    } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        nextIndex = currentIndex < tabs.length - 1 ? currentIndex + 1 : 0;
    }
    
    if (nextIndex !== undefined) {
        tabs[nextIndex].focus();
        tabs[nextIndex].click();
    }
});

// Handle window resize for responsive adjustments
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        // Recalculate any layout-dependent elements
        adjustLayoutForViewport();
    }, 250);
});

function adjustLayoutForViewport() {
    const isMobile = window.innerWidth < 768;
    const statsGrid = document.querySelector('.profile-stats-grid');
    
    if (statsGrid) {
        if (isMobile) {
            statsGrid.style.gridTemplateColumns = '1fr';
        } else {
            statsGrid.style.gridTemplateColumns = '';
        }
    }
}

// Initialize on load
window.addEventListener('load', function() {
    adjustLayoutForViewport();
});

// Utility function to format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
}

// Utility function to format numbers
function formatNumber(value, decimals = 0) {
    return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(value);
}

// Export functions to global scope
window.showToast = showToast;
window.showPasswordModal = showPasswordModal;
window.closePasswordModal = closePasswordModal;
window.copyToClipboard = copyToClipboard;
window.saveSettings = saveSettings;
window.handleAvatarUpload = handleAvatarUpload;
window.exportData = exportData;
window.refreshActivity = refreshActivity;
window.formatCurrency = formatCurrency;
window.formatNumber = formatNumber;
