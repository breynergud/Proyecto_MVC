/**
 * NotificationService - Sistema de notificaciones tipo Toast
 */
const NotificationService = {
    container: null,

    init() {
        // Crear contenedor de toasts si no existe
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    },

    showError(message, duration = 5000) {
        this.show(message, 'error', duration);
    },

    showSuccess(message, duration = 4000) {
        this.show(message, 'success', duration);
    },

    showWarning(message, duration = 4000) {
        this.show(message, 'warning', duration);
    },

    showInfo(message, duration = 4000) {
        this.show(message, 'info', duration);
    },

    show(message, type = 'info', duration = 4000) {
        this.init();

        // Crear el toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        // Icono según el tipo
        const icons = {
            success: '<i class="fa-solid fa-circle-check"></i>',
            error: '<i class="fa-solid fa-circle-xmark"></i>',
            warning: '<i class="fa-solid fa-triangle-exclamation"></i>',
            info: '<i class="fa-solid fa-circle-info"></i>'
        };

        toast.innerHTML = `
            <div class="toast-icon">
                ${icons[type] || icons.info}
            </div>
            <div class="toast-content">
                <div class="toast-message">${this.escapeHtml(message)}</div>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        // Agregar al contenedor
        this.container.appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Auto-remover después del tiempo especificado
        if (duration > 0) {
            setTimeout(() => {
                this.hide(toast);
            }, duration);
        }

        return toast;
    },

    hide(toast) {
        toast.classList.remove('show');
        toast.classList.add('hide');

        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    NotificationService.init();
});
