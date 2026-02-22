document.addEventListener('DOMContentLoaded', function () {
    let ambientes = [];
    let sedes = [];
    let currentPage = 1;
    const itemsPerPage = 5;
    const urlParams = new URLSearchParams(window.location.search);
    const sedeFilterId = urlParams.get('sede_id');

    const tableBody = document.getElementById('ambientesTableBody');
    const totalAmbientesSpan = document.getElementById('totalAmbientes');
    const searchInput = document.getElementById('searchInput');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const paginationNumbers = document.getElementById('paginationNumbers');
    const showingFrom = document.getElementById('showingFrom');
    const showingTo = document.getElementById('showingTo');
    const totalRecords = document.getElementById('totalRecords');

    // Fetch initial data
    Promise.all([
        fetch('../../routing.php?controller=ambiente&action=index', {
            headers: { 'Accept': 'application/json' }
        }).then(res => res.json()),
        fetch('../../routing.php?controller=sede&action=index', {
            headers: { 'Accept': 'application/json' }
        }).then(res => res.json())
    ]).then(([ambData, sedeData]) => {
        ambientes = ambData;
        sedes = sedeData;

        // Apply URL filter if present
        if (sedeFilterId) {
            ambientes = ambientes.filter(a => a.sede_sede_id == sedeFilterId);
            showFilterBadge();
        }

        renderTable();
    }).catch(err => console.error('Error loading data:', err));

    function showFilterBadge() {
        const sede = sedes.find(s => s.sede_id == sedeFilterId);
        const sedeName = sede ? sede.sede_nombre : 'Sede seleccionada';

        // Check if badge already exists
        if (document.getElementById('sedeFilterBadge')) return;

        const badge = document.createElement('div');
        badge.id = 'sedeFilterBadge';
        badge.className = 'inline-flex items-center gap-2 px-3 py-1 bg-sena-green/10 text-sena-green text-xs font-medium rounded-full border border-sena-green/20 mb-4';
        badge.innerHTML = `
            Filtrando por: ${sedeName}
            <button onclick="window.location.href='index.php'" class="hover:text-green-700 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        const container = document.querySelector('.main-content .p-8');
        if (container) {
            container.insertBefore(badge, container.firstChild);
        }
    }

    function getSedeName(sedeId) {
        const sede = sedes.find(s => s.sede_id == sedeId);
        return sede ? sede.sede_nombre : 'N/A';
    }

    function renderTable() {
        const query = searchInput.value.toLowerCase();
        const filtered = ambientes.filter(a =>
            a.amb_nombre.toLowerCase().includes(query) ||
            (a.sede_nombre && a.sede_nombre.toLowerCase().includes(query))
        );

        const total = filtered.length;
        totalAmbientesSpan.textContent = ambientes.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';
        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center p-8 text-gray-500">No se encontraron ambientes</td></tr>';
        } else {
            paginated.forEach(a => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${a.amb_id}`;
                tr.title = 'Haga clic para ver detalles';

                tr.innerHTML = `
                    <td class="font-semibold text-sena-green">${String(a.amb_id).padStart(3, '0')}</td>
                    <td class="font-medium group-hover:text-sena-green transition-colors">${a.amb_nombre}</td>
                    <td>${a.sede_nombre || 'N/A'}</td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                             <a href="editar.php?id=${a.amb_id}" class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 shadow-sm flex items-center justify-center text-slate-400 hover:text-sena-orange transition-all" title="Editar" onclick="event.stopPropagation()">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </a>
                            <button class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 shadow-sm flex items-center justify-center text-slate-400 hover:text-red-500 transition-all" title="Eliminar" onclick="event.stopPropagation(); window.openDeleteModal('${a.amb_id}', '${a.amb_nombre}')">
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
        const query = searchInput.value.toLowerCase();
        const filtered = ambientes.filter(a =>
            a.amb_nombre.toLowerCase().includes(query) ||
            (a.sede_nombre && a.sede_nombre.toLowerCase().includes(query))
        );
        const totalPages = Math.ceil(filtered.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    };

    // Global delete modal functions
    let ambienteToDeleteId = null;
    window.openDeleteModal = (id, nombre) => {
        ambienteToDeleteId = id;
        document.getElementById('ambienteToDelete').textContent = nombre;
        document.getElementById('deleteModal').classList.add('show');
    };

    window.closeDeleteModal = () => {
        document.getElementById('deleteModal').classList.remove('show');
    };

    document.getElementById('confirmDeleteBtn').onclick = () => {
        if (ambienteToDeleteId) {
            fetch(`../../routing.php?controller=ambiente&action=destroy&id=${ambienteToDeleteId}`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' }
            }).then(res => {
                if (!res.ok) return res.json().then(err => { throw err; });
                return res.json();
            })
                .then(data => {
                    if (data.message) {
                        ambientes = ambientes.filter(a => a.amb_id != ambienteToDeleteId);
                        closeDeleteModal();
                        renderTable();
                        NotificationService.showSuccess('Ambiente eliminado correctamente');
                    } else {
                        NotificationService.showError(data.error);
                    }
                })
                .catch(err => {
                    console.error('Error deleting:', err);
                    NotificationService.showError(err.error || 'Error al eliminar el ambiente');
                });
        }
    };
});
