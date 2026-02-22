// Instructor Form JavaScript
class InstructorForm {
    constructor() {
        this.form = document.getElementById('instructorForm');
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupValidation();
    }



    bindEvents() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit();
            });
        }

        // Real-time validation
        ['inst_nombre', 'inst_apellidos', 'inst_correo', 'inst_telefono'].forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.addEventListener('blur', () => this.validateField(field));
                input.addEventListener('input', () => this.clearError(field));
            }
        });

        // Modal events
        window.closeSuccessModal = () => {
            this.closeSuccessModal();
        };
    }

    setupValidation() {
        this.validationRules = {
            inst_nombre: { required: true, minLength: 3, pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/ },
            inst_apellidos: { required: true, minLength: 3, pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/ },
            inst_correo: { required: true, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
            inst_telefono: { required: true, pattern: /^[0-9+]+$/ }
        };
    }

    async handleSubmit() {
        if (!this.validateForm()) {
            return;
        }

        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="loading-spinner" style="width: 16px; height: 16px; margin-right: 8px;"></div>
                Guardando...
            `;

            const formData = new FormData(this.form);

            // Append controller and action
            formData.append('controller', 'instructor');
            formData.append('action', 'store');

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('Respuesta inválida del servidor');
            }

            if (!response.ok || data.error) {
                throw new Error(data.error || 'Error al crear el instructor');
            }

            this.showSuccessModal(`${formData.get('inst_nombre')} ${formData.get('inst_apellidos')}`);
            this.form.reset();

        } catch (error) {
            console.error('Error creating instructor:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError(error.message);
            } else {
                alert(error.message);
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    validateForm() {
        let isValid = true;
        Object.keys(this.validationRules).forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        return isValid;
    }

    validateField(fieldName) {
        const input = document.getElementById(fieldName);
        if (!input) return true;

        const value = input.value.trim();
        const rules = this.validationRules[fieldName];

        if (rules.required && !value) {
            this.showError(fieldName, 'Este campo es obligatorio');
            return false;
        }

        if (value && rules.minLength && value.length < rules.minLength) {
            this.showError(fieldName, `Mínimo ${rules.minLength} caracteres`);
            return false;
        }

        if (value && rules.pattern && !rules.pattern.test(value)) {
            this.showError(fieldName, 'Formato inválido');
            return false;
        }

        this.clearError(fieldName);
        return true;
    }

    showError(fieldName, message) {
        const errorElement = document.getElementById(`${fieldName}_error`);
        const inputElement = document.getElementById(fieldName);

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }

        if (inputElement) {
            inputElement.style.borderColor = 'var(--red-500)';
        }
    }

    clearError(fieldName) {
        const errorElement = document.getElementById(`${fieldName}_error`);
        const inputElement = document.getElementById(fieldName);

        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.remove('show');
        }

        if (inputElement) {
            inputElement.style.borderColor = '';
        }
    }

    showSuccessModal(nombre) {
        const modal = document.getElementById('successModal');
        const nameElement = document.getElementById('createdInstructorName');

        if (nameElement) {
            nameElement.textContent = nombre;
        }

        if (modal) {
            modal.classList.add('show');
        }
    }

    closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new InstructorForm();
});
