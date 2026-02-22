class ProgramaView {
    constructor() {
        this.programas = [];
        this.filteredProgramas = [];
        this.currentPage = 1;
        this.recordsPerPage = 5;
        this.programaIdToDelete = null;

        // Competencias management
        this.currentProgramaId = null;
        this.currentTab = 'asociadas';
        this.competenciasAsociadas = [];
        this.competenciasDisponibles = [];
        this.allCompetenciasAsociadas = [];
        this.allCompetenciasDisponibles = [];

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadProgramas();
    }

    cacheDOM() {
        this.tableBody = document.getElementById('programasTableBody');
        this.searchInput = document.getElementById('searchInput');
        this.showingFrom = document.getElementById('showingFrom');
        this.showingTo = document.getElementById('showingTo');
        this.totalRecords = document.getElementById('totalRecords');
        this.paginationNumbers = document.getElementById('paginationNumbers');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.deleteModal = document.getElementById('deleteModal');
        this.programaToDeleteLabel = document.getElementById('programaToDelete');
        this.confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        // Competencias Modal
        this.competenciasModal = document.getElementById('competenciasModal');
        this.programaNombre = document.getElementById('programaNombre');
        this.searchCompetenciaModal = document.getElementById('searchCompetenciaModal');
        this.competenciasAsociadasList = document.getElementById('competenciasAsociadasList');
        this.competenciasDisponiblesList = document.getElementById('competenciasDisponiblesList');
        this.countAsociadas = document.getElementById('countAsociadas');
        this.countDisponibles = document.getElementById('countDisponibles');
        this.tabAsociadas = document.getElementById('tabAsociadas');
        this.tabDisponibles = document.getElementById('tabDisponibles');
        this.competenciasAsociadasTab = document.getElementById('competenciasAsociadasTab');
        this.competenciasDisponiblesTab = document.getElementById('competenciasDisponiblesTab');
    }

    bindEvents() {
        this.searchInput.addEventListener('input', () => this.handleSearch());
        this.prevBtn.addEventListener('click', () => this.changePage(this.currentPage - 1));
        this.nextBtn.addEventListener('click', () => this.changePage(this.currentPage + 1));
        this.confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());

        if (this.searchCompetenciaModal) {
            this.searchCompetenciaModal.addEventListener('input', (e) => this.handleSearchCompetencias(e.target.value));
        }
    }

    async loadProgramas() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=index');
            this.programas = await response.json();
            this.filteredProgramas = [...this.programas];
            this.render();
        } catch (error) {
            console.error('Error loading programas:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar los programas', 'error');
            }
        }
    }

    handleSearch() {
        const query = this.searchInput.value.toLowerCase();
        this.filteredProgramas = this.programas.filter(p =>
            p.prog_codigo.toString().includes(query) ||
            p.prog_denominacion.toLowerCase().includes(query) ||
            p.titpro_nombre.toLowerCase().includes(query)
        );
        this.currentPage = 1;
        this.render();
    }

    render() {
        this.renderTable();
        this.renderStats();
        this.renderPagination();
    }

    renderStats() {
        this.totalRecords.textContent = this.filteredProgramas.length;

        const start = this.filteredProgramas.length === 0 ? 0 : (this.currentPage - 1) * this.recordsPerPage + 1;
        const end = Math.min(this.currentPage * this.recordsPerPage, this.filteredProgramas.length);

        this.showingFrom.textContent = start;
        this.showingTo.textContent = end;
    }

    renderTable() {
        const start = (this.currentPage - 1) * this.recordsPerPage;
        const end = start + this.recordsPerPage;
        const paginatedProgramas = this.filteredProgramas.slice(start, end);

        if (paginatedProgramas.length === 0) {
            this.tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-gray-500">
                <div class="flex flex-col items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-4xl text-gray-300"></i>
                    <p>No se encontraron programas</p>
                </div>
            </td></tr>`;
            return;
        }

        this.tableBody.innerHTML = '';
        paginatedProgramas.forEach(p => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 transition-colors';

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-sena-green">
                    ${String(p.prog_codigo).padStart(3, '0')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-sena-green/10 text-sena-green border border-sena-green/20">
                        ${p.prog_codigo}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-slate-900 leading-tight">
                        ${this.escapeHtml(p.prog_denominacion)}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <i class="fa-solid fa-certificate text-sena-green"></i>
                        <span class="truncate max-w-[200px]" title="${this.escapeHtml(p.titpro_nombre)}">${this.escapeHtml(p.titpro_nombre)}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                        ${p.prog_tipo || 'N/A'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="action-buttons">

                        <a href="ver.php?id=${p.prog_codigo}" class="btn-icon btn-view" title="Ver detalles">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="editar.php?id=${p.prog_codigo}" class="btn-icon btn-edit" title="Editar">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <button onclick="programaView.openDeleteModal(${p.prog_codigo}, '${this.escapeHtml(p.prog_denominacion)}')" class="btn-icon btn-delete" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            this.tableBody.appendChild(row);
        });
    }

    renderCompetenciasBadges(programa) {
        const total = programa.total_competencias || 0;

        if (total === 0) {
            return `
                <div style="display: flex; align-items: center; gap: 8px; color: #9ca3af; font-size: 13px;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 18px;"></i>
                    <span>Sin competencias</span>
                </div>
            `;
        }

        const competencias = programa.competencias || [];

        let html = '<div style="display: flex; flex-direction: column; gap: 6px;">';

        // Mostrar las primeras 3 competencias como mini-cards
        competencias.slice(0, 3).forEach(comp => {
            html += `
                <div style="display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); border-left: 3px solid #3b82f6; border-radius: 6px; font-size: 12px;">
                    <i class="fa-solid fa-circle-check" style="color: #3b82f6; font-size: 16px; flex-shrink: 0;"></i>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; color: #1e40af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${this.escapeHtml(comp.comp_nombre_corto)}">
                            ${this.escapeHtml(comp.comp_nombre_corto)}
                        </div>
                        <div style="color: #64748b; font-size: 11px;">
                            ${comp.comp_horas} horas
                        </div>
                    </div>
                </div>
            `;
        });

        // Si hay más, mostrar contador
        if (total > 3) {
            const remaining = total - 3;
            html += `
                <div style="display: flex; align-items: center; justify-content: center; padding: 6px; background: #f1f5f9; border-radius: 6px; color: #64748b; font-size: 12px; font-weight: 500;">
                    <i class="fa-solid fa-ellipsis" style="margin-right: 4px;"></i>
                    +${remaining} más
                </div>
            `;
        }

        html += '</div>';
        return html;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    renderPagination() {
        const totalPages = Math.ceil(this.filteredProgramas.length / this.recordsPerPage);
        this.paginationNumbers.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = `pagination-btn ${i === this.currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => this.changePage(i);
            this.paginationNumbers.appendChild(btn);
        }

        this.prevBtn.disabled = this.currentPage === 1;
        this.nextBtn.disabled = this.currentPage === totalPages || totalPages === 0;
    }

    changePage(page) {
        if (page < 1 || page > Math.ceil(this.filteredProgramas.length / this.recordsPerPage)) return;
        this.currentPage = page;
        this.render();
    }

    openDeleteModal(id, nombre) {
        this.programaIdToDelete = id;
        this.programaToDeleteLabel.textContent = nombre;
        this.deleteModal.classList.add('active');
    }

    closeDeleteModal() {
        this.deleteModal.classList.remove('active');
        this.programaIdToDelete = null;
    }

    async confirmDelete() {
        if (!this.programaIdToDelete) return;

        try {
            const formData = new FormData();
            formData.append('controller', 'programa');
            formData.append('action', 'destroy');
            formData.append('id', this.programaIdToDelete);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.show(result.message, 'success');
                }
                this.closeDeleteModal();
                await this.loadProgramas();
            } else {
                throw new Error(result.error || 'Error desconocido');
            }
        } catch (error) {
            console.error('Error deleting programa:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }

    // ========== COMPETENCIAS MODAL ==========

    async openCompetenciasModal(programaId, programaNombre) {
        this.currentProgramaId = programaId;
        this.programaNombre.textContent = programaNombre;
        this.competenciasModal.classList.add('active');
        this.currentTab = 'asociadas';
        this.switchTab('asociadas');

        await this.loadCompetenciasData();
    }

    closeCompetenciasModal() {
        this.competenciasModal.classList.remove('active');
        this.currentProgramaId = null;
        this.searchCompetenciaModal.value = '';
    }

    switchTab(tab) {
        this.currentTab = tab;

        if (tab === 'asociadas') {
            this.tabAsociadas.classList.add('active');
            this.tabDisponibles.classList.remove('active');
            this.competenciasAsociadasTab.classList.add('active');
            this.competenciasDisponiblesTab.classList.remove('active');
            this.renderCompetenciasAsociadas();
        } else {
            this.tabDisponibles.classList.add('active');
            this.tabAsociadas.classList.remove('active');
            this.competenciasDisponiblesTab.classList.add('active');
            this.competenciasAsociadasTab.classList.remove('active');
            this.renderCompetenciasDisponibles();
        }
    }

    async loadCompetenciasData() {
        try {
            // Load asociadas
            const respAsociadas = await fetch(`../../routing.php?controller=programa&action=getCompetencias&programa_id=${this.currentProgramaId}&t=` + new Date().getTime());
            this.allCompetenciasAsociadas = await respAsociadas.json();
            this.competenciasAsociadas = [...this.allCompetenciasAsociadas];

            // Load disponibles
            const respDisponibles = await fetch(`../../routing.php?controller=programa&action=getCompetenciasDisponibles&programa_id=${this.currentProgramaId}&t=` + new Date().getTime());
            this.allCompetenciasDisponibles = await respDisponibles.json();
            this.competenciasDisponibles = [...this.allCompetenciasDisponibles];

            this.countAsociadas.textContent = this.allCompetenciasAsociadas.length;
            this.countDisponibles.textContent = this.allCompetenciasDisponibles.length;

            if (this.currentTab === 'asociadas') {
                this.renderCompetenciasAsociadas();
            } else {
                this.renderCompetenciasDisponibles();
            }
        } catch (error) {
            console.error('Error loading competencias:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar competencias', 'error');
            }
        }
    }

    handleSearchCompetencias(searchTerm) {
        const term = searchTerm.toLowerCase().trim();

        if (this.currentTab === 'asociadas') {
            if (!term) {
                this.competenciasAsociadas = [...this.allCompetenciasAsociadas];
            } else {
                this.competenciasAsociadas = this.allCompetenciasAsociadas.filter(comp =>
                    comp.comp_nombre_corto.toLowerCase().includes(term) ||
                    comp.comp_nombre_unidad_competencia.toLowerCase().includes(term)
                );
            }
            this.renderCompetenciasAsociadas();
        } else {
            if (!term) {
                this.competenciasDisponibles = [...this.allCompetenciasDisponibles];
            } else {
                this.competenciasDisponibles = this.allCompetenciasDisponibles.filter(comp =>
                    comp.comp_nombre_corto.toLowerCase().includes(term) ||
                    comp.comp_nombre_unidad_competencia.toLowerCase().includes(term)
                );
            }
            this.renderCompetenciasDisponibles();
        }
    }

    renderCompetenciasAsociadas() {
        if (this.competenciasAsociadas.length === 0) {
            this.competenciasAsociadasList.innerHTML = '<p class="text-center text-gray-500" style="padding: 40px;">No hay competencias asociadas</p>';
            return;
        }

        this.competenciasAsociadasList.innerHTML = this.competenciasAsociadas.map(comp => `
            <div class="competencia-card">
                <div class="competencia-info">
                    <div class="competencia-nombre">${this.escapeHtml(comp.comp_nombre_corto)}</div>
                    <div class="competencia-detalles">
                        <span class="competencia-horas">${comp.comp_horas} horas</span>
                        ${this.escapeHtml(comp.comp_nombre_unidad_competencia)}
                    </div>
                </div>
                <button class="btn-icon-action btn-remove-comp" onclick="programaView.desasociarCompetencia(${comp.comp_id})" title="Quitar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `).join('');
    }

    renderCompetenciasDisponibles() {
        if (this.competenciasDisponibles.length === 0) {
            this.competenciasDisponiblesList.innerHTML = '<p class="text-center text-gray-500" style="padding: 40px;">No hay competencias disponibles</p>';
            return;
        }

        this.competenciasDisponiblesList.innerHTML = this.competenciasDisponibles.map(comp => `
            <div class="competencia-card">
                <div class="competencia-info">
                    <div class="competencia-nombre">${this.escapeHtml(comp.comp_nombre_corto)}</div>
                    <div class="competencia-detalles">
                        <span class="competencia-horas">${comp.comp_horas} horas</span>
                        ${this.escapeHtml(comp.comp_nombre_unidad_competencia)}
                    </div>
                </div>
                <button class="btn-icon-action btn-add-comp" onclick="programaView.asociarCompetencia(${comp.comp_id})" title="Agregar">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        `).join('');
    }

    async asociarCompetencia(competenciaId) {
        try {
            const formData = new FormData();
            formData.append('controller', 'programa');
            formData.append('action', 'asociarCompetencia');
            formData.append('programa_id', this.currentProgramaId);
            formData.append('competencia_id', competenciaId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            if (window.NotificationService) {
                NotificationService.show('Competencia asociada correctamente', 'success');
            }

            await this.loadCompetenciasData();
            await this.loadProgramas(); // Refresh main table
        } catch (error) {
            console.error('Error asociando competencia:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }

    async desasociarCompetencia(competenciaId) {
        try {
            const formData = new FormData();
            formData.append('controller', 'programa');
            formData.append('action', 'desasociarCompetencia');
            formData.append('programa_id', this.currentProgramaId);
            formData.append('competencia_id', competenciaId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            if (window.NotificationService) {
                NotificationService.show('Competencia desasociada correctamente', 'success');
            }

            await this.loadCompetenciasData();
            await this.loadProgramas(); // Refresh main table
        } catch (error) {
            console.error('Error desasociando competencia:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.programaView = new ProgramaView();
});

// Helper functions for global scope (onclick attributes)
window.closeDeleteModal = () => window.programaView.closeDeleteModal();
window.confirmDelete = () => window.programaView.confirmDelete();
window.closeCompetenciasModal = () => window.programaView.closeCompetenciasModal();

