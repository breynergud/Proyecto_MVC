class CrearPrograma {
    constructor() {
        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        this.titulosData = []; // Store titles data
        await Promise.all([
            this.loadTitulos(),
            this.loadCompetencias()
        ]);
    }

    cacheDOM() {
        this.form = document.getElementById('crearProgramaForm');
        this.tipoSelect = document.getElementById('prog_tipo');
        this.tituloSelect = document.getElementById('tit_programa_titpro_id'); // Hidden select
        this.competenciasContainer = document.getElementById('competenciasContainer');
        this.loadingCompetencias = document.getElementById('loadingCompetencias');
        this.competenciasError = document.getElementById('competenciasError');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        this.tipoSelect.addEventListener('change', () => this.syncTitleWithType());
    }

    syncTitleWithType() {
        const selectedType = this.tipoSelect.value;
        if (!selectedType || !this.titulosData.length) return;

        const normalize = (str) => str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim().toLowerCase();

        const normType = normalize(selectedType);
        const matchingTitulo = this.titulosData.find(t => normalize(t.titpro_nombre) === normType);

        if (matchingTitulo) {
            this.tituloSelect.value = matchingTitulo.titpro_id;
            console.log(`Sincronizado: ${selectedType} -> ID ${matchingTitulo.titpro_id}`);
        } else {
            console.warn(`No se encontró un título que coincida exactamente con: ${selectedType}`);
            // Fallback: búsqueda parcial
            const partialMatch = this.titulosData.find(t =>
                normalize(t.titpro_nombre).includes(normType) ||
                normType.includes(normalize(t.titpro_nombre))
            );
            if (partialMatch) {
                this.tituloSelect.value = partialMatch.titpro_id;
                console.log(`Sincronizado (parcial): ${selectedType} -> ID ${partialMatch.titpro_id}`);
            }
        }
    }

    async loadTitulos() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=getTitulos&t=' + new Date().getTime(), {
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            this.titulosData = JSON.parse(text);

            if (!response.ok) {
                throw new Error(this.titulosData.error || 'Error al cargar títulos');
            }

            // Initial sync if type is already selected
            this.syncTitleWithType();

        } catch (error) {
            console.error('Error loading titulos:', error);
            this.showError('Error al cargar los títulos: ' + error.message);
        }
    }

    async loadCompetencias() {
        try {
            const response = await fetch('../../routing.php?controller=competencia&action=index&t=' + new Date().getTime(), {
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            let competencias;

            try {
                competencias = JSON.parse(text);
            } catch (e) {
                console.error('Error al parsear competencias:', e);
                throw new Error('Respuesta inválida al cargar competencias');
            }

            if (!response.ok) {
                throw new Error(competencias.error || 'Error al cargar competencias');
            }

            this.renderCompetencias(competencias);
        } catch (error) {
            console.error('Error loading competencias:', error);
            this.loadingCompetencias.innerHTML = `<span class="text-red-500">Error: ${error.message}</span>`;
        }
    }

    renderCompetencias(competencias) {
        this.loadingCompetencias.classList.add('hidden');

        if (!competencias || competencias.length === 0) {
            this.competenciasContainer.innerHTML = '<p class="text-gray-500 col-span-2">No hay competencias registradas.</p>';
            this.competenciasContainer.classList.remove('hidden');
            return;
        }

        this.competenciasContainer.innerHTML = '';
        competencias.forEach(comp => {
            const div = document.createElement('div');
            div.className = 'flex items-start p-3 border rounded hover:bg-gray-50 transition-colors';

            div.innerHTML = `
                <div class="flex items-center h-5">
                    <input id="comp_${comp.comp_id}" name="competencias[]" value="${comp.comp_id}" type="checkbox" 
                           class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded cursor-pointer">
                </div>
                <div class="ml-3 text-sm">
                    <label for="comp_${comp.comp_id}" class="font-medium text-gray-700 cursor-pointer select-none">
                        ${comp.comp_nombre_corto}
                    </label>
                    <p class="text-gray-500 text-xs mt-1">${comp.comp_horas} horas</p>
                </div>
            `;
            this.competenciasContainer.appendChild(div);
        });

        this.competenciasContainer.classList.remove('hidden');
    }

    async handleSubmit(e) {
        e.preventDefault();

        // Asegurar que el ID del título esté asignado
        if (!this.tituloSelect.value) {
            this.syncTitleWithType();
            if (!this.tituloSelect.value) {
                this.showError('No se pudo determinar el título académico automáticamente para el tipo: ' + this.tipoSelect.value);
                return;
            }
        }

        const formData = new FormData(this.form);
        formData.append('controller', 'programa');
        formData.append('action', 'store');

        // Collect selected competencies manually if needed (though FormData handles name="competencias[]" automatically usually)
        // Check if we need to process them specially
        const selectedCompetencias = [];
        this.competenciasContainer.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
            selectedCompetencias.push(cb.value);
        });

        // Remove individual entries to avoid duplicates if checkboxes naturally add them
        formData.delete('competencias[]');

        // Add as a JSON array string or individual items depending on backend preference
        // Let's send as array for consistency with our plan
        selectedCompetencias.forEach(id => formData.append('competencias_ids[]', id));

        console.log('Enviando datos del programa...');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            console.log('Respuesta del servidor:', text);

            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                console.error('Error al parsear JSON:', e);
                throw new Error('El servidor envió una respuesta inválida. Revisa la consola (F12)');
            }

            if (!response.ok || result.error) {
                throw new Error(result.error || result.details || 'Error al crear el programa');
            }

            if (result.message) {
                this.showSuccess(result.message);
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            }
        } catch (error) {
            console.error('Error creating programa:', error);
            this.showError(error.message);
        }
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

document.addEventListener('DOMContentLoaded', () => {
    new CrearPrograma();
});
