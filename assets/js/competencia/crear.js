// Competencia Form JavaScript
class CompetenciaForm {
    constructor() {
        this.form = document.getElementById('competenciaForm');
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
        const nombreInput = document.getElementById('comp_nombre_corto');
        const horasInput = document.getElementById('comp_horas');
        const normaInput = document.getElementById('comp_nombre_unidad_competencia');

        if (nombreInput) {
            nombreInput.addEventListener('blur', () => this.validateNombreCorto());
            nombreInput.addEventListener('input', () => this.clearError('comp_nombre_corto'));
        }

        if (horasInput) {
            horasInput.addEventListener('blur', () => this.validateHoras());
            horasInput.addEventListener('input', () => this.clearError('comp_horas'));
        }

        if (normaInput) {
            normaInput.addEventListener('blur', () => this.validateNorma());
            normaInput.addEventListener('input', () => this.clearError('comp_nombre_unidad_competencia'));
        }

        // Modal events
        window.closeSuccessModal = () => {
            this.closeSuccessModal();
        };
    }

    setupValidation() {
        this.validationRules = {
            comp_nombre_corto: {
                required: true,
                minLength: 3,
                maxLength: 200
            },
            comp_horas: {
                required: true,
                min: 1,
                max: 9999
            },
            comp_nombre_unidad_competencia: {
                required: true,
                minLength: 10,
                maxLength: 500
            }
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
            const competenciaData = {
                comp_nombre_corto: formData.get('comp_nombre_corto').trim(),
                comp_horas: parseInt(formData.get('comp_horas')),
                comp_nombre_unidad_competencia: formData.get('comp_nombre_unidad_competencia').trim()
            };

            const result = await this.createCompetencia(competenciaData);

            if (result.success) {
                this.showSuccessModal();
                this.form.reset();
            } else {
                throw new Error(result.message || 'Error al crear la competencia');
            }

        } catch (error) {
            console.error('Error creating competencia:', error);
            this.showGlobalError(error.message || 'Error al crear la competencia');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    validateForm() {
        let isValid = true;

        if (!this.validateNombreCorto()) isValid = false;
        if (!this.validateHoras()) isValid = false;
        if (!this.validateNorma()) isValid = false;

        return isValid;
    }

    validateNombreCorto() {
        const input = document.getElementById('comp_nombre_corto');
        const value = input.value.trim();
        const rules = this.validationRules.comp_nombre_corto;

        if (rules.required && !value) {
            this.showError('comp_nombre_corto', 'El nombre corto es obligatorio');
            return false;
        }

        if (value && value.length < rules.minLength) {
            this.showError('comp_nombre_corto', `Debe tener al menos ${rules.minLength} caracteres`);
            return false;
        }

        if (value && value.length > rules.maxLength) {
            this.showError('comp_nombre_corto', `No puede exceder ${rules.maxLength} caracteres`);
            return false;
        }

        this.clearError('comp_nombre_corto');
        return true;
    }

    validateHoras() {
        const input = document.getElementById('comp_horas');
        const value = input.value.trim();
        const rules = this.validationRules.comp_horas;

        if (rules.required && !value) {
            this.showError('comp_horas', 'Las horas son obligatorias');
            return false;
        }

        const numValue = parseInt(value);
        if (isNaN(numValue) || numValue < rules.min) {
            this.showError('comp_horas', `Debe ser un número mayor o igual a ${rules.min}`);
            return false;
        }

        if (numValue > rules.max) {
            this.showError('comp_horas', `No puede exceder ${rules.max} horas`);
            return false;
        }

        this.clearError('comp_horas');
        return true;
    }

    validateNorma() {
        const input = document.getElementById('comp_nombre_unidad_competencia');
        const value = input.value.trim();
        const rules = this.validationRules.comp_nombre_unidad_competencia;

        if (rules.required && !value) {
            this.showError('comp_nombre_unidad_competencia', 'La norma/unidad de competencia es obligatoria');
            return false;
        }

        if (value && value.length < rules.minLength) {
            this.showError('comp_nombre_unidad_competencia', `Debe tener al menos ${rules.minLength} caracteres`);
            return false;
        }

        if (value && value.length > rules.maxLength) {
            this.showError('comp_nombre_unidad_competencia', `No puede exceder ${rules.maxLength} caracteres`);
            return false;
        }

        this.clearError('comp_nombre_unidad_competencia');
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
            inputElement.focus();
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

    async createCompetencia(competenciaData) {
        const formData = new FormData();
        formData.append('controller', 'competencia');
        formData.append('action', 'store');
        formData.append('comp_nombre_corto', competenciaData.comp_nombre_corto);
        formData.append('comp_horas', competenciaData.comp_horas);
        formData.append('comp_nombre_unidad_competencia', competenciaData.comp_nombre_unidad_competencia);

        console.log('=== ENVIANDO COMPETENCIA ===');
        console.log('Datos:', competenciaData);
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            console.log('=== RESPUESTA DEL SERVIDOR ===');
            console.log('Status:', response.status);
            console.log('Respuesta cruda:', text);

            let data;
            try {
                data = JSON.parse(text);
                console.log('Respuesta parseada:', data);
            } catch (e) {
                console.error('Error al parsear JSON:', e);
                return { 
                    success: false, 
                    message: 'El servidor envió una respuesta inválida. Revisa la consola (F12) para más detalles.' 
                };
            }
            
            if (!response.ok || data.error) {
                const detailMsg = data.details ? ` (${data.details})` : '';
                return { success: false, message: (data.error || 'Error al crear la competencia') + detailMsg };
            }

            return { success: true, message: data.message };
        } catch (error) {
            console.error('Error en fetch:', error);
            return { success: false, message: 'Error de conexión: ' + error.message };
        }
    }

    showSuccessModal() {
        const modal = document.getElementById('successModal');
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

    showGlobalError(message) {
        if (typeof NotificationService !== 'undefined') {
            NotificationService.showError(message);
        } else {
            alert('Error: ' + message);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CompetenciaForm();
});
