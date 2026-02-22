// Sede View JavaScript
class SedeView {
    constructor() {
        this.sedeId = this.getSedeIdFromUrl();
        this.sedeData = null;
        this.ambientes = [];
        this.filteredAmbientes = [];
        this.ambienteFilters = {
            search: ''
        };
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadSedeData();
        this.initDeleteModal();
    }

    bindEvents() {
        // Ambientes toggle functionality
        const verTodosAmbientesBtn = document.getElementById('verTodosAmbientes');
        const volverAmbientesBtn = document.getElementById('volverAmbientesPreview');

        if (verTodosAmbientesBtn) {
            verTodosAmbientesBtn.addEventListener('click', () => {
                this.showFullAmbientesList();
            });
        }

        if (volverAmbientesBtn) {
            volverAmbientesBtn.addEventListener('click', () => {
                this.showAmbientesPreview();
            });
        }

        // Ambiente filter events
        const searchAmbiente = document.getElementById('searchAmbiente');
        if (searchAmbiente) {
            searchAmbiente.addEventListener('input', (e) => {
                this.ambienteFilters.search = e.target.value;
                this.applyAmbienteFilters();
            });
        }

        // Delete button
        const deleteSedeBtn = document.getElementById('deleteSedeBtn');
        if (deleteSedeBtn) {
            deleteSedeBtn.addEventListener('click', () => {
                this.openDeleteModal();
            });
        }
    }

