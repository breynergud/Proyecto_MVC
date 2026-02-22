// Competencia Edit JavaScript
class CompetenciaEdit {
    constructor() {
        this.form = document.getElementById('competenciaEditForm');
        this.competenciaId = this.getCompetenciaIdFromUrl();
        this.init();
    }

    init() {
        if (this.competenciaId) {
            this.loadCompetencia();
        } else {
            this.showError('No se especificó el ID de la competencia');
        }
        this.bindEvents();
        this.setupValidation();
    }

    getCompetenciaIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
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

    async loadCompetencia() {
        const loadingState = document.getElementById('loadingState');
        const formCard = document.getElementById('formCard');
        const errorCard = document.getElementById('errorCard');
        const infoCard = document.getElementById('infoCard');

        try {
            const response = await fetch(`../../routing.php?controller=competencia&action=show&id=${this.competenciaId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('Error al cargar la competencia');
            }

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            this.populateForm(data);

            if (loadingState) loadingState.style.display = 'none';
            if (formCard) formCard.style.display = 'block';
            if (infoCard) infoCard.style.display = 'block';

        } catch (error) {
            console.error('Error loading competencia:', error);
            if (loadingState) loadingState.style.display = 'none';
            if (errorCard) {
                errorCard.style.display = 'flex';
                const errorMessage = document.getElementById('errorMessage');
                if (errorMessage) {
                    errorMessage.textContent = error.message || 'No se pudo cargar la información de la competencia';
                }
            }
        }
    }

    populateForm(data) {
        document.getElementById('comp_id').value = data.comp_id;
        document.getElementById('comp_nombre_corto').value = data.comp_nombre_corto;
        document.getElementById('comp_horas').value = data.comp_horas;
        document.getElementById('comp_nombre_unidad_competencia').value = data.comp_nombre_unidad_competencia;
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
                Actualizando...
            `;

            const formData = new FormData(this.form);
            const competenciaData = {
                comp_id: formData.get('comp_id'),
                comp_nombre_corto: formData.get('comp_nombre_corto').trim(),
                comp_horas: parseInt(formData.get('comp_horas')),
                comp_nombre_unidad_competencia: formData.get('comp_nombre_unidad_competencia').trim()
            };

            const result = await this.updateCompetencia(competenciaData);

            if (result.success) {
                this.showSuccessModal();
            } else {
                throw new Error(result.message || 'Error al actualizar la competencia');
            }

        } catch (error) {
            console.error('Error updating competencia:', error);
            this.showGlobalError(error.message || 'Error al actualizar la competencia');
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

    async updateCompetencia(competenciaData) {
        const formData = new FormData();
        formData.append('controller', 'competencia');
        formData.append('action', 'update');
        formData.append('comp_id', competenciaData.comp_id);
        formData.append('comp_nombre_corto', competenciaData.comp_nombre_corto);
        formData.append('comp_horas', competenciaData.comp_horas);
        formData.append('comp_nombre_unidad_competencia', competenciaData.comp_nombre_unidad_competencia);

        const response = await fetch('../../routing.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });

        const data = await response.json();
        
        if (!response.ok || data.error) {
            const detailMsg = data.details ? ` (${data.details})` : '';
            return { success: false, message: (data.error || 'Error al actualizar la competencia') + detailMsg };
        }

        return { success: true, message: data.message };
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
        alert('Error: ' + message);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CompetenciaEdit();
});
