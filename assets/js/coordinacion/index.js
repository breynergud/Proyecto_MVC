document.addEventListener('DOMContentLoaded', function () {
    let coordinaciones = [];
    let currentPage = 1;
    const itemsPerPage = 5;

    const tableBody = document.getElementById('coordinacionesTableBody');
    const totalCoordinacionesSpan = document.getElementById('totalCoordinaciones');
    const searchInput = document.getElementById('searchInput');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const paginationNumbers = document.getElementById('paginationNumbers');
    const showingFrom = document.getElementById('showingFrom');
    const showingTo = document.getElementById('showingTo');
    const totalRecords = document.getElementById('totalRecords');

    // Fetch initial data
    async function fetchData() {
        try {
            const response = await fetch('../../routing.php?controller=coordinacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al obtener datos');
            coordinaciones = await response.json();
            renderTable();
        } catch (error) {
            console.error('Error loading data:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError('No se pudieron cargar las coordinaciones');
            }
        }
    }

    fetchData();

    function renderTable() {
        const query = searchInput.value.toLowerCase();
        const filtered = coordinaciones.filter(c =>
            c.coord_descripcion.toLowerCase().includes(query) ||
            (c.coord_nombre_coordinador && c.coord_nombre_coordinador.toLowerCase().includes(query)) ||
            (c.cent_nombre && c.cent_nombre.toLowerCase().includes(query))
        );

        const total = filtered.length;
        if (totalCoordinacionesSpan) totalCoordinacionesSpan.textContent = coordinaciones.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';
        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-gray-500">No se encontraron coordinaciones</td></tr>';
        } else {
            paginated.forEach(c => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `index.php`; // Cambiar a ver.php si se requiere

                tr.innerHTML = `
                    <td class="font-semibold text-sena-green">${String(c.coord_id).padStart(3, '0')}</td>
                    <td>
                        <div class="font-medium text-slate-900">${c.coord_descripcion}</div>
                        <div class="text-xs text-slate-500">${c.cent_nombre || 'Sin Centro'}</div>
                    </td>
                    <td class="text-slate-700">${c.coord_nombre_coordinador || 'N/A'}</td>
                    <td class="text-slate-600 font-mono text-sm">${c.coord_correo || 'N/A'}</td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                             <a href="editar.php?id=${c.coord_id}" class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-sena-orange transition-all" title="Editar" onclick="event.stopPropagation()">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>
                            <button class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-red-500 transition-all" title="Eliminar" onclick="event.stopPropagation(); window.openDeleteModal('${c.coord_id}', '${c.coord_descripcion}')">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }

        if (showingFrom) showingFrom.textContent = total > 0 ? start + 1 : 0;
        if (showingTo) showingTo.textContent = end;
        if (totalRecords) totalRecords.textContent = total;

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        paginationNumbers.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = `pagination-number ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => {
                currentPage = i;
                renderTable();
            };
            paginationNumbers.appendChild(btn);
        }
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages || totalPages === 0;
    }

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        renderTable();
    });

    prevBtn.onclick = () => {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    };

    nextBtn.onclick = () => {
        const totalPages = Math.ceil(coordinaciones.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    };

    // Modal de eliminación
    let coordinacionToDeleteId = null;
    window.openDeleteModal = (id, descripcion) => {
        coordinacionToDeleteId = id;
        document.getElementById('coordinacionToDelete').textContent = descripcion;
        document.getElementById('deleteModal').classList.add('show');
    };

    window.closeDeleteModal = () => {
        document.getElementById('deleteModal').classList.remove('show');
    };

    document.getElementById('confirmDeleteBtn').onclick = async () => {
        if (coordinacionToDeleteId) {
            try {
                const response = await fetch(`../../routing.php?controller=coordinacion&action=destroy&id=${coordinacionToDeleteId}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.message) {
                    coordinaciones = coordinaciones.filter(c => c.coord_id != coordinacionToDeleteId);
                    closeDeleteModal();
                    renderTable();
                    if (typeof NotificationService !== 'undefined') {
                        NotificationService.showSuccess('Coordinación eliminada correctamente');
                    }
                } else {
                    throw new Error(data.error);
                }
            } catch (error) {
                console.error('Error deleting:', error);
                if (typeof NotificationService !== 'undefined') {
                    NotificationService.showError(error.message || 'Error al eliminar la coordinación');
                }
            }
        }
    };
});
