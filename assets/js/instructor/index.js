// Instructor Management JavaScript
class InstructorManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 5;
        this.totalItems = 0;
        this.instructores = [];
        this.filteredInstructores = [];
        this.deleteInstructorId = null;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadInstructores();
    }

    bindEvents() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filterInstructores(e.target.value);
            });
        }

        // Pagination
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.renderTable();
                    this.updatePagination();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const totalPages = Math.ceil(this.filteredInstructores.length / this.itemsPerPage);
                if (this.currentPage < totalPages) {
                    this.currentPage++;
                    this.renderTable();
                    this.updatePagination();
                }
            });
        }

        // Modal events
        window.closeDeleteModal = () => {
            this.closeDeleteModal();
        };

        window.confirmDelete = () => {
            this.deleteInstructor();
        };
    }

    async loadInstructores() {
        try {
            const response = await this.fetchInstructores();
            this.instructores = response;
            this.filteredInstructores = [...this.instructores];
            this.totalItems = this.instructores.length;

            this.updateStats();
            this.renderTable();
            this.updatePagination();
        } catch (error) {
            console.error('Error loading instructores:', error);
            this.showError('Error al cargar los instructores: ' + error.message);
        }
    }

    async fetchInstructores() {
        const response = await fetch('../../routing.php?controller=instructor&action=index&t=' + new Date().getTime(), {
            headers: { 'Accept': 'application/json' }
        });
        const text = await response.text();

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Error al parsear JSON:', e);
            throw new Error('El servidor envió una respuesta inválida.');
        }

        if (!response.ok) {
            const errorMessage = data.details || data.error || 'Error desconocido en el servidor';
            throw new Error(errorMessage);
        }

        return data;
    }

    filterInstructores(searchTerm) {
        if (!searchTerm.trim()) {
            this.filteredInstructores = [...this.instructores];
        } else {
            const term = searchTerm.toLowerCase();
            this.filteredInstructores = this.instructores.filter(inst =>
                inst.inst_nombres.toLowerCase().includes(term) ||
                inst.inst_apellidos.toLowerCase().includes(term) ||
                inst.inst_correo.toLowerCase().includes(term)
            );
        }

        this.currentPage = 1;
        this.renderTable();
        this.updatePagination();
    }

    renderTable() {
        const tbody = document.getElementById('instructoresTableBody');
        if (!tbody) return;

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const pageItems = this.filteredInstructores.slice(startIndex, endIndex);

        tbody.innerHTML = '';

        if (pageItems.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-8">
                        <div style="text-align: center; padding: 2rem;">
                            <i class="fa-solid fa-magnifying-glass" style="font-size:2rem;color:#9ca3af;margin-bottom:0.5rem;"></i>
                            <p style="color: #6b7280;">No se encontraron instructores que coincidan con la búsqueda.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        pageItems.forEach(inst => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
            // Click to edit
            row.setAttribute('onclick', `window.location.href='editar.php?id=${inst.inst_id}'`);
            row.title = 'Haga clic para editar';

            const nombreCompleto = `${inst.inst_nombres} ${inst.inst_apellidos}`;

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-sena-green">
                    ${String(inst.inst_id).padStart(3, '0')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    <div class="font-medium group-hover:text-sena-green transition-colors">${nombreCompleto}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    ${inst.inst_correo}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    ${inst.inst_telefono || '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button onclick="instructorView.openDeleteModal(${inst.inst_id}, '${nombreCompleto.replace(/'/g, "\\'")}')" 
                                class="btn-icon btn-icon-danger" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                </td>
            `;

            tbody.appendChild(row);
        });
    }

    updatePagination() {
        const totalPages = Math.ceil(this.filteredInstructores.length / this.itemsPerPage);
        const startItem = (this.currentPage - 1) * this.itemsPerPage + 1;
        const endItem = Math.min(this.currentPage * this.itemsPerPage, this.filteredInstructores.length);

        // Update pagination info
        const showingFrom = document.getElementById('showingFrom');
        const showingTo = document.getElementById('showingTo');
        const totalRecords = document.getElementById('totalRecords');

        if (showingFrom) showingFrom.textContent = this.filteredInstructores.length > 0 ? startItem : 0;
        if (showingTo) showingTo.textContent = endItem;
        if (totalRecords) totalRecords.textContent = this.filteredInstructores.length;

        // Update pagination buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) {
            prevBtn.disabled = this.currentPage === 1;
        }

        if (nextBtn) {
            nextBtn.disabled = this.currentPage === totalPages || totalPages === 0;
        }

        // Update pagination numbers
        const paginationNumbers = document.getElementById('paginationNumbers');
        if (paginationNumbers) {
            paginationNumbers.innerHTML = '';

            for (let i = 1; i <= Math.min(totalPages, 5); i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `pagination-number ${i === this.currentPage ? 'active' : ''}`;
                pageBtn.textContent = i;
                pageBtn.addEventListener('click', () => {
                    this.currentPage = i;
                    this.renderTable();
                    this.updatePagination();
                });
                paginationNumbers.appendChild(pageBtn);
            }
        }
    }

    updateStats() {
        // Can be improved later
    }

    openDeleteModal(id, nombre) {
        this.deleteInstructorId = id;
        const modal = document.getElementById('deleteModal');
        const instToDelete = document.getElementById('instructorToDelete');

        if (instToDelete) {
            instToDelete.textContent = nombre;
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
        this.deleteInstructorId = null;
    }

    async deleteInstructor() {
        if (!this.deleteInstructorId) return;

        try {
            await this.deleteInstructorFetch(this.deleteInstructorId);

            // Remove from local array
            this.instructores = this.instructores.filter(inst => inst.inst_id !== this.deleteInstructorId);
            this.filteredInstructores = this.filteredInstructores.filter(inst => inst.inst_id !== this.deleteInstructorId);

            this.closeDeleteModal();
            this.updateStats();
            this.renderTable();
            this.updatePagination();

            this.showSuccess('Instructor eliminado correctamente');
        } catch (error) {
            console.error('Error deleting instructor:', error);
            this.showError(error.message || 'Error al eliminar el instructor');
        }
    }

    async deleteInstructorFetch(id) {
        const formData = new FormData();
        formData.append('controller', 'instructor');
        formData.append('action', 'destroy');
        formData.append('id', id);

        const response = await fetch('../../routing.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Respuesta inválida del servidor');
        }

        if (!response.ok || data.error) {
            throw new Error(data.error || 'Error al eliminar el instructor');
        }
        return data;
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
            alert(message);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.instructorManager = new InstructorManager();
});
