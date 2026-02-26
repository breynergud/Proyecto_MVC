<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENA - Inicio de Sesión</title>
    <!-- Tailwind CSS (utilizando el mismo que en dashboard/layouts si aplica, aquí usaré CDN para asegurar diseño rápido y limpio) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sena-green { color: #39A900; }
        .bg-sena-green { background-color: #39A900; }
        .hover-sena-green:hover { background-color: #2d8a00; }
        .sena-light { background-color: #f7fdf5; }
    </style>
</head>
<body class="sena-light flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <img src="../../assets/imagenes/LOGOsena.png" alt="SENA Logo" class="mx-auto h-20 mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Sistema de Asignación</h1>
            <p class="text-gray-500 mt-2">Inicio de Sesión</p>
        </div>

        <div id="error-message" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm text-center">
            <!-- Mensaje de error aquí -->
        </div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" id="correo" name="correo" required class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2 border" placeholder="correo@sena.edu.co">
                </div>
            </div>

            <div>
                <label for="rol" class="block text-sm font-medium text-gray-700">Rol</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-user-tag text-gray-400"></i>
                    </div>
                    <select id="rol" name="rol" required class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2 border">
                        <option value="">Seleccione su rol</option>
                        <option value="centro">Centro de Formación</option>
                        <option value="coordinador">Coordinador</option>
                        <option value="instructor">Instructor</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="password" name="password" required class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2 border" placeholder="••••••••">
                </div>
            </div>

            <div>
                <button type="submit" id="submitBtn" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sena-green hover-sena-green focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    Ingresar
                </button>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        const errorDiv = document.getElementById('error-message');
        const formData = new FormData(this);
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Ingresando...';
        errorDiv.classList.add('hidden');

        try {
            const response = await fetch('../../routing.php?controller=auth&action=login', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (response.ok) {
                window.location.href = data.redirect;
            } else {
                errorDiv.textContent = data.error || 'Autenticación fallida';
                errorDiv.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = 'Ingresar';
            }
        } catch (error) {
            errorDiv.textContent = 'Error de conexión con el servidor';
            errorDiv.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = 'Ingresar';
        }
    });
    </script>
</body>
</html>
