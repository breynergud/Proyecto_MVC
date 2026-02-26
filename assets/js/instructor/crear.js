// Instructor Form JavaScript
class InstructorForm {
    constructor() {
        this.form = document.getElementById('instructorForm');
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupValidation();
        this.loadProgramas();
    }

    async loadProgramas() {
        try {
            const response = await fetch('../../routing.php?controller=instructor&action=getProgramas');
            const programas = await response.json();

            const select = document.getElementById('intruProgSelect');
            if (select && !programas.error) {
                programas.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p.prog_codigo;
                    option.textContent = p.prog_denominacion;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error cargando programas:', error);
        }
    }

    async loadCompetenciasForPrograma() {
        const programaId = document.getElementById('intruProgSelect').value;
        const container = document.getElementById('compContainer');
        const listContainer = document.getElementById('compList');

        if (!programaId) {
            container.style.display = 'none';
            return;
        }

        try {
            listContainer.innerHTML = '<div class="text-gray-500 text-center py-4 text-sm col-span-2">Cargando competencias...</div>';
            container.style.display = 'block';

            // Fetch to dedicated instructor action to get competencies for a program
            const response = await fetch(`../../routing.php?controller=instructor&action=getCompetenciasInstructorPrograma&programa_id=${programaId}`);

            const competencias = await response.json();

            listContainer.innerHTML = '';

            if (!competencias || competencias.length === 0 || competencias.error) {
                listContainer.innerHTML = '<div class="text-orange-500 text-sm text-center py-2 col-span-2">Este programa no tiene competencias asociadas.</div>';
                return;
            }

            // Create checkboxes
            competencias.forEach(comp => {
                const div = document.createElement('div');
                div.className = 'flex items-center p-2 hover:bg-white rounded border border-transparent hover:border-gray-200 transition-colors cursor-pointer';
                div.innerHTML = `
                    <input type="checkbox" id="comp_${comp.id}" name="competencias[]" value="${comp.id}" class="w-4 h-4 text-sena-green bg-white border-gray-300 rounded focus:ring-sena-green cursor-pointer">
                    <label for="comp_${comp.id}" class="ml-2 text-sm font-medium text-gray-700 w-full cursor-pointer leading-tight">${comp.nombre}</label>
                `;
                listContainer.appendChild(div);
            });

        } catch (error) {
            console.error('Error loading competencies:', error);
            listContainer.innerHTML = `<span class="text-red-500 text-sm col-span-2">Error cargando competencias.</span>`;
        }
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

            // Append program and competencies (Especialidades)
            const programaId = document.getElementById('intruProgSelect').value;
            if (programaId) {
                formData.append('programa_id', programaId);
                const checkboxes = document.querySelectorAll('input[name="competencias[]"]:checked');
                const competenciasIds = Array.from(checkboxes).map(cb => cb.value);
                formData.append('competencias', JSON.stringify(competenciasIds));
            }

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
    window.instructorView = new InstructorForm();
});
