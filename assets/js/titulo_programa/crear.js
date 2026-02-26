class CrearTitulo {
    constructor() {
        this.init();
    }

    init() {
        this.cacheDOM();
        this.bindEvents();
    }

    cacheDOM() {
        this.form = document.getElementById('crearTituloForm');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);

        try {
            const response = await fetch('../../routing.php?controller=titulo_programa&action=store', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const responseText = await response.text();
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Invalid JSON response:', responseText);
                throw new Error(`Respuesta no válida del servidor: ${responseText.substring(0, 50)}...`);
            }

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.show(result.message, 'success');
                } else {
                    alert(result.message);
                }
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al crear el título');
            }
        } catch (error) {
            console.error('Error creating titulo:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            } else {
                alert(error.message);
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CrearTitulo();
});
