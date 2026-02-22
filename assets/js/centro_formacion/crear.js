document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('crearCentroForm');

    form.onsubmit = async (e) => {
        e.preventDefault();

        const saveBtn = document.getElementById('saveBtn');
        const originalBtnText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Guardando...';

        try {
            const formData = new FormData(form);
            const response = await fetch('../../routing.php?controller=centro_formacion&action=store', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (response.ok) {
                if (typeof NotificationService !== 'undefined') {
                    NotificationService.showSuccess('Centro registrado exitosamente');
                }
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al guardar');
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
