// Competencia Index JavaScript
class CompetenciaIndex {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 5;
        this.allCompetencias = [];
        this.filteredCompetencias = [];
        this.competenciaToDelete = null;

        this.init();
    }

    init() {
        this.loadCompetencias();
        this.bindEvents();
    }

    bindEvents() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });
        }

        // Pagination
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.previousPage());
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }

        // Modal functions
        window.closeDeleteModal = () => this.closeDeleteModal();
        window.confirmDelete = () => this.confirmDelete();
    }

    async loadCompetencias() {
        try {
            const response = await fetch('../../routing.php?controller=competencia&action=index&t=' + new Date().getTime(), {
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            console.log('Respuesta del servidor:', text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Error al parsear JSON:', e);
                throw new Error('El servidor envió una respuesta inválida. Revisa la consola (F12) para ver el error real.');
            }

            if (!response.ok) {
                const errorMessage = data.details || data.error || 'Error desconocido en el servidor';
                throw new Error(errorMessage);
            }

            this.allCompetencias = data;
            this.filteredCompetencias = [...this.allCompetencias];
            this.updateStats();
            this.renderTable();
        } catch (error) {
            console.error('Error loading competencias:', error);
            this.showError('No se pudieron cargar las competencias: ' + error.message);
        }
    }

    handleSearch(searchTerm) {
        const term = searchTerm.toLowerCase().trim();

        if (!term) {
            this.filteredCompetencias = [...this.allCompetencias];
        } else {
            this.filteredCompetencias = this.allCompetencias.filter(comp => {
                return comp.comp_nombre_corto.toLowerCase().includes(term) ||
                    comp.comp_nombre_unidad_competencia.toLowerCase().includes(term);
            });
        }

        this.currentPage = 1;
        this.renderTable();
    }

    updateStats() {
        const totalElement = document.getElementById('totalCompetencias');
        if (totalElement) {
            totalElement.textContent = this.allCompetencias.length;
        }
    }

    renderTable() {
        const tbody = document.getElementById('competenciasTableBody');
        if (!tbody) return;

        // Calculate pagination
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const pageData = this.filteredCompetencias.slice(startIndex, endIndex);

        // Clear table
        tbody.innerHTML = '';

        if (pageData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-8">
                        <div style="text-align: center; padding: 2rem;">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 48px; opacity: 0.3; color:#9ca3af;"></i>
                            <p class="text-gray-500 mt-2">No se encontraron competencias que coincidan con la búsqueda.</p>
                        </div>
                    </td>
            `;
            this.updatePaginationInfo(0, 0);
            return;
        }

        // Render rows
        pageData.forEach(comp => {
            const row = this.createTableRow(comp);
            tbody.appendChild(row);
        });

        this.updatePaginationInfo(startIndex + 1, Math.min(endIndex, this.filteredCompetencias.length));
        this.renderPaginationButtons();
    }

    createTableRow(comp) {
        const tr = document.createElement('tr');

        // Truncar texto largo
        const normaCorta = comp.comp_nombre_unidad_competencia.length > 60
            ? comp.comp_nombre_unidad_competencia.substring(0, 60) + '...'
            : comp.comp_nombre_unidad_competencia;

        tr.innerHTML = `
            <td>${comp.comp_id}</td>
            <td>${this.escapeHtml(comp.comp_nombre_corto)}</td>
            <td>${comp.comp_horas} hrs</td>
            <td>${this.escapeHtml(normaCorta)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="ver.php?id=${comp.id}" class="text-sena-green hover:text-emerald-700 mx-2" title="Ver detalles">
                       <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="editar.php?id=${comp.id}" class="text-indigo-600 hover:text-indigo-900 mx-2" title="Editar">
                       <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <button onclick="competenciaView.openDeleteModal(${comp.id}, '${comp.nombre}')" 
                                class="btn-icon btn-icon-danger" title="Eliminar">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
        `;

        return tr;
    }

    openDeleteModal(id, nombre) {
        this.competenciaToDelete = id;
        const modal = document.getElementById('deleteModal');
        const nameElement = document.getElementById('competenciaToDelete');

        if (nameElement) {
            nameElement.textContent = nombre;
        }

        if (modal) {
            modal.classList.add('show');
        }
    }

    closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.remove('show');
        }
        this.competenciaToDelete = null;
    }

    async confirmDelete() {
        if (!this.competenciaToDelete) return;

        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const originalText = confirmBtn.innerHTML;

        try {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = 'Eliminando...';

            const formData = new FormData();
            formData.append('controller', 'competencia');
            formData.append('action', 'destroy');
            formData.append('comp_id', this.competenciaToDelete);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            this.closeDeleteModal();
            this.showSuccess('Competencia eliminada correctamente');
            await this.loadCompetencias();

        } catch (error) {
            console.error('Error deleting competencia:', error);
            this.showError(error.message || 'No se pudo eliminar la competencia');
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        }
    }

    updatePaginationInfo(from, to) {
        const fromElement = document.getElementById('showingFrom');
        const toElement = document.getElementById('showingTo');
        const totalElement = document.getElementById('totalRecords');

        if (fromElement) fromElement.textContent = from;
        if (toElement) toElement.textContent = to;
        if (totalElement) totalElement.textContent = this.filteredCompetencias.length;
    }

    renderPaginationButtons() {
        const container = document.getElementById('paginationNumbers');
        if (!container) return;

        const totalPages = Math.ceil(this.filteredCompetencias.length / this.itemsPerPage);
        container.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.className = `pagination-btn ${i === this.currentPage ? 'active' : ''}`;
            button.textContent = i;
            button.addEventListener('click', () => {
                this.currentPage = i;
                this.renderTable();
            });
            container.appendChild(button);
        }

        // Update prev/next buttons state
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage === totalPages || totalPages === 0;
    }

    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.renderTable();
        }
    }

    nextPage() {
        const totalPages = Math.ceil(this.filteredCompetencias.length / this.itemsPerPage);
        if (this.currentPage < totalPages) {
            this.currentPage++;
            this.renderTable();
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

// Initialize
let competenciaIndex;
document.addEventListener('DOMContentLoaded', () => {
    competenciaIndex = new CompetenciaIndex();
});
