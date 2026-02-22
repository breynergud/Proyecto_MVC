// Competencia View JavaScript
class CompetenciaView {
    constructor() {
        this.competenciaId = this.getCompetenciaIdFromUrl();
        this.init();
    }

    init() {
        if (this.competenciaId) {
            this.loadCompetencia();
        } else {
            this.showError('No se especificó el ID de la competencia');
        }
    }

    getCompetenciaIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    async loadCompetencia() {
        const loadingState = document.getElementById('loadingState');
        const detailCard = document.getElementById('detailCard');
        const errorCard = document.getElementById('errorCard');

        try {
            const response = await fetch(`../../routing.php?controller=competencia&action=show&id=${this.competenciaId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('Error al cargar la competencia');
            }

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            this.displayCompetencia(data);

            if (loadingState) loadingState.style.display = 'none';
            if (detailCard) detailCard.style.display = 'block';

        } catch (error) {
            console.error('Error loading competencia:', error);
            if (loadingState) loadingState.style.display = 'none';
            if (errorCard) {
                errorCard.style.display = 'flex';
                const errorMessage = document.getElementById('errorMessage');
                if (errorMessage) {
                    errorMessage.textContent = error.message || 'No se pudo cargar la información de la competencia';
                }
            }
        }
    }

    displayCompetencia(data) {
        // Update title
        const titleElement = document.getElementById('competenciaNombre');
        if (titleElement) {
            titleElement.textContent = data.comp_nombre_corto;
        }

        // Update details
        this.setDetailValue('detailId', data.comp_id);
        this.setDetailValue('detailNombreCorto', data.comp_nombre_corto);
        this.setDetailValue('detailHoras', `${data.comp_horas} horas`);
        this.setDetailValue('detailNorma', data.comp_nombre_unidad_competencia);

        // Update edit button link
        const editBtn = document.getElementById('editBtn');
        if (editBtn) {
            editBtn.href = `editar.php?id=${data.comp_id}`;
        }
    }

    setDetailValue(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = value || '-';
        }
    }

    showError(message) {
        const loadingState = document.getElementById('loadingState');
        const errorCard = document.getElementById('errorCard');
        const errorMessage = document.getElementById('errorMessage');

        if (loadingState) loadingState.style.display = 'none';
        if (errorCard) errorCard.style.display = 'flex';
        if (errorMessage) errorMessage.textContent = message;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CompetenciaView();
});
