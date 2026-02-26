document.addEventListener('DOMContentLoaded', function () {
    let centros = [];
    let currentPage = 1;
    const itemsPerPage = 5;

    const tableBody = document.getElementById('centrosTableBody');
    const totalCentrosSpan = document.getElementById('totalCentros');
    const searchInput = document.getElementById('searchInput');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const paginationNumbers = document.getElementById('paginationNumbers');
    const showingFrom = document.getElementById('showingFrom');
    const showingTo = document.getElementById('showingTo');
    const totalRecords = document.getElementById('totalRecords');

    // Cargar datos iniciales
    async function fetchData() {
        try {
            const response = await fetch('../../routing.php?controller=centro_formacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al obtener centros');
            centros = await response.json();
            renderTable();
        } catch (error) {
            console.error('Error loading data:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError('No se pudieron cargar los centros de formación');
            }
        }
    }

    fetchData();

    function renderTable() {
        const query = searchInput.value.toLowerCase();
        const filtered = centros.filter(c =>
            c.cent_nombre.toLowerCase().includes(query) ||
            (c.cent_correo && c.cent_correo.toLowerCase().includes(query)) ||
            String(c.cent_id).includes(query)
        );

        const total = filtered.length;
        if (totalCentrosSpan) totalCentrosSpan.textContent = centros.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';
        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center p-8 text-gray-500">No se encontraron centros</td></tr>';
        } else {
            paginated.forEach(c => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `editar.php?id=${c.cent_id}`;

                tr.innerHTML = `
                    <td class="font-semibold text-sena-green">${String(c.cent_id).padStart(3, '0')}</td>
                    <td class="font-medium text-slate-900">${c.cent_nombre}</td>
                    <td class="text-gray-600">${c.cent_correo || '-'}</td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                             <a href="editar.php?id=${c.cent_id}" class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-sena-orange transition-all" title="Editar" onclick="event.stopPropagation()">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>
                            <button class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-red-500 transition-all" title="Eliminar" onclick="event.stopPropagation(); window.openDeleteModal('${c.cent_id}', '${c.cent_nombre}')">
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
        const totalPages = Math.ceil(centros.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    };

    // Modal de eliminación
    let centroToDeleteId = null;
    window.openDeleteModal = (id, nombre) => {
        centroToDeleteId = id;
        document.getElementById('centroToDelete').textContent = nombre;
        document.getElementById('deleteModal').classList.add('show');
    };

    window.closeDeleteModal = () => {
        document.getElementById('deleteModal').classList.remove('show');
    };

    document.getElementById('confirmDeleteBtn').onclick = async () => {
        if (centroToDeleteId) {
            try {
                const response = await fetch(`../../routing.php?controller=centro_formacion&action=destroy&id=${centroToDeleteId}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (response.ok) {
                    centros = centros.filter(c => c.cent_id != centroToDeleteId);
                    closeDeleteModal();
                    renderTable();
                    if (typeof NotificationService !== 'undefined') {
                        NotificationService.showSuccess('Centro eliminado correctamente');
                    }
                } else {
                    throw new Error(data.error);
                }
            } catch (error) {
                console.error('Error deleting:', error);
                if (typeof NotificationService !== 'undefined') {
                    NotificationService.showError(error.message || 'Error al eliminar el centro');
                }
            }
        }
    };
});
