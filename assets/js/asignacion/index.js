class AsignacionCalendario {
    constructor() {
        this.fichaActual = null;
        this.currentDate = new Date();
        this.asignaciones = [];
        this.competenciasPendientes = [];
        this.instructoresDisponibles = [];
        this.selectedDate = null;
        this.selectedInstructor = null;
        this.ambientes = [];

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadAmbientes();
    }

    cacheDOM() {
        // Búsqueda
        this.fichaSearch = document.getElementById('searchFicha');
        this.btnBuscarFicha = document.getElementById('btnBuscarFicha');
        this.fichaInfo = document.getElementById('fichaInfo');
        this.fichaNumero = document.getElementById('fichaNumero');
        this.fichaProgramaNombre = document.getElementById('fichaProgramaNombre');
        this.fichaJornada = document.getElementById('fichaJornada');
        this.fichaInstructor = document.getElementById('fichaInstructor');

        // Calendario
        this.calendarioContainer = document.getElementById('calendarioContainer');
        this.mesAnio = document.getElementById('mesAnio');
        this.calendarioDias = document.getElementById('calendarioDias');
        this.btnPrevMonth = document.getElementById('btnPrevMonth');
        this.btnNextMonth = document.getElementById('btnNextMonth');

        // Modal
        this.asignacionModal = document.getElementById('asignacionModal');
        this.selectedDateEl = document.getElementById('selectedDate');
        this.competenciaSelect = document.getElementById('competenciaSelect');
        this.ambienteSelect = document.getElementById('ambienteSelect');
        this.horaInicio = document.getElementById('horaInicio');
        this.horaFin = document.getElementById('horaFin');
        this.instructoresSection = document.getElementById('instructoresSection');
        this.instructoresList = document.getElementById('instructoresList');
        this.btnGuardarAsignacion = document.getElementById('btnGuardarAsignacion');
    }

    bindEvents() {
        this.btnBuscarFicha.addEventListener('click', () => this.buscarFicha());
        this.fichaSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.buscarFicha();
        });

        this.btnPrevMonth.addEventListener('click', () => this.cambiarMes(-1));
        this.btnNextMonth.addEventListener('click', () => this.cambiarMes(1));

        this.competenciaSelect.addEventListener('change', () => this.onCompetenciaChange());
        this.btnGuardarAsignacion.addEventListener('click', () => this.guardarAsignacion());
    }

    async loadAmbientes() {
        try {
            const response = await fetch('../../routing.php?controller=ambiente&action=index');
            this.ambientes = await response.json();
            this.renderAmbientes();
        } catch (error) {
            console.error('Error loading ambientes:', error);
        }
    }

    renderAmbientes() {
        this.ambienteSelect.innerHTML = '<option value="">Seleccione un ambiente...</option>';
        this.ambientes.forEach(amb => {
            const option = document.createElement('option');
            option.value = amb.amb_id;
            option.textContent = `${amb.amb_nombre} - ${amb.sede_nombre}`;
            this.ambienteSelect.appendChild(option);
        });
    }

    async buscarFicha() {
        const fichaId = this.fichaSearch.value.trim();

        if (!fichaId) {
            this.showNotification('Por favor ingrese un número de ficha', 'warning');
            return;
        }

        try {
            const response = await fetch(`../../routing.php?controller=asignacion&action=getFichaInfo&ficha_id=${fichaId}`);
            const data = await response.json();

            if (data.error) {
                this.showNotification(data.error, 'error');
                return;
            }

            this.fichaActual = data;
            this.mostrarInfoFicha();
            await this.cargarAsignaciones();
            this.renderCalendario();
            this.calendarioContainer.style.display = 'block';
        } catch (error) {
            console.error('Error buscando ficha:', error);
            this.showNotification('Error al buscar la ficha', 'error');
        }
    }

    mostrarInfoFicha() {
        this.fichaNumero.textContent = `Ficha #${this.fichaActual.fich_id}`;
        this.fichaProgramaNombre.textContent = this.fichaActual.programa_nombre;
        this.fichaJornada.textContent = this.fichaActual.fich_jornada || 'N/A';
        this.fichaInstructor.textContent = `${this.fichaActual.instructor_nombre}`;
        this.fichaInfo.style.display = 'block';
    }

    async cargarAsignaciones() {
        try {
            const response = await fetch(`../../routing.php?controller=asignacion&action=getAsignacionesByFicha&ficha_id=${this.fichaActual.fich_id}`);
            this.asignaciones = await response.json();
        } catch (error) {
            console.error('Error cargando asignaciones:', error);
            this.asignaciones = [];
        }
    }

    cambiarMes(direccion) {
        this.currentDate.setMonth(this.currentDate.getMonth() + direccion);
        this.renderCalendario();
    }

    renderCalendario() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();

        // Actualizar título
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        this.mesAnio.textContent = `${meses[month]} ${year}`;

        // Obtener primer y último día del mes
        const primerDia = new Date(year, month, 1);
        const ultimoDia = new Date(year, month + 1, 0);

        // Días a mostrar antes del primer día del mes
        const diasAntes = primerDia.getDay();

        // Limpiar calendario
        this.calendarioDias.innerHTML = '';

        // Días del mes anterior
        const ultimoDiaMesAnterior = new Date(year, month, 0).getDate();
        for (let i = diasAntes - 1; i >= 0; i--) {
            const dia = ultimoDiaMesAnterior - i;
            this.renderDia(dia, true, new Date(year, month - 1, dia));
        }

        // Días del mes actual
        for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
            this.renderDia(dia, false, new Date(year, month, dia));
        }

        // Días del mes siguiente
        const diasDespues = 42 - (diasAntes + ultimoDia.getDate());
        for (let dia = 1; dia <= diasDespues; dia++) {
            this.renderDia(dia, true, new Date(year, month + 1, dia));
        }
    }

    renderDia(numero, otroMes, fecha) {
        const diaCell = document.createElement('div');
        diaCell.className = `dia-cell ${otroMes ? 'otro-mes' : ''}`;

        const diaNumero = document.createElement('div');
        diaNumero.className = 'dia-numero';
        diaNumero.textContent = numero;
        diaCell.appendChild(diaNumero);

        // Restricción de fecha: No permitir fechas anteriores a la fecha de inicio de la ficha
        let isDateDisabled = false;
        if (this.fichaActual && this.fichaActual.fich_fecha_ini_lectiva) {
            const fechaIniFicha = new Date(this.fichaActual.fich_fecha_ini_lectiva);
            // Normalizar fechas para comparar solo año-mes-día
            const f1 = new Date(fecha.getFullYear(), fecha.getMonth(), fecha.getDate());
            const f2 = new Date(fechaIniFicha.getFullYear(), fechaIniFicha.getMonth(), fechaIniFicha.getDate());

            if (f1 < f2) {
                isDateDisabled = true;
                diaCell.classList.add('disabled');
            }

            if (this.fichaActual.fich_fecha_fin_lectiva) {
                const fechaFinFicha = new Date(this.fichaActual.fich_fecha_fin_lectiva);
                const f3 = new Date(fechaFinFicha.getFullYear(), fechaFinFicha.getMonth(), fechaFinFicha.getDate());
                if (f1 > f3) {
                    isDateDisabled = true;
                    diaCell.classList.add('disabled');
                }
            }
        }

        // Buscar asignaciones para este día
        if (!otroMes && this.fichaActual) {
            const asignacionesDelDia = this.asignaciones.filter(asig => {
                const asigFecha = new Date(asig.asig_fecha_ini);
                return asigFecha.toDateString() === fecha.toDateString();
            });

            asignacionesDelDia.forEach(asig => {
                const asigItem = document.createElement('div');
                asigItem.className = 'asignacion-item';

                // Formatear hora (HH:mm)
                const hora = new Date(asig.asig_fecha_ini).toLocaleTimeString('es-ES', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                asigItem.textContent = `${hora} - ${asig.competencia_nombre}`;
                asigItem.title = `${asig.competencia_nombre} \nInstructor: ${asig.instructor_nombre}\nAmbiente: ${asig.ambiente_nombre}\nHorario: ${hora}`;

                // Botón de eliminar
                const btnEliminar = document.createElement('button');
                btnEliminar.className = 'btn-delete-asig';
                btnEliminar.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
                btnEliminar.onclick = (e) => {
                    e.stopPropagation();
                    this.confirmarEliminarAsignacion(asig.asig_id);
                };
                asigItem.appendChild(btnEliminar);

                diaCell.appendChild(asigItem);
            });
        }

        // Click para crear asignación (solo si no está deshabilitado)
        if (!otroMes && !isDateDisabled) {
            diaCell.addEventListener('click', () => this.abrirModalAsignacion(fecha));
        }

        this.calendarioDias.appendChild(diaCell);
    }

    async abrirModalAsignacion(fecha) {
        if (!this.fichaActual) return;

        this.selectedDate = fecha;
        const fechaStr = fecha.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        this.selectedDateEl.textContent = fechaStr.charAt(0).toUpperCase() + fechaStr.slice(1);

        // Cargar competencias pendientes
        await this.cargarCompetenciasPendientes();

        this.asignacionModal.classList.add('active');
    }

    closeAsignacionModal() {
        this.asignacionModal.classList.remove('active');
        this.competenciaSelect.value = '';
        this.ambienteSelect.value = '';
        this.instructoresSection.style.display = 'none';
        this.instructoresList.innerHTML = '';
        this.selectedInstructor = null;
        this.btnGuardarAsignacion.disabled = true;
    }

    async cargarCompetenciasPendientes() {
        try {
            const response = await fetch(`../../routing.php?controller=asignacion&action=getCompetenciasPendientes&ficha_id=${this.fichaActual.fich_id}`);
            this.competenciasPendientes = await response.json();

            this.competenciaSelect.innerHTML = '<option value="">Seleccione una competencia...</option>';

            if (this.competenciasPendientes.length === 0) {
                const option = document.createElement('option');
                option.textContent = 'No hay competencias pendientes';
                option.disabled = true;
                this.competenciaSelect.appendChild(option);
            } else {
                this.competenciasPendientes.forEach(comp => {
                    const option = document.createElement('option');
                    option.value = comp.comp_id;
                    option.textContent = `${comp.comp_nombre_corto} (${comp.comp_horas}h)`;
                    this.competenciaSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error cargando competencias pendientes:', error);
        }
    }

    async onCompetenciaChange() {
        const competenciaId = this.competenciaSelect.value;

        if (!competenciaId) {
            this.instructoresSection.style.display = 'none';
            this.btnGuardarAsignacion.disabled = true;
            return;
        }

        // Cargar instructores especializados
        await this.cargarInstructoresEspecializados(competenciaId);
    }

    async cargarInstructoresEspecializados(competenciaId) {
        try {
            const programaId = this.fichaActual.prog_codigo; // Usamos prog_codigo que es la PK en programa
            const response = await fetch(`../../routing.php?controller=asignacion&action=getInstructoresByCompetencia&competencia_id=${competenciaId}&programa_id=${programaId}`);
            this.instructoresDisponibles = await response.json();

            this.renderInstructores();
            this.instructoresSection.style.display = 'block';
        } catch (error) {
            console.error('Error cargando instructores:', error);
            this.instructoresDisponibles = [];
        }
    }

    renderInstructores() {
        this.instructoresList.innerHTML = '';

        if (this.instructoresDisponibles.length === 0) {
            this.instructoresList.innerHTML = '<p class="text-center text-gray-500" style="padding: 20px;">No hay instructores especializados en esta competencia</p>';
            return;
        }

        this.instructoresDisponibles.forEach(inst => {
            const card = document.createElement('div');
            card.className = 'instructor-card';
            card.onclick = () => this.seleccionarInstructor(inst.inst_id, card);

            card.innerHTML = `
                <div class="instructor-info">
                    <div class="instructor-nombre">${inst.inst_nombres} ${inst.inst_apellidos}</div>
                    <div class="instructor-detalles">
                        ${inst.inst_correo || 'Sin correo'} • ${inst.centro_nombre || 'Sin centro'}
                    </div>
                </div>
                <input type="radio" name="instructor" value="${inst.inst_id}" class="instructor-radio">
            `;

            this.instructoresList.appendChild(card);
        });
    }

    seleccionarInstructor(instructorId, cardElement) {
        // Remover selección anterior
        document.querySelectorAll('.instructor-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Seleccionar nuevo
        cardElement.classList.add('selected');
        cardElement.querySelector('input[type="radio"]').checked = true;
        this.selectedInstructor = instructorId;

        // Habilitar botón guardar
        this.btnGuardarAsignacion.disabled = false;
    }

    async guardarAsignacion() {
        if (!this.selectedInstructor || !this.competenciaSelect.value || !this.ambienteSelect.value) {
            this.showNotification('Complete todos los campos requeridos', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('controller', 'asignacion');
        formData.append('action', 'store');
        formData.append('ficha_id', this.fichaActual.fich_id);
        formData.append('instructor_id', this.selectedInstructor);
        formData.append('competencia_id', this.competenciaSelect.value);
        formData.append('ambiente_id', this.ambienteSelect.value);

        // Combinar fecha con horas
        const fechaInicio = new Date(this.selectedDate);
        const [horaIni, minIni] = this.horaInicio.value.split(':');
        fechaInicio.setHours(horaIni, minIni, 0);

        const fechaFin = new Date(this.selectedDate);
        const [horaFin, minFin] = this.horaFin.value.split(':');
        fechaFin.setHours(horaFin, minFin, 0);

        formData.append('fecha_inicio', fechaInicio.toISOString());
        formData.append('fecha_fin', fechaFin.toISOString());

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            this.showNotification('Asignación creada correctamente', 'success');
            this.closeAsignacionModal();
            await this.cargarAsignaciones();
            this.renderCalendario();
        } catch (error) {
            console.error('Error guardando asignación:', error);
            this.showNotification(error.message, 'error');
        }
    }

    async confirmarEliminarAsignacion(asigId) {
        if (confirm('¿Está seguro de que desea eliminar esta asignación?')) {
            await this.eliminarAsignacion(asigId);
        }
    }

    async eliminarAsignacion(asigId) {
        try {
            const formData = new FormData();
            formData.append('controller', 'asignacion');
            formData.append('action', 'destroy');
            formData.append('id', asigId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            this.showNotification('Asignación eliminada correctamente', 'success');
            await this.cargarAsignaciones();
            this.renderCalendario();
        } catch (error) {
            console.error('Error eliminando asignación:', error);
            this.showNotification(error.message, 'error');
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

// Initialize
let asignacionCalendario;
document.addEventListener('DOMContentLoaded', () => {
    asignacionCalendario = new AsignacionCalendario();
});

// Global functions
window.closeAsignacionModal = () => asignacionCalendario.closeAsignacionModal();
