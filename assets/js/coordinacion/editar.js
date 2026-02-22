document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('editarCoordinacionForm');
    const centroSelect = document.getElementById('centro_formacion_cent_id');
    const coordId = document.getElementById('coord_id').value;

    // Cargar datos iniciales y centros
    async function init() {
        try {
            // Cargar centros de formación
            const resCentros = await fetch('../../routing.php?controller=coordinacion&action=getCentros', {
                headers: { 'Accept': 'application/json' }
            });
            const centros = await resCentros.json();

            centroSelect.innerHTML = '<option value="">Seleccione un centro...</option>';
            centros.forEach(centro => {
                const option = document.createElement('option');
                option.value = centro.cent_id;
                option.textContent = centro.cent_nombre;
                centroSelect.appendChild(option);
            });

            // Cargar datos de la coordinación
            const resCoord = await fetch(`../../routing.php?controller=coordinacion&action=show&id=${coordId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const coord = await resCoord.json();

            if (coord) {
                document.getElementById('coord_descripcion').value = coord.coord_descripcion;
                document.getElementById('centro_formacion_cent_id').value = coord.centro_formacion_cent_id;
                document.getElementById('coord_nombre_coordinador').value = coord.coord_nombre_coordinador;
                document.getElementById('coord_correo').value = coord.coord_correo || '';
                // La contraseña no se muestra por seguridad
            }
        } catch (error) {
            console.error('Error initializing:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError('Error al cargar los datos de la coordinación');
            }
        }
    }

    init();

    form.onsubmit = async (e) => {
        e.preventDefault();

        const saveBtn = document.getElementById('saveBtn');
        const originalBtnText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Guardando...';

        try {
            const formData = new FormData(form);
            const response = await fetch('../../routing.php?controller=coordinacion&action=update', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (response.ok) {
                if (typeof NotificationService !== 'undefined') {
                    NotificationService.showSuccess('Coordinación actualizada exitosamente');
                }
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al actualizar');
            }
        } catch (error) {
            console.error('Error updating:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError(error.message);
            }
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnText;
        }
    };
});