    initDeleteModal() {
        this.modal = document.getElementById('deleteModal');
        this.modalContent = document.getElementById('modalContent');
        this.modalOverlay = document.getElementById('modalOverlay');
        this.cancelBtn = document.getElementById('cancelDeleteBtn');
        this.confirmBtn = document.getElementById('confirmDeleteBtn');
        this.sedeNameSpan = document.getElementById('sedeToDeleteName');

        if (this.cancelBtn) {
            this.cancelBtn.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.modalOverlay) {
            this.modalOverlay.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.confirmBtn) {
            this.confirmBtn.addEventListener('click', () => this.confirmDelete());
        }

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal && !this.modal.classList.contains('hidden')) {
                this.closeDeleteModal();
            }
        });
    }

    openDeleteModal() {
        if (!this.modal || !this.sedeData) return;

        if (this.sedeNameSpan) {
            this.sedeNameSpan.textContent = this.sedeData.sede_nombre;
        }

        this.modal.classList.remove('hidden');
        // Small delay for animation
        setTimeout(() => {
            if (this.modalContent) {
                this.modalContent.classList.remove('scale-95', 'opacity-0');
                this.modalContent.classList.add('scale-100', 'opacity-100');
            }
        }, 10);
    }

    closeDeleteModal() {
        if (!this.modal || !this.modalContent) return;

        this.modalContent.classList.remove('scale-100', 'opacity-100');
        this.modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            this.modal.classList.add('hidden');
        }, 300);
    }

    async confirmDelete() {
        if (!this.sedeId) return;

        try {
            this.confirmBtn.disabled = true;
            this.confirmBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

            const formData = new FormData();
            formData.append('controller', 'sede');
            formData.append('action', 'destroy');
            formData.append('sede_id', this.sedeId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();
            if (!response.ok || data.error) {
                throw new Error(data.error || 'Error al eliminar la sede');
            }

            this.showSuccessFeedback();

            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);

        } catch (error) {
            console.error('Error deleting sede:', error);
            NotificationService.showError(error.message || 'Hubo un error al intentar eliminar la sede. Por favor, intente de nuevo.');
            this.confirmBtn.disabled = false;
            this.confirmBtn.textContent = 'Sí, eliminar';
        }
    }

    showSuccessFeedback() {
        const overlay = document.getElementById('successOverlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            this.closeDeleteModal();
        }
    }

    getSedeIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    async loadSedeData() {
        if (!this.sedeId) {
            NotificationService.showError('ID de sede no válido');
            return;
        }

        try {
            const sede = await this.fetchSede(this.sedeId);

            if (sede) {
                this.sedeData = sede;
                await this.loadRelatedData();
                this.populateSedeInfo();
                this.showDetails();
            } else {
                this.showError('Sede no encontrada');
            }
        } catch (error) {
            console.error('Error loading sede:', error);
            this.showError('Error al cargar la información de la sede');
        }
    }

    async fetchSede(sedeId) {
        const response = await fetch(`../../routing.php?controller=sede&action=show&id=${sedeId}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!response.ok) {
            return null;
        }
        return await response.json();
    }

    async loadRelatedData() {
        try {
            const [environments] = await Promise.all([
                this.fetchAmbientes(this.sedeId)
            ]);

            this.ambientes = environments;
            this.filteredAmbientes = [...environments];
        } catch (error) {
            console.error('Error loading related data:', error);
            this.ambientes = [];
            this.filteredAmbientes = [];
        }
    }

    async fetchAmbientes(sedeId) {
        try {
            const response = await fetch(`../../routing.php?controller=ambiente&action=index&sede_id=${sedeId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return [];
            return await response.json();
        } catch (error) {
            console.error('Error fetching ambientes:', error);
            return [];
        }
    }

    populateSedeInfo() {
        // Basic info
        const sedeNombreCard = document.getElementById('sedeNombreCard');
        const sedeId = document.getElementById('sedeId');

        if (sedeNombreCard) {
            sedeNombreCard.textContent = this.sedeData.sede_nombre;
        }

        if (sedeId) {
            sedeId.textContent = String(this.sedeData.sede_id).padStart(3, '0');
        }

        // Sede Photo in Regional Card
        const sedeFotoCard = document.getElementById('sedeFotoCard');
        const sedeFotoImg = document.getElementById('sedeFotoImg');
        const sedeDefaultInfo = document.getElementById('sedeDefaultInfo');

        if (sedeFotoCard && sedeFotoImg && sedeDefaultInfo) {
            if (this.sedeData.sede_foto) {
                sedeFotoImg.src = this.sedeData.sede_foto;
                sedeFotoCard.classList.remove('hidden');
                sedeDefaultInfo.classList.add('hidden');
            } else {
                sedeFotoCard.classList.add('hidden');
                sedeDefaultInfo.classList.remove('hidden');
            }
        }

        // Edit link
        const editLink = document.getElementById('editLink');
        if (editLink) {
            editLink.href = `editar.php?id=${this.sedeData.sede_id}`;
        }

        // Populate environments
        this.populateAmbientes();
    }

    populateAmbientes() {
        const ambientesList = document.getElementById('ambientesList');
        const noAmbientes = document.getElementById('noAmbientes');

        if (!this.ambientes || this.ambientes.length === 0) {
            if (ambientesList) ambientesList.style.display = 'none';
            if (noAmbientes) noAmbientes.style.display = 'block';
            return;
        }

        if (noAmbientes) noAmbientes.style.display = 'none';
        if (ambientesList) {
            ambientesList.style.display = 'block';
            ambientesList.innerHTML = '';

            // Show first 3 ambientes in preview mode
            const ambientesToShow = this.ambientes.slice(0, 3);

            ambientesToShow.forEach(ambiente => {
                const ambienteItem = document.createElement('div');
                ambienteItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group';

                ambienteItem.onclick = () => {
                    window.location.href = `../ambiente/ver.php?id=${ambiente.amb_id}`;
                };

                ambienteItem.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-white dark:bg-slate-700 flex items-center justify-center text-slate-400 shadow-sm group-hover:text-sena-orange transition-colors">
                            <i class="fa-solid fa-cube"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">${ambiente.amb_nombre}</p>
                            <p class="text-xs text-slate-500">ID: ${String(ambiente.amb_id).padStart(3, '0')}</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                `;
                ambientesList.appendChild(ambienteItem);
            });

            // Add additional indicator if more than 3
            if (this.ambientes.length > 3) {
                const additionalDiv = document.createElement('div');
                additionalDiv.className = 'mt-4 text-center';
                additionalDiv.innerHTML = `
                    <div class="inline-flex items-center justify-center px-4 py-2 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-500 dark:text-slate-400 w-full bg-slate-50/50 dark:bg-slate-800/30">
                        + ${this.ambientes.length - 3} ambientes registrados en esta sede
                    </div>
                `;
                ambientesList.appendChild(additionalDiv);
            }
        }

        // Populate complete list
        this.renderAmbientesCompleteList();
    }

    renderAmbientesCompleteList() {
        const ambientesListComplete = document.getElementById('ambientesListComplete');
        const totalAmbientesCount = document.getElementById('totalAmbientesCount');
        const noAmbienteFilterResults = document.getElementById('noAmbienteFilterResults');

        if (!ambientesListComplete) return;

        if (totalAmbientesCount) {
            totalAmbientesCount.textContent = this.ambientes.length;
        }

        this.updateFilteredAmbientesCount();

        // Show/hide no results message
        if (this.filteredAmbientes.length === 0 && this.ambienteFilters.search) {
            ambientesListComplete.style.display = 'none';
            if (noAmbienteFilterResults) noAmbienteFilterResults.style.display = 'block';
            return;
        } else {
            ambientesListComplete.style.display = 'block';
            if (noAmbienteFilterResults) noAmbienteFilterResults.style.display = 'none';
        }

        ambientesListComplete.innerHTML = '';
        this.filteredAmbientes.forEach(ambiente => {
            const ambienteItem = document.createElement('div');
            ambienteItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group';

            ambienteItem.onclick = () => {
                window.location.href = `../ambiente/ver.php?id=${ambiente.amb_id}`;
            };

            ambienteItem.innerHTML = `
                <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700 flex justify-between items-center group hover:border-sena-green/30 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 shadow-sm flex items-center justify-center text-sena-green">
                            <i class="fa-solid fa-cube"></i>
                        </div>
                        <span class="font-medium text-slate-700 dark:text-slate-300 text-sm">${ambiente.amb_nombre}</span>
                    </div>
                    <a href="../ambiente/ver.php?id=${ambiente.amb_id}" class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center text-slate-400 hover:text-sena-green hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <i class="fa-solid fa-eye text-sm"></i>
                    </a>
                </div>
            `;

            ambientesListComplete.appendChild(ambienteItem);
        });
    }

    showFullAmbientesList() {
        const preview = document.getElementById('ambientesPreview');
        const fullList = document.getElementById('ambientesFullList');

        if (preview) preview.style.display = 'none';
        if (fullList) fullList.style.display = 'block';

        // Reset filters when opening full list
        this.filteredAmbientes = [...this.ambientes];
        this.renderAmbientesCompleteList();
    }

    showAmbientesPreview() {
        const preview = document.getElementById('ambientesPreview');
        const fullList = document.getElementById('ambientesFullList');

        if (preview) preview.style.display = 'block';
        if (fullList) fullList.style.display = 'none';

        // Clear filters when going back to preview
        this.clearAllAmbienteFilters();
    }

    applyAmbienteFilters() {
        const searchTerm = this.ambienteFilters.search.toLowerCase();

        this.filteredAmbientes = this.ambientes.filter(ambiente => {
            return !searchTerm ||
                ambiente.amb_nombre.toLowerCase().includes(searchTerm) ||
                String(ambiente.amb_id).includes(searchTerm);
        });

        this.renderAmbientesCompleteList();
    }

    updateFilteredAmbientesCount() {
        const filteredCount = document.getElementById('filteredAmbientesCount');
        if (filteredCount) {
            filteredCount.textContent = this.filteredAmbientes.length;
        }
    }

    clearAllAmbienteFilters() {
        this.ambienteFilters = { search: '' };
        const searchInput = document.getElementById('searchAmbiente');
        if (searchInput) searchInput.value = '';
        this.applyAmbienteFilters();
    }

    showDetails() {
        const loadingState = document.getElementById('loadingState');
        const sedeDetails = document.getElementById('sedeDetails');

        if (loadingState) {
            loadingState.style.display = 'none';
        }

        if (sedeDetails) {
            sedeDetails.style.display = 'block';
        }
    }

    showError(message) {
        const loadingState = document.getElementById('loadingState');
        const errorCard = document.getElementById('errorCard');
        const errorMessage = document.getElementById('errorMessage');

        if (loadingState) {
            loadingState.style.display = 'none';
        }

        if (errorMessage) {
            errorMessage.textContent = message;
        }

        if (errorCard) {
            errorCard.style.display = 'block';
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.sedeView = new SedeView();
});