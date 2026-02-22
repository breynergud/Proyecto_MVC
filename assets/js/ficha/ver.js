class VerFicha {
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
        await this.loadFichaData();
    }

    cacheDOM() {
        this.fichaNumero = document.getElementById('fichaNumero');
        this.programaNombre = document.getElementById('programaNombre');
        this.programaCodigo = document.getElementById('programaCodigo');
        this.instructorNombre = document.getElementById('instructorNombre');
        this.jornada = document.getElementById('jornada');
        this.coordinacion = document.getElementById('coordinacion');
    }

    async loadFichaData() {
        try {
            const response = await fetch(`../../routing.php?controller=ficha&action=show&id=${this.fichaId}`);
            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            // Cargar datos relacionados
            await this.loadRelatedData(data);

            this.renderData(data);
        } catch (error) {
            console.error('Error loading ficha data:', error);
            this.showError('Error al cargar datos de la ficha');
        }
    }

    async loadRelatedData(ficha) {
        try {
            // Cargar programa
            const programaResp = await fetch(`../../routing.php?controller=programa&action=show&prog_codigo=${ficha.programa_prog_id}`);
            const programa = await programaResp.json();
            ficha.programa = programa;

            // Cargar instructor
            const instructorResp = await fetch(`../../routing.php?controller=instructor&action=show&id=${ficha.instructor_inst_id}`);
            const instructor = await instructorResp.json();
            ficha.instructor = instructor;

            // Cargar coordinación
            const coordResp = await fetch(`../../routing.php?controller=ficha&action=getCoordinaciones`);
            const coordinaciones = await coordResp.json();
            ficha.coordinacion = coordinaciones.find(c => c.coord_id == ficha.coordinacion_coord_id);
        } catch (error) {
            console.error('Error loading related data:', error);
        }
    }

    renderData(data) {
        this.fichaNumero.textContent = `Ficha #${String(data.fich_id).padStart(6, '0')}`;

        if (data.programa) {
            this.programaNombre.innerHTML = `
                <i class="fa-solid fa-graduation-cap"></i>
                <span>${this.escapeHtml(data.programa.prog_denominacion)}</span>
            `;
            this.programaCodigo.innerHTML = `
                <i class="fa-solid fa-barcode"></i>
                <span>${data.programa.prog_codigo}</span>
            `;
        }

        if (data.instructor) {
            this.instructorNombre.innerHTML = `
                <i class="fa-solid fa-user"></i>
                <span>${this.escapeHtml(data.instructor.inst_nombres)} ${this.escapeHtml(data.instructor.inst_apellidos)}</span>
            `;
        }

        this.jornada.innerHTML = `
            <i class="fa-solid fa-clock"></i>
            <span>${data.fich_jornada || 'N/A'}</span>
        `;

        if (data.coordinacion) {
            this.coordinacion.innerHTML = `
                <i class="fa-solid fa-building"></i>
                <span>${this.escapeHtml(data.coordinacion.coord_nombre)} - ${this.escapeHtml(data.coordinacion.cent_nombre)}</span>
            `;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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
    new VerFicha();
});
