document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('crearCoordinacionForm');
    const centroSelect = document.getElementById('centro_formacion_cent_id');

    // Cargar centros de formación
    async function loadCentros() {
        try {
            const response = await fetch('../../routing.php?controller=coordinacion&action=getCentros', {
                headers: { 'Accept': 'application/json' }
            });
            const centros = await response.json();

            centroSelect.innerHTML = '<option value="">Seleccione un centro...</option>';
            centros.forEach(centro => {
                const option = document.createElement('option');
                option.value = centro.cent_id;
                option.textContent = centro.cent_nombre;
                centroSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading centros:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError('Error al cargar la lista de centros');
            }
        }
    }

    loadCentros();

    form.onsubmit = async (e) => {
        e.preventDefault();

        const saveBtn = document.getElementById('saveBtn');
        const originalBtnText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Guardando...';

        try {
            const formData = new FormData(form);
            const response = await fetch('../../routing.php?controller=coordinacion&action=store', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (response.ok) {
                if (typeof NotificationService !== 'undefined') {
                    NotificationService.showSuccess('Coordinación registrada exitosamente');
                }
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                throw new Error(result.error || 'Error desconocido al guardar');
            }
        } catch (error) {
            console.error('Error:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError(error.message);
            }
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnText;
        }
    };
});
