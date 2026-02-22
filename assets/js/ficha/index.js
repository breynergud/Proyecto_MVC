class FichaView {
    constructor() {
        this.fichas = [];
        this.filteredFichas = [];
        this.currentPage = 1;
        this.recordsPerPage = 5;
        this.fichaIdToDelete = null;

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadFichas();
    }

    cacheDOM() {
        this.tableBody = document.getElementById('fichasTableBody');
        this.searchInput = document.getElementById('searchInput');
        this.showingFrom = document.getElementById('showingFrom');
        this.showingTo = document.getElementById('showingTo');
        this.totalRecords = document.getElementById('totalRecords');
        this.paginationNumbers = document.getElementById('paginationNumbers');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.deleteModal = document.getElementById('deleteModal');
        this.fichaToDeleteLabel = document.getElementById('fichaToDelete');
        this.confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    }

    bindEvents() {
        this.searchInput.addEventListener('input', () => this.handleSearch());
        this.prevBtn.addEventListener('click', () => this.changePage(this.currentPage - 1));
        this.nextBtn.addEventListener('click', () => this.changePage(this.currentPage + 1));
        this.confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
    }

    async loadFichas() {
        try {
            const response = await fetch('../../routing.php?controller=ficha&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.fichas = await response.json();
            this.filteredFichas = [...this.fichas];
            this.render();
        } catch (error) {
            console.error('Error loading fichas:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar las fichas', 'error');
            }
        }
    }

    handleSearch() {
        const query = this.searchInput.value.toLowerCase();
        this.filteredFichas = this.fichas.filter(f =>
            f.fich_id.toString().includes(query) ||
            f.prog_denominacion.toLowerCase().includes(query) ||
            f.instructor_nombre.toLowerCase().includes(query)
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
        this.totalRecords.textContent = this.filteredFichas.length;

        const start = this.filteredFichas.length === 0 ? 0 : (this.currentPage - 1) * this.recordsPerPage + 1;
        const end = Math.min(this.currentPage * this.recordsPerPage, this.filteredFichas.length);

        this.showingFrom.textContent = start;
        this.showingTo.textContent = end;
    }

    renderTable() {
        const start = (this.currentPage - 1) * this.recordsPerPage;
        const end = start + this.recordsPerPage;
        const paginatedFichas = this.filteredFichas.slice(start, end);

        if (paginatedFichas.length === 0) {
            this.tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-gray-500">
                <div class="text-center py-12">
                    <i class="fa-solid fa-magnifying-glass text-4xl text-gray-300"></i>
                    <p class="text-gray-400 mt-2">No se encontraron fichas que coincidan con la búsqueda.</p>
                </div>
            </td></tr>`;
            return;
        }

        this.tableBody.innerHTML = '';
        paginatedFichas.forEach(f => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 transition-colors';

            const jornadaBadgeColor = this.getJornadaColor(f.fich_jornada);

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-sena-green">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-lg"></i>
                        ${String(f.fich_id).padStart(6, '0')}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-slate-900 leading-tight">
                        ${this.escapeHtml(f.prog_denominacion)}
                    </div>
                    <div class="text-xs text-slate-500 mt-1">
                        Código: ${f.prog_codigo}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user text-sena-green"></i>
                        <span class="text-sm text-slate-700">${this.escapeHtml(f.instructor_nombre)}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ${jornadaBadgeColor}">
                        ${f.fich_jornada || 'N/A'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-slate-700">${this.escapeHtml(f.coord_nombre)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="ver.php?id=${f.fich_id}" class="text-sena-green hover:text-emerald-700 mx-2" title="Ver detalles">
                       <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="editar.php?id=${f.fich_id}" class="text-indigo-600 hover:text-indigo-900 mx-2" title="Editar">
                       <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <button onclick="fichaView.openDeleteModal(${f.fich_id}, '${f.fich_id}')" 
                                class="btn-icon btn-icon-danger" title="Eliminar">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            this.tableBody.appendChild(row);
        });
    }

    getJornadaColor(jornada) {
        const colors = {
            'Diurna': 'bg-yellow-100 text-yellow-800',
            'Nocturna': 'bg-blue-100 text-blue-800',
            'Mixta': 'bg-purple-100 text-purple-800',
            'Fin de Semana': 'bg-green-100 text-green-800'
        };
        return colors[jornada] || 'bg-slate-100 text-slate-700';
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    renderPagination() {
        const totalPages = Math.ceil(this.filteredFichas.length / this.recordsPerPage);
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
        if (page < 1 || page > Math.ceil(this.filteredFichas.length / this.recordsPerPage)) return;
        this.currentPage = page;
        this.render();
    }

    openDeleteModal(id, numero) {
        this.fichaIdToDelete = id;
        this.fichaToDeleteLabel.textContent = `#${String(numero).padStart(6, '0')}`;
        this.deleteModal.classList.add('active');
    }

    closeDeleteModal() {
        this.deleteModal.classList.remove('active');
        this.fichaIdToDelete = null;
    }

    async confirmDelete() {
        if (!this.fichaIdToDelete) return;

        try {
            const response = await fetch(`../../routing.php?controller=ficha&action=destroy&id=${this.fichaIdToDelete}`, {
                method: 'DELETE'
            });
            const result = await response.json();

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.show(result.message, 'success');
                }
                this.closeDeleteModal();
                await this.loadFichas();
            } else {
                throw new Error(result.error || 'Error desconocido');
            }
        } catch (error) {
            console.error('Error deleting ficha:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.fichaView = new FichaView();
});

// Helper functions
window.closeDeleteModal = () => window.fichaView.closeDeleteModal();
window.confirmDelete = () => window.fichaView.confirmDelete();
