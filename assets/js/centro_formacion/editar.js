document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('editarCentroForm');
    const centId = document.getElementById('cent_id').value;

    async function loadCentro() {
        try {
            const response = await fetch(`../../routing.php?controller=centro_formacion&action=show&id=${centId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const centro = await response.json();

            if (centro) {
                document.getElementById('cent_nombre').value = centro.cent_nombre;
            }
        } catch (error) {
            console.error('Error loading centro:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError('No se pudieron cargar los datos del centro');
            }
        }
    }

    loadCentro();

    form.onsubmit = async (e) => {
        e.preventDefault();

        const saveBtn = document.getElementById('saveBtn');
        const originalBtnText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Guardando...';

        try {
            const formData = new FormData(form);
            const response = await fetch('../../routing.php?controller=centro_formacion&action=update', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (response.ok) {
                if (typeof NotificationService !== 'undefined') {
                    NotificationService.showSuccess('Centro actualizado correctamente');
                }
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al actualizar');
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
