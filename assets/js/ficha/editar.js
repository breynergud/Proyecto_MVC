class EditarFicha {
    constructor() {
        const urlParams = new URLSearchParams(window.location.search);
        this.fichaId = urlParams.get('id');

        if (!this.fichaId) {
            window.location.href = 'index.php';
            return;
        }

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadProgramas();
        await this.loadInstructores();
        await this.loadCoordinaciones();
        await this.loadFichaData();
    }

    cacheDOM() {
        this.form = document.getElementById('editarFichaForm');
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

            this.programaSelect.innerHTML = '<option value="" disabled>Seleccione un programa...</option>';
            programas.forEach(p => {
                const option = document.createElement('option');
                option.value = p.prog_codigo;
                option.textContent = `${p.prog_codigo} - ${p.prog_denominacion}`;
                this.programaSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading programas:', error);
        }
    }

    async loadInstructores() {
        try {
            const response = await fetch('../../routing.php?controller=ficha&action=getInstructores');
            const instructores = await response.json();

            this.instructorSelect.innerHTML = '<option value="" disabled>Seleccione un instructor...</option>';
            instructores.forEach(i => {
                const option = document.createElement('option');
                option.value = i.inst_id;
                option.textContent = `${i.inst_nombres} ${i.inst_apellidos}`;
                this.instructorSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading instructores:', error);
        }
    }

    async loadCoordinaciones() {
        try {
            const response = await fetch('../../routing.php?controller=ficha&action=getCoordinaciones');
            const coordinaciones = await response.json();

            this.coordinacionSelect.innerHTML = '<option value="" disabled>Seleccione coordinación...</option>';
            coordinaciones.forEach(c => {
                const option = document.createElement('option');
                option.value = c.coord_id;
                option.textContent = `${c.coord_nombre} - ${c.cent_nombre}`;
                this.coordinacionSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading coordinaciones:', error);
        }
    }

    async loadFichaData() {
        try {
            const response = await fetch(`../../routing.php?controller=ficha&action=show&id=${this.fichaId}`);
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            this.programaSelect.value = data.programa_prog_id;
            this.instructorSelect.value = data.instructor_inst_id;
            this.jornadaSelect.value = data.fich_jornada;
            this.coordinacionSelect.value = data.coordinacion_coord_id;
        } catch (error) {
            console.error('Error loading ficha data:', error);
            this.showError('Error al cargar datos de la ficha');
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        formData.append('controller', 'ficha');
        formData.append('action', 'update');

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            this.showSuccess(result.message || 'Ficha actualizada correctamente');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1500);
        } catch (error) {
            console.error('Error updating ficha:', error);
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
    new EditarFicha();
});
