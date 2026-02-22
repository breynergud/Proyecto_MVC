class EditarPrograma {
    constructor() {
        const urlParams = new URLSearchParams(window.location.search);
        this.programaId = urlParams.get('id');

        if (!this.programaId) {
            window.location.href = 'index.php';
            return;
        }

        this.todasCompetencias = [];
        this.competenciasAsociadas = [];
        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadTitulos();
        await this.loadProgramaData();
        await this.loadCompetencias();
    }

    cacheDOM() {
        this.form = document.getElementById('editarProgramaForm');
        this.tituloSelect = document.getElementById('tit_programa_titpro_id');
        this.codigoInput = document.getElementById('prog_codigo');
        this.denominacionInput = document.getElementById('prog_denominacion');
        this.tipoSelect = document.getElementById('prog_tipo');

        // Competencias
        this.searchCompetencia = document.getElementById('searchCompetencia');
        this.competenciasDisponibles = document.getElementById('competenciasDisponibles');
        this.competenciasDisponiblesList = document.getElementById('competenciasDisponiblesList');
        this.competenciasAsociadas = document.getElementById('competenciasAsociadas');
        this.competenciasCount = document.getElementById('competenciasCount');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        if (this.searchCompetencia) {
            this.searchCompetencia.addEventListener('input', (e) => this.handleSearchCompetencia(e.target.value));
            this.searchCompetencia.addEventListener('focus', () => this.loadCompetenciasDisponibles());
        }
    }

    async loadTitulos() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=getTitulos&t=' + new Date().getTime(), {
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            let titulos = JSON.parse(text);

            this.tituloSelect.innerHTML = '<option value="" disabled>Seleccione un título...</option>';
            titulos.forEach(t => {
                const option = document.createElement('option');
                option.value = t.titpro_id;
                option.textContent = t.titpro_nombre;
                this.tituloSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading titulos:', error);
            this.showError('Error al cargar los títulos');
        }
    }

    async loadProgramaData() {
        try {
            const response = await fetch(`../../routing.php?controller=programa&action=show&id=${this.programaId}&t=` + new Date().getTime(), {
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            let data = JSON.parse(text);

            if (data.error) throw new Error(data.error);

            this.codigoInput.value = data.prog_codigo;
            this.denominacionInput.value = data.prog_denominacion;
            this.tituloSelect.value = data.tit_programa_titpro_id;
            this.tipoSelect.value = data.prog_tipo || '';

        } catch (error) {
            console.error('Error loading programa data:', error);
            this.showError('Error al cargar datos del programa');
        }
    }

    async loadCompetencias() {
        try {
            const response = await fetch(`../../routing.php?controller=programa&action=getCompetencias&programa_id=${this.programaId}&t=` + new Date().getTime(), {
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            this.competenciasAsociadas = JSON.parse(text);
            this.renderCompetenciasAsociadas();
        } catch (error) {
            console.error('Error loading competencias:', error);
            this.competenciasAsociadas.innerHTML = '<p class="text-center text-red-500">Error al cargar competencias</p>';
        }
    }

    async loadCompetenciasDisponibles() {
        try {
            const response = await fetch(`../../routing.php?controller=programa&action=getCompetenciasDisponibles&programa_id=${this.programaId}&t=` + new Date().getTime(), {
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            this.todasCompetencias = JSON.parse(text);

            if (this.todasCompetencias.length > 0) {
                this.competenciasDisponibles.style.display = 'block';
                this.renderCompetenciasDisponibles(this.todasCompetencias);
            }
        } catch (error) {
            console.error('Error loading competencias disponibles:', error);
        }
    }

    handleSearchCompetencia(searchTerm) {
        const term = searchTerm.toLowerCase().trim();

        if (!term) {
            this.renderCompetenciasDisponibles(this.todasCompetencias);
            return;
        }

        const filtered = this.todasCompetencias.filter(comp =>
            comp.comp_nombre_corto.toLowerCase().includes(term) ||
            comp.comp_nombre_unidad_competencia.toLowerCase().includes(term)
        );

        this.renderCompetenciasDisponibles(filtered);
    }

    renderCompetenciasAsociadas() {
        this.competenciasCount.textContent = this.competenciasAsociadas.length;

        if (this.competenciasAsociadas.length === 0) {
            this.competenciasAsociadas.innerHTML = '<p class="text-center text-gray-500">No hay competencias asociadas</p>';
            return;
        }

        this.competenciasAsociadas.innerHTML = this.competenciasAsociadas.map(comp => `
            <div class="competencia-item-small">
                <div class="competencia-item-info">
                    <div class="competencia-item-nombre">${this.escapeHtml(comp.comp_nombre_corto)}</div>
                    <div style="font-size: 12px; color: #6b7280;">
                        <span class="competencia-item-horas">${comp.comp_horas} horas</span>
                        ${this.escapeHtml(comp.comp_nombre_unidad_competencia.substring(0, 60))}...
                    </div>
                </div>
                <div class="competencia-item-actions">
                    <button type="button" class="btn-icon-action btn-remove-comp" onclick="editarPrograma.desasociarCompetencia(${comp.comp_id})" title="Quitar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    renderCompetenciasDisponibles(competencias) {
        if (competencias.length === 0) {
            this.competenciasDisponiblesList.innerHTML = '<p class="text-center text-gray-500">No hay competencias disponibles</p>';
            return;
        }

        this.competenciasDisponiblesList.innerHTML = competencias.map(comp => `
            <div class="competencia-item-small">
                <div class="competencia-item-info">
                    <div class="competencia-item-nombre">${this.escapeHtml(comp.comp_nombre_corto)}</div>
                    <div style="font-size: 12px; color: #6b7280;">
                        <span class="competencia-item-horas">${comp.comp_horas} horas</span>
                        ${this.escapeHtml(comp.comp_nombre_unidad_competencia.substring(0, 60))}...
                    </div>
                </div>
                <div class="competencia-item-actions">
                    <button type="button" class="btn-icon-action btn-add-comp" onclick="editarPrograma.asociarCompetencia(${comp.comp_id})" title="Agregar">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    async asociarCompetencia(competenciaId) {
        try {
            const formData = new FormData();
            formData.append('controller', 'programa');
            formData.append('action', 'asociarCompetencia');
            formData.append('programa_id', this.programaId);
            formData.append('competencia_id', competenciaId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            this.showSuccess('Competencia asociada correctamente');
            await this.loadCompetencias();
            await this.loadCompetenciasDisponibles();
            this.searchCompetencia.value = '';
        } catch (error) {
            console.error('Error asociando competencia:', error);
            this.showError(error.message);
        }
    }

    async desasociarCompetencia(competenciaId) {
        try {
            const formData = new FormData();
            formData.append('controller', 'programa');
            formData.append('action', 'desasociarCompetencia');
            formData.append('programa_id', this.programaId);
            formData.append('competencia_id', competenciaId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            this.showSuccess('Competencia desasociada correctamente');
            await this.loadCompetencias();
            await this.loadCompetenciasDisponibles();
        } catch (error) {
            console.error('Error desasociando competencia:', error);
            this.showError(error.message);
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        formData.append('controller', 'programa');
        formData.append('action', 'update');

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            let result = JSON.parse(text);

            if (result.error) {
                throw new Error(result.error);
            }

            this.showSuccess(result.message || 'Programa actualizado correctamente');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1500);
        } catch (error) {
            console.error('Error updating programa:', error);
            this.showError(error.message);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showSuccess(message) {
        if (typeof NotificationService !== 'undefined') {
            NotificationService.showSuccess(message);
        } else {
            alert(message);
        }
    }

    showError(message) {
        if (typeof NotificationService !== 'undefined') {
            NotificationService.showError(message);
        } else {
            alert('Error: ' + message);
        }
    }
}

let editarPrograma;
document.addEventListener('DOMContentLoaded', () => {
    editarPrograma = new EditarPrograma();
});
