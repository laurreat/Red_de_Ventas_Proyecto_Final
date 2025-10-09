/**
 * Formularios de Roles - JavaScript Interactivo
 * Manejo de permisos por categorías, validaciones y contador en tiempo real
 * Versión: 2.0
 */

class RoleFormsManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupCategoryToggles();
        this.setupPermissionCheckboxes();
        this.setupNameNormalization();
        this.setupFormValidation();
        this.updateAllCounters(); // Initial count
        console.log('✅ Role Forms Manager initialized');
    }

    /* ========================================
       CATEGORY TOGGLES
    ======================================== */

    setupCategoryToggles() {
        const categoryToggles = document.querySelectorAll('.category-toggle');

        categoryToggles.forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                const category = e.target.dataset.category;
                const isChecked = e.target.checked;

                // Toggle all permissions in this category
                const permissionCheckboxes = document.querySelectorAll(
                    `.permission-checkbox[data-category="${category}"]`
                );

                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;

                    // Add visual feedback
                    if (isChecked) {
                        checkbox.parentElement.style.animation = 'fadeIn 0.3s ease-out';
                    }
                });

                // Update counters
                this.updateCategoryCounter(category);
                this.updateTotalCounter();
            });

            // Make the entire header clickable
            const header = toggle.closest('.permission-category-header');
            if (header) {
                header.addEventListener('click', (e) => {
                    // Only trigger if not clicking the checkbox directly
                    if (e.target !== toggle) {
                        toggle.checked = !toggle.checked;
                        toggle.dispatchEvent(new Event('change'));
                    }
                });
            }
        });
    }

    /* ========================================
       PERMISSION CHECKBOXES
    ======================================== */

    setupPermissionCheckboxes() {
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const category = e.target.dataset.category;

                // Update category toggle state
                this.updateCategoryToggleState(category);

                // Update counters
                this.updateCategoryCounter(category);
                this.updateTotalCounter();

                // Visual feedback
                if (e.target.checked) {
                    const label = e.target.nextElementSibling;
                    if (label) {
                        label.style.animation = 'pulse 0.3s ease-out';
                        setTimeout(() => {
                            label.style.animation = '';
                        }, 300);
                    }
                }
            });
        });
    }

    updateCategoryToggleState(category) {
        const categoryToggle = document.querySelector(`.category-toggle[data-category="${category}"]`);
        if (!categoryToggle) return;

        const permissionCheckboxes = document.querySelectorAll(
            `.permission-checkbox[data-category="${category}"]`
        );

        const checkedCount = Array.from(permissionCheckboxes).filter(cb => cb.checked).length;
        const totalCount = permissionCheckboxes.length;

        // Set category toggle state
        if (checkedCount === 0) {
            categoryToggle.checked = false;
            categoryToggle.indeterminate = false;
        } else if (checkedCount === totalCount) {
            categoryToggle.checked = true;
            categoryToggle.indeterminate = false;
        } else {
            categoryToggle.checked = false;
            categoryToggle.indeterminate = true;
        }
    }

    /* ========================================
       COUNTERS
    ======================================== */

    updateCategoryCounter(category) {
        const counterElement = document.querySelector(
            `.category-selected-count[data-category="${category}"]`
        );
        if (!counterElement) return;

        const permissionCheckboxes = document.querySelectorAll(
            `.permission-checkbox[data-category="${category}"]`
        );

        const checkedCount = Array.from(permissionCheckboxes).filter(cb => cb.checked).length;

        // Animate counter change
        counterElement.style.transform = 'scale(1.2)';
        counterElement.textContent = checkedCount;

        setTimeout(() => {
            counterElement.style.transform = 'scale(1)';
        }, 200);
    }

    updateTotalCounter() {
        const totalCounterElement = document.getElementById('totalSelectedCount');
        const selectedCountElement = document.getElementById('selectedCount');

        if (!totalCounterElement && !selectedCountElement) return;

        const allPermissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        const checkedCount = Array.from(allPermissionCheckboxes).filter(cb => cb.checked).length;

        // Update both counters
        if (totalCounterElement) {
            totalCounterElement.style.transform = 'scale(1.2)';
            totalCounterElement.textContent = checkedCount;

            setTimeout(() => {
                totalCounterElement.style.transform = 'scale(1)';
            }, 200);
        }

        if (selectedCountElement) {
            selectedCountElement.style.transform = 'scale(1.1)';
            selectedCountElement.textContent = `${checkedCount} seleccionados`;

            setTimeout(() => {
                selectedCountElement.style.transform = 'scale(1)';
            }, 200);
        }
    }

    updateAllCounters() {
        // Update all category counters
        const categoryToggles = document.querySelectorAll('.category-toggle');
        categoryToggles.forEach(toggle => {
            const category = toggle.dataset.category;
            this.updateCategoryCounter(category);
            this.updateCategoryToggleState(category);
        });

        // Update total counter
        this.updateTotalCounter();
    }

    /* ========================================
       NAME NORMALIZATION
    ======================================== */

    setupNameNormalization() {
        const nameInput = document.getElementById('name');
        const displayNameInput = document.getElementById('display_name');

        if (!nameInput || !displayNameInput) return;

        // Auto-generate technical name from display name
        displayNameInput.addEventListener('input', (e) => {
            if (nameInput.value === '' || nameInput.dataset.manuallyEdited !== 'true') {
                const normalizedName = this.normalizeName(e.target.value);
                nameInput.value = normalizedName;
                nameInput.dataset.autoGenerated = 'true';
            }
        });

        // Mark as manually edited when user types directly in name field
        nameInput.addEventListener('input', (e) => {
            if (nameInput.dataset.autoGenerated !== 'true') {
                nameInput.dataset.manuallyEdited = 'true';
            }
            delete nameInput.dataset.autoGenerated;

            // Validate format
            this.validateNameFormat(nameInput);
        });

        // Validate on blur
        nameInput.addEventListener('blur', () => {
            this.validateNameFormat(nameInput);
        });
    }

    normalizeName(str) {
        return str
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remove accents
            .replace(/[^a-z0-9]+/g, '_') // Replace non-alphanumeric with underscore
            .replace(/^_+|_+$/g, '') // Remove leading/trailing underscores
            .replace(/_+/g, '_'); // Replace multiple underscores with single
    }

    validateNameFormat(input) {
        const value = input.value;
        const validPattern = /^[a-z0-9_]+$/;

        if (value && !validPattern.test(value)) {
            input.classList.add('is-invalid');

            // Show or update error message
            let feedback = input.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.parentNode.insertBefore(feedback, input.nextSibling);
            }
            feedback.textContent = 'Solo se permiten letras minúsculas, números y guiones bajos';
        } else {
            input.classList.remove('is-invalid');
        }
    }

    /* ========================================
       FORM VALIDATION
    ======================================== */

    setupFormValidation() {
        const form = document.getElementById('createRoleForm') || document.getElementById('editRoleForm');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            let isValid = true;

            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Validate at least one permission is selected
            const permissionCheckboxes = form.querySelectorAll('.permission-checkbox');
            const hasSelectedPermission = Array.from(permissionCheckboxes).some(cb => cb.checked);

            if (!hasSelectedPermission) {
                e.preventDefault();

                // Show error message
                this.showValidationError('Debes seleccionar al menos un permiso para el rol');

                // Scroll to permissions section
                const permissionsCard = document.querySelector('.role-info-card-header i.bi-key');
                if (permissionsCard) {
                    permissionsCard.closest('.role-info-card').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // Highlight permissions section
                    const permissionsHeader = permissionsCard.closest('.role-info-card-header');
                    permissionsHeader.style.animation = 'pulse 0.5s ease-out 3';
                }

                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Real-time validation
        const requiredInputs = form.querySelectorAll('input[required], textarea[required]');
        requiredInputs.forEach(input => {
            input.addEventListener('blur', () => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid') && input.value.trim()) {
                    input.classList.remove('is-invalid');
                }
            });
        });
    }

    showValidationError(message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'role-toast error';
        toast.innerHTML = `
            <i class="bi bi-x-circle-fill"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
}

/* ========================================
   INITIALIZE
======================================== */

document.addEventListener('DOMContentLoaded', () => {
    window.roleFormsManager = new RoleFormsManager();
});

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    .category-selected-count,
    #totalSelectedCount {
        transition: transform 0.2s ease;
    }

    #selectedCount {
        transition: transform 0.2s ease;
    }
`;
document.head.appendChild(style);
