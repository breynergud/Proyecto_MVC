<!-- Global Notifications Container -->
<div id="notification-overlay" class="modal hidden">
    <div id="notification-modal" class="modal-content scale-95 opacity-0 transition-all duration-300">
        <!-- Error Alert -->
        <div id="error-alert" class="hidden">
            <div class="modal-header bg-red-50 border-b border-red-100 p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-exclamation text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Atención detectada</h3>
                    <p class="text-sm text-gray-500">Ha ocurrido un problema con la solicitud.</p>
                </div>
            </div>
            <div class="modal-body p-6">
                <div class="bg-red-50/50 border border-red-100 rounded-xl p-4 mb-4">
                    <p id="error-message-text" class="text-red-800 font-medium"></p>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Si el problema persiste, por favor contacte al administrador del sistema.
                </p>
            </div>
            <div class="modal-footer bg-gray-50 p-4 flex justify-end">
                <button onclick="NotificationService.hide()" class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-semibold hover:bg-gray-800 transition-all active:scale-95 shadow-lg shadow-gray-200">
                    Entendido
                </button>
            </div>
        </div>

        <!-- Success Alert -->
        <div id="success-alert" class="hidden">
            <div class="modal-header bg-green-50 border-b border-green-100 p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-file-circle-check text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Operación exitosa</h3>
                    <p class="text-sm text-gray-500">Todo se ha procesado correctamente.</p>
                </div>
            </div>
            <div class="modal-body p-6 text-center">
                <p id="success-message-text" class="text-gray-700 font-medium text-lg"></p>
            </div>
            <div class="modal-footer bg-gray-50 p-4 flex justify-end">
                <button id="success-close-btn" onclick="NotificationService.hide()" class="px-6 py-2.5 bg-sena-green text-white rounded-xl font-semibold hover:bg-dark-green transition-all active:scale-95 shadow-lg shadow-sena-green/20">
                    Continuar
                </button>
            </div>
        </div>
    </div>
</div>