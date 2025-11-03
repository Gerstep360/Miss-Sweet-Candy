<x-layouts.app :title="__('Gestión de Puntos - Cajero')">
    <div class="min-h-screen bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="dashboard-card mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Gestión de Puntos</h1>
                                <p class="text-sm text-zinc-400">Consulta y canjea puntos de clientes</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-zinc-400 text-sm">Cajero:</span>
                        <span class="bg-blue-500/20 text-blue-400 px-3 py-1.5 rounded-lg font-medium">
                            {{ auth()->user()->name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Buscador de Clientes -->
            <div class="dashboard-card mb-6">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar Cliente
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-zinc-300 font-medium mb-2">Buscar por nombre, email o teléfono</label>
                        <div class="relative">
                            <input type="text" 
                                   id="searchInput"
                                   placeholder="Ej: Juan Pérez, juan@email.com, 555-1234..."
                                   class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pl-11">
                            <svg class="w-5 h-5 text-zinc-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="button" 
                                id="searchBtn"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-blue-500/30 hover:scale-105 active:scale-95 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Buscar Cliente
                        </button>
                    </div>
                </div>

                <!-- Resultados de búsqueda -->
                <div id="searchResults" class="mt-4 hidden">
                    <div class="border border-zinc-700 rounded-lg divide-y divide-zinc-700 max-h-64 overflow-y-auto">
                        <!-- Los resultados se cargarán aquí via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Información del Cliente Seleccionado -->
            <div id="clientInfo" class="dashboard-card mb-6 hidden">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Información del Cliente
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Datos del cliente -->
                    <div class="lg:col-span-2">
                        <div class="bg-gradient-to-br from-zinc-800/50 to-zinc-700/30 rounded-xl p-6 border border-zinc-700/50">
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-lg" id="clientInitials">JD</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-white mb-2" id="clientName">Juan David Pérez</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-zinc-400">Email:</span>
                                            <span class="text-white ml-2" id="clientEmail">juan@email.com</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-400">Teléfono:</span>
                                            <span class="text-white ml-2" id="clientPhone">555-1234</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-400">Cliente desde:</span>
                                            <span class="text-white ml-2" id="clientSince">Ene 2024</span>
                                        </div>
                                        <div>
                                            <span class="text-zinc-400">Total Pedidos:</span>
                                            <span class="text-white ml-2" id="totalOrders">15</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Puntos -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-amber-500/10 to-orange-600/10 rounded-xl p-6 border border-amber-500/30">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 class="text-amber-400 font-bold text-2xl mb-1" id="totalPoints">0</h4>
                                <p class="text-amber-300 text-sm font-medium">Puntos Disponibles</p>
                            </div>
                        </div>

                        <!-- Botón para canjear -->
                        <button type="button" 
                                id="redeemBtn"
                                class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-green-500/30 hover:scale-105 active:scale-95 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Canjear Puntos
                        </button>
                    </div>
                </div>
            </div>

            <!-- Historial de Movimientos -->
            <div id="movementsHistory" class="dashboard-card hidden">
                <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Historial de Movimientos
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-zinc-700">
                                <th class="text-left py-3 px-4 text-zinc-400 font-medium">Fecha</th>
                                <th class="text-left py-3 px-4 text-zinc-400 font-medium">Tipo</th>
                                <th class="text-left py-3 px-4 text-zinc-400 font-medium">Descripción</th>
                                <th class="text-right py-3 px-4 text-zinc-400 font-medium">Puntos</th>
                                <th class="text-right py-3 px-4 text-zinc-400 font-medium">Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="movementsTable">
                            <!-- Los movimientos se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Estado vacío -->
            <div id="emptyState" class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-zinc-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Busca un cliente</h3>
                <p class="text-zinc-400 max-w-md mx-auto">
                    Ingresa el nombre, email o teléfono de un cliente para consultar sus puntos y realizar canjes.
                </p>
            </div>
        </div>
    </div>

    <!-- Modal para Canjear Puntos -->
    <div id="redeemModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" id="redeemModalBackdrop"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-md border border-zinc-700">
                <!-- Header -->
                <div class="p-6 border-b border-zinc-700">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Canjear Puntos
                    </h3>
                    <p class="text-zinc-400 text-sm mt-1" id="modalClientInfo">Cliente: -</p>
                </div>

                <!-- Form -->
                <form id="redeemForm" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" id="redeemClientId" name="cliente_id">
                    
                    <div>
                        <label class="block text-zinc-300 font-medium mb-2">Puntos a Canjear</label>
                        <input type="number" 
                               id="redeemPoints"
                               name="puntos"
                               min="1"
                               class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                               placeholder="Ingresa la cantidad de puntos">
                        <p class="text-zinc-400 text-xs mt-1">
                            Puntos disponibles: <span id="availablePoints" class="text-amber-400 font-medium">0</span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-zinc-300 font-medium mb-2">Descripción del Canje</label>
                        <textarea id="redeemDescription"
                                  name="descripcion"
                                  rows="3"
                                  class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"
                                  placeholder="Ej: Canje por descuento, producto gratuito..."></textarea>
                    </div>

                    <!-- Resumen -->
                    <div class="bg-zinc-800/50 rounded-lg p-4 border border-zinc-700">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-zinc-400">Puntos actuales:</span>
                            <span class="text-white font-medium" id="summaryCurrent">0</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-zinc-400">Puntos a canjear:</span>
                            <span class="text-red-400 font-medium" id="summaryRedeem">0</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-zinc-700">
                            <span class="text-white font-semibold">Nuevo saldo:</span>
                            <span class="text-amber-400 font-bold text-lg" id="summaryNew">0</span>
                        </div>
                    </div>
                </form>

                <!-- Footer -->
                <div class="p-6 border-t border-zinc-700 flex gap-3">
                    <button type="button" 
                            id="cancelRedeem"
                            class="flex-1 bg-zinc-700 hover:bg-zinc-600 text-white py-3 px-6 rounded-lg transition-all duration-200 font-medium">
                        Cancelar
                    </button>
                    <button type="button" 
                            id="confirmRedeem"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white py-3 px-6 rounded-lg transition-all duration-200 font-semibold">
                        Confirmar Canje
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const searchResults = document.getElementById('searchResults');
            const clientInfo = document.getElementById('clientInfo');
            const movementsHistory = document.getElementById('movementsHistory');
            const emptyState = document.getElementById('emptyState');
            const redeemBtn = document.getElementById('redeemBtn');
            const redeemModal = document.getElementById('redeemModal');
            const redeemForm = document.getElementById('redeemForm');
            const availablePoints = document.getElementById('availablePoints');
            const summaryCurrent = document.getElementById('summaryCurrent');
            const summaryRedeem = document.getElementById('summaryRedeem');
            const summaryNew = document.getElementById('summaryNew');
            const modalClientInfo = document.getElementById('modalClientInfo');
            const redeemClientId = document.getElementById('redeemClientId');
            const redeemPoints = document.getElementById('redeemPoints');
            const redeemDescription = document.getElementById('redeemDescription');
            const cancelRedeem = document.getElementById('cancelRedeem');
            const confirmRedeem = document.getElementById('confirmRedeem');

            let currentClient = null;

            // Buscar clientes
            searchBtn.addEventListener('click', searchClients);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') searchClients();
            });

            // Canjear puntos
            redeemBtn.addEventListener('click', openRedeemModal);
            cancelRedeem.addEventListener('click', closeRedeemModal);
            redeemPoints.addEventListener('input', updateRedeemSummary);
            confirmRedeem.addEventListener('click', confirmRedeemPoints);

            // Cerrar modal al hacer clic fuera
            document.getElementById('redeemModalBackdrop').addEventListener('click', closeRedeemModal);

            function searchClients() {
                const query = searchInput.value.trim();
                if (!query) return;

                // Simulación de búsqueda - reemplazar con llamada AJAX real
                simulateSearch(query);
            }

            function simulateSearch(query) {
                // Esto es una simulación - reemplazar con llamada AJAX real a tu backend
                const mockClients = [
                    {
                        id: 1,
                        name: 'Juan David Pérez',
                        email: 'juan@email.com',
                        phone: '555-1234',
                        since: 'Ene 2024',
                        total_orders: 15,
                        points: 1250,
                        movements: [
                            { date: '2024-01-15', type: 'acumulo', desc: 'Compra #001', points: 100, balance: 100 },
                            { date: '2024-01-20', type: 'acumulo', desc: 'Compra #002', points: 150, balance: 250 },
                            { date: '2024-02-01', type: 'canje', desc: 'Descuento 10%', points: -200, balance: 50 },
                            { date: '2024-02-15', type: 'acumulo', desc: 'Compra #015', points: 1200, balance: 1250 }
                        ]
                    },
                    {
                        id: 2,
                        name: 'María González',
                        email: 'maria@email.com',
                        phone: '555-5678',
                        since: 'Feb 2024',
                        total_orders: 8,
                        points: 450,
                        movements: [
                            { date: '2024-02-10', type: 'acumulo', desc: 'Compra #001', points: 50, balance: 50 },
                            { date: '2024-02-20', type: 'acumulo', desc: 'Compra #008', points: 400, balance: 450 }
                        ]
                    }
                ];

                const filteredClients = mockClients.filter(client => 
                    client.name.toLowerCase().includes(query.toLowerCase()) ||
                    client.email.toLowerCase().includes(query.toLowerCase()) ||
                    client.phone.includes(query)
                );

                displaySearchResults(filteredClients);
            }

            function displaySearchResults(clients) {
                if (clients.length === 0) {
                    searchResults.innerHTML = `
                        <div class="p-4 text-center text-zinc-400">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            No se encontraron clientes
                        </div>
                    `;
                } else {
                    searchResults.innerHTML = clients.map(client => `
                        <div class="p-4 hover:bg-zinc-800/50 cursor-pointer transition-colors client-result" data-client='${JSON.stringify(client)}'>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-medium text-sm">${getInitials(client.name)}</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-white font-medium">${client.name}</h4>
                                    <p class="text-zinc-400 text-sm">${client.email} • ${client.phone}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-amber-400 font-bold">${client.points} pts</span>
                                    <p class="text-zinc-400 text-xs">${client.total_orders} pedidos</p>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    // Agregar event listeners a los resultados
                    document.querySelectorAll('.client-result').forEach(item => {
                        item.addEventListener('click', function() {
                            const client = JSON.parse(this.getAttribute('data-client'));
                            selectClient(client);
                        });
                    });
                }

                searchResults.classList.remove('hidden');
            }

            function selectClient(client) {
                currentClient = client;
                
                // Ocultar elementos de búsqueda
                searchResults.classList.add('hidden');
                emptyState.classList.add('hidden');
                
                // Mostrar información del cliente
                document.getElementById('clientInitials').textContent = getInitials(client.name);
                document.getElementById('clientName').textContent = client.name;
                document.getElementById('clientEmail').textContent = client.email;
                document.getElementById('clientPhone').textContent = client.phone;
                document.getElementById('clientSince').textContent = client.since;
                document.getElementById('totalOrders').textContent = client.total_orders;
                document.getElementById('totalPoints').textContent = client.points.toLocaleString();
                
                // Mostrar historial
                displayMovements(client.movements);
                
                // Mostrar secciones
                clientInfo.classList.remove('hidden');
                movementsHistory.classList.remove('hidden');
            }

            function displayMovements(movements) {
                const tableBody = document.getElementById('movementsTable');
                tableBody.innerHTML = movements.map(mov => `
                    <tr class="border-b border-zinc-800 hover:bg-zinc-800/30 transition-colors">
                        <td class="py-3 px-4 text-zinc-300 text-sm">${formatDate(mov.date)}</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                mov.type === 'acumulo' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'
                            }">
                                ${mov.type === 'acumulo' ? 'Acumulación' : 'Canje'}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-zinc-300 text-sm">${mov.desc}</td>
                        <td class="py-3 px-4 text-right text-sm ${
                            mov.points > 0 ? 'text-green-400' : 'text-red-400'
                        } font-medium">
                            ${mov.points > 0 ? '+' : ''}${mov.points}
                        </td>
                        <td class="py-3 px-4 text-right text-amber-400 text-sm font-medium">
                            ${mov.balance.toLocaleString()}
                        </td>
                    </tr>
                `).join('');
            }

            function openRedeemModal() {
                if (!currentClient) return;

                modalClientInfo.textContent = `Cliente: ${currentClient.name}`;
                redeemClientId.value = currentClient.id;
                availablePoints.textContent = currentClient.points.toLocaleString();
                summaryCurrent.textContent = currentClient.points.toLocaleString();
                redeemPoints.value = '';
                redeemDescription.value = '';
                updateRedeemSummary();

                redeemModal.classList.remove('hidden');
            }

            function closeRedeemModal() {
                redeemModal.classList.add('hidden');
            }

            function updateRedeemSummary() {
                const points = parseInt(redeemPoints.value) || 0;
                const current = currentClient.points;
                
                summaryRedeem.textContent = points.toLocaleString();
                summaryNew.textContent = (current - points).toLocaleString();

                // Validar que no exceda los puntos disponibles
                if (points > current) {
                    redeemPoints.classList.add('border-red-500');
                    confirmRedeem.disabled = true;
                } else {
                    redeemPoints.classList.remove('border-red-500');
                    confirmRedeem.disabled = points <= 0;
                }
            }

            function confirmRedeemPoints() {
                const points = parseInt(redeemPoints.value);
                const description = redeemDescription.value.trim();

                if (!points || points <= 0) {
                    alert('Ingresa una cantidad válida de puntos');
                    return;
                }

                if (points > currentClient.points) {
                    alert('El cliente no tiene suficientes puntos');
                    return;
                }

                if (!description) {
                    alert('Ingresa una descripción para el canje');
                    return;
                }

                // Aquí iría la llamada AJAX real al backend
                simulateRedeem(points, description);
            }

            function simulateRedeem(points, description) {
                // Simulación - reemplazar con llamada AJAX real
                alert(`Canje realizado:\n${points} puntos\n${description}`);
                
                // Actualizar puntos del cliente (en una implementación real, esto vendría del backend)
                currentClient.points -= points;
                currentClient.movements.unshift({
                    date: new Date().toISOString().split('T')[0],
                    type: 'canje',
                    desc: description,
                    points: -points,
                    balance: currentClient.points
                });

                // Actualizar la vista
                document.getElementById('totalPoints').textContent = currentClient.points.toLocaleString();
                displayMovements(currentClient.movements);
                
                closeRedeemModal();
            }

            function getInitials(name) {
                return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
            }

            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('es-ES');
            }
        });
    </script>

    <style>
        .dashboard-card {
            background: rgba(39, 39, 42, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(63, 63, 70, 0.5);
            border-radius: 1rem;
            padding: 1.5rem;
        }

        #redeemModal {
            transition: all 0.3s ease;
        }

        #redeemModal:not(.hidden) {
            display: flex !important;
        }
    </style>
</x-layouts.app>