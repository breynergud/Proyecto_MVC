class CrearFicha {
    constructor() {
        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadProgramas();
        await this.loadInstructores();
        await this.loadCoordinaciones();
    }

    cacheDOM() {
        this.form = document.getElementById('crearFichaForm');
        this.programaSelect = document.getElementById('programa_prog_id');
        this.instructorSelect = document.getElementById('instructor_inst_id');
        this.jornadaSelect = document.getElementById('fich_jornada');
        this.coordinacionSelect = document.getElementById('coordinacion_coord_id');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async loadProgramas() {
        try {
            const response = await fetch('../../routing.php?controller=ficha&action=getProgramas');
            const programas = await response.json();

            this.programaSelect.innerHTML = '<option value="" disabled selected>Seleccione un programa...</option>';
            programas.forEach(p => {
                const option = document.createElement('option');
                option.value = p.prog_codigo;
                option.textContent = `${p.prog_codigo} - ${p.prog_denominacion}`;
                this.programaSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading programas:', error);
            this.showError('Error al cargar los programas');
        }
    }

    async loadInstructores() {
        try {
            const response = await fetch('../../routing.php?controller=ficha&action=getInstructores', {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const text = await response.text();
            console.log('Instructores response:', text);

            const instructores = JSON.parse(text);

            this.instructorSelect.innerHTML = '<option value="" disabled selected>Seleccione un instructor...</option>';
            instructores.forEach(i => {
                const option = document.createElement('option');
                option.value = i.inst_id;
                option.textContent = `${i.inst_nombres} ${i.inst_apellidos}`;
                this.instructorSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading instructores:', error);
            this.showError('Error al cargar los instructores: ' + error.message);
        }
    }

    async loadCoordinaciones() {
        try {
            const response = await fetch('../../routing.php?controller=ficha&action=getCoordinaciones', {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const text = await response.text();
            console.log('Coordinaciones response:', text);

            const coordinaciones = JSON.parse(text);

            this.coordinacionSelect.innerHTML = '<option value="" disabled selected>Seleccione coordinación...</option>';
            coordinaciones.forEach(c => {
                const option = document.createElement('option');
                option.value = c.coord_id;
                option.textContent = `${c.coord_nombre} - ${c.cent_nombre}`;
                this.coordinacionSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading coordinaciones:', error);
            this.showError('Error al cargar las coordinaciones: ' + error.message);
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        formData.append('controller', 'ficha');
        formData.append('action', 'store');

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            this.showSuccess(result.message || 'Ficha creada correctamente');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1500);
        } catch (error) {
            console.error('Error creating ficha:', error);
            this.showError(error.message);
        }
    }

    showSuccess(message) {
        if (typeof NotificationService !== 'undefined') {
            NotificationService.show(message, 'success');
        } else {
            alert(message);
        }
    }

    showError(message) {
        if (typeof NotificationService !== 'undefined') {
            NotificationService.show(message, 'error');
        } else {
            alert('Error: ' + message);
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new CrearFicha();
});
