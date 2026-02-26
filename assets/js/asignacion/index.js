class AsignacionCalendario {
    constructor() {
        this.viewDate = new Date();
        this.fichaActual = null;
        this.events = [];
        this.createModal = null;
        this.viewModal = null;

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.initModals();
        this.bindEvents();
        this.renderCalendar();
        await this.preloadModalData();
    }

    cacheDOM() {
        this.fichaSearch = document.getElementById('searchFicha');
        this.btnBuscarFicha = document.getElementById('btnBuscarFicha');
        this.fichaInfo = document.getElementById('fichaInfo');
        this.fichaNumero = document.getElementById('fichaNumero');
        this.fichaProgramaNombre = document.getElementById('fichaProgramaNombre');
        this.calendarioContainer = document.getElementById('calendarioContainer');

        // Elementos Calendario
        this.monthDisplay = document.getElementById('monthDisplay');
        this.calendarGrid = document.getElementById('calendarGrid');
        this.btnPrevMonth = document.getElementById('btnPrevMonth');
        this.btnNextMonth = document.getElementById('btnNextMonth');
        this.btnToday = document.getElementById('btnToday');

        // Modal crear
        this.createForm = document.getElementById('createAssignmentForm');
        this.btnGuardar = document.getElementById('btnGuardarNuevaAsignacion');
        this.modalFichaId = document.getElementById('modalFichaId');
        this.modalFechaInicio = document.getElementById('modalFechaInicio');
        this.modalHoraInicio = document.getElementById('modalHoraInicio');
        this.modalFechaFin = document.getElementById('modalFechaFin');
        this.modalHoraFin = document.getElementById('modalHoraFin');
        this.selectInstructor = document.getElementById('modalInstructor');
        this.selectAmbiente = document.getElementById('modalAmbiente');
        this.selectCompetencia = document.getElementById('modalCompetencia');

        // Modal ver
        this.viewDetailCompetencia = document.getElementById('viewDetailCompetencia');
        this.viewDetailInstructor = document.getElementById('viewDetailInstructor');
        this.viewDetailInicio = document.getElementById('viewDetailInicio');
        this.viewDetailFin = document.getElementById('viewDetailFin');
        this.btnEliminar = document.getElementById('btnEliminarAsignacion');
    }

    initModals() {
        this.createModal = new bootstrap.Modal(document.getElementById('createAssignmentModal'));
        this.viewModal = new bootstrap.Modal(document.getElementById('viewAssignmentModal'));
    }

    bindEvents() {
        this.btnBuscarFicha.addEventListener('click', () => this.buscarFicha());
        this.fichaSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.buscarFicha();
        });
        this.btnGuardar.addEventListener('click', () => this.guardarAsignacion());

        // Navegación Calendario
        this.btnPrevMonth.addEventListener('click', () => {
            this.viewDate.setMonth(this.viewDate.getMonth() - 1);
            this.renderCalendar();
        });
        this.btnNextMonth.addEventListener('click', () => {
            this.viewDate.setMonth(this.viewDate.getMonth() + 1);
            this.renderCalendar();
        });
        this.btnToday.addEventListener('click', () => {
            this.viewDate = new Date();
            this.renderCalendar();
        });

        // Listener para cambio de competencia
        this.selectCompetencia.addEventListener('change', () => this.onCompetenciaChange());
    }

    async renderCalendar() {
        if (!this.calendarGrid) return;

        this.calendarGrid.innerHTML = '';
        const year = this.viewDate.getFullYear();
        const month = this.viewDate.getMonth();

        // Mostrar mes y año
        const monthNames = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
        this.monthDisplay.textContent = `${monthNames[month]} ${year}`;

        // Primer día del mes y total de días
        const firstDay = new Date(year, month, 1).getDay(); // 0 (Dom) a 6 (Sab)
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Ajustar primer día para que empiece en lunes (0=Lun, 6=Dom)
        let emptyStartingCells = firstDay === 0 ? 6 : firstDay - 1;

        // Días del mes anterior (para completar la primera fila si se desea, o solo espacios vacíos)
        for (let i = 0; i < emptyStartingCells; i++) {
            const div = document.createElement('div');
            div.className = 'calendar-day other-month';
            this.calendarGrid.appendChild(div);
        }

        // Días del mes actual
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = new Date().toDateString() === new Date(year, month, day).toDateString();

            const dayCell = document.createElement('div');
            dayCell.className = `calendar-day ${isToday ? 'today' : ''}`;
            dayCell.innerHTML = `<span class="day-number">${day}</span><div class="event-list" id="events-${dateStr}"></div>`;

            dayCell.addEventListener('click', (e) => {
                if (e.target.closest('.event-item')) return; // No abrir creación si se hace clic en evento
                this.onDateClick(dateStr);
            });

            this.calendarGrid.appendChild(dayCell);
        }

        // Renderizar eventos si hay una ficha seleccionada
        if (this.fichaActual) {
            await this.fetchAndRenderEvents();
        }
    }

    async fetchAndRenderEvents() {
        try {
            const response = await fetch(`../../routing.php?controller=asignacion&action=getEventos&ficha_id=${this.fichaActual.fich_id}`);
            this.events = await response.json();

            this.events.forEach(event => {
                // Parse dates in local timezone to avoid weird UTC offset shifts missing the last day
                const startStr = event.start.split(' ')[0]; // Take only YYYY-MM-DD
                const endStr = (event.end || event.start).split(' ')[0];

                const [sY, sM, sD] = startStr.split('-').map(Number);
                const [eY, eM, eD] = endStr.split('-').map(Number);

                // Use local timezone manually
                let current = new Date(sY, sM - 1, sD);
                const endDay = new Date(eY, eM - 1, eD);

                while (current <= endDay) {
                    const y = current.getFullYear();
                    const m = String(current.getMonth() + 1).padStart(2, '0');
                    const d = String(current.getDate()).padStart(2, '0');
                    const dateStr = `${y}-${m}-${d}`;

                    const listContainer = document.getElementById(`events-${dateStr}`);

                    if (listContainer) {
                        const eventEl = document.createElement('div');
                        eventEl.className = 'event-item';
                        eventEl.textContent = event.title;
                        eventEl.title = `${event.title} - ${event.instructor}`;
                        eventEl.addEventListener('click', (e) => {
                            e.stopPropagation();
                            this.onEventClick(event);
                        });
                        listContainer.appendChild(eventEl);
                    }

                    // Avanzar al siguiente día
                    current.setDate(current.getDate() + 1);
                }
            });
        } catch (error) {
            console.error('Error rendering events:', error);
        }
    }

    async preloadModalData() {
        try {
            const results = await Promise.allSettled([
                this.fetchData('../../routing.php?controller=asignacion&action=getAmbientesList'),
                this.fetchData('../../routing.php?controller=asignacion&action=getInstructoresList')
            ]);

            if (results[0].status === 'fulfilled') this.fillSelect(this.selectAmbiente, results[0].value);
            if (results[1].status === 'fulfilled') this.fillSelect(this.selectInstructor, results[1].value);
        } catch (error) {
            console.error('Error preloading data:', error);
        }
    }

    async fetchData(url) {
        const response = await fetch(url);
        const text = await response.text();

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Response is not valid JSON:', text);
            throw new Error('Respuesta del servidor no válida (JSON esperado)');
        }

        // Si el JSON trae un error explícito desde PHP, lo mostramos (ej: "Ficha no encontrada")
        if (data && data.error) {
            throw new Error(data.error);
        }

        // Si falla por otra cosa (ej: 500 fatal sin JSON)
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return data;
    }

    async onCompetenciaChange() {
        const competenciaId = this.selectCompetencia.value;
        if (!competenciaId) {
            // No limpiamos los instructores si no hay competencia, 
            // dejamos los que están (todos) o volvemos a cargar todos
            this.preloadModalData();
            return;
        }

        try {
            const programaId = this.fichaActual.prog_codigo;
            const data = await this.fetchData(`../../routing.php?controller=asignacion&action=getInstructoresByCompetencia&competencia_id=${competenciaId}&programa_id=${programaId}`);
            this.fillSelect(this.selectInstructor, data);
        } catch (error) {
            console.error('Error filtering instructors:', error);
            this.showNotification('No se pudieron filtrar los instructores', 'warning');
        }
    }

    fillSelect(select, data) {
        if (!select) return;
        select.innerHTML = '<option value="">Seleccione una opción...</option>';

        if (!Array.isArray(data)) {
            console.error('Data for select is not an array:', data);
            return;
        }

        data.forEach(item => {
            const option = document.createElement('option');
            // Mapeo flexible de propiedades según lo que devuelva el modelo/controlador
            const id = item.id || item.amb_id || item.inst_id || item.comp_id || item.prog_codigo;
            const nombre = item.nombre || item.amb_nombre || item.comp_nombre_corto || item.prog_denominacion || (item.inst_nombres ? `${item.inst_nombres} ${item.inst_apellidos}` : '');

            option.value = id;
            option.textContent = nombre || 'Sin nombre';
            select.appendChild(option);
        });
    }

    async buscarFicha() {
        const fichaId = this.fichaSearch.value.trim();
        if (!fichaId) {
            this.showNotification('Ingrese un número de ficha', 'warning');
            return;
        }

        try {
            const data = await this.fetchData(`../../routing.php?controller=asignacion&action=getFichaInfo&ficha_id=${fichaId}`);

            this.fichaActual = data;
            this.mostrarInfoFicha();
            this.calendarioContainer.style.display = 'block';

            // Cargar TODAS las competencias (Técnicas y Transversales) 
            try {
                const competencias = await this.fetchData(`../../routing.php?controller=asignacion&action=getCompetenciasList`);
                this.fillSelect(this.selectCompetencia, competencias);
            } catch (compErr) {
                console.error('Error loading competencies:', compErr);
                this.showNotification('No se pudieron cargar las competencias del programa', 'warning');
            }

            if (data.fich_fecha_ini_lectiva) {
                this.viewDate = new Date(data.fich_fecha_ini_lectiva);
            }
            this.renderCalendar();
        } catch (error) {
            console.error('Error buscando ficha:', error);
            this.showNotification(error.message || 'Error al buscar la ficha', 'error');
        }
    }

    mostrarInfoFicha() {
        this.fichaNumero.textContent = `Ficha #${this.fichaActual.fich_id}`;
        this.fichaProgramaNombre.textContent = this.fichaActual.programa_nombre;
        this.fichaInfo.style.display = 'block';
    }

    async onDateClick(dateStr) {
        if (!this.fichaActual) {
            this.showNotification('Primero busque una ficha', 'info');
            return;
        }

        // Refrescar datos antes de mostrar el modal
        try {
            await this.preloadModalData();
            const competencias = await this.fetchData(`../../routing.php?controller=asignacion&action=getCompetenciasByPrograma&programa_id=${this.fichaActual.prog_codigo}`);
            this.fillSelect(this.selectCompetencia, competencias);
        } catch (error) {
            console.error('Error refreshing modal data:', error);
        }

        this.createForm.reset();
        this.modalFichaId.value = this.fichaActual.fich_id;

        this.modalFechaInicio.value = dateStr;
        this.modalHoraInicio.value = "07:00";
        this.modalFechaFin.value = dateStr;
        this.modalHoraFin.value = "08:00";

        this.createModal.show();
    }

    onEventClick(event) {
        this.viewDetailCompetencia.textContent = event.title;
        this.viewDetailInstructor.textContent = event.instructor;
        this.viewDetailInicio.textContent = event.start;
        this.viewDetailFin.textContent = event.end || 'N/A';

        this.btnEliminar.onclick = () => this.confirmarEliminar(event.id);

        this.viewModal.show();
    }

    async guardarAsignacion() {
        const formData = new FormData(this.createForm);
        const fechaIni = `${formData.get('fecha_inicio')} ${formData.get('hora_inicio')}:00`;
        const fechaFin = `${formData.get('fecha_fin')} ${formData.get('hora_fin')}:00`;

        formData.set('fecha_inicio', fechaIni);
        formData.set('fecha_fin', fechaFin);
        formData.append('controller', 'asignacion');
        formData.append('action', 'store');

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                this.showNotification(result.message, 'success');
                this.createModal.hide();
                this.renderCalendar();
            } else {
                this.showNotification(result.message || 'Error al guardar', 'error');
            }
        } catch (error) {
            console.error('Error guardando:', error);
            this.showNotification('Error de conexión', 'error');
        }
    }

    confirmarEliminar(id) {
        if (confirm('¿Está seguro de eliminar esta asignación?')) {
            this.eliminarAsignacion(id);
        }
    }

    async eliminarAsignacion(id) {
        try {
            const formData = new FormData();
            formData.append('controller', 'asignacion');
            formData.append('action', 'destroy');
            formData.append('id', id);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!result.error) {
                this.showNotification('Asignación eliminada', 'success');
                this.viewModal.hide();
                this.renderCalendar();
            } else {
                this.showNotification(result.error, 'error');
            }
        } catch (error) {
            console.error('Error eliminando:', error);
        }
    }

    showNotification(message, type) {
        if (typeof NotificationService !== 'undefined') {
            NotificationService.show(message, type);
        } else {
            alert(message);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AsignacionCalendario();
});
