<x-layouts.app title="Ubicación - Café Aroma">
    <div class="min-h-screen bg-gray-900 py-12">
        <div class="max-w-6xl mx-auto px-8">
            <!-- Header de la página -->
            <div class="text-center mb-12">
                <h1 class="font-display text-4xl font-bold text-white mb-4">
                    📍 Nuestra Ubicación
                </h1>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Visítanos en el corazón de la ciudad y disfruta de la mejor experiencia de café
                </p>
            </div>

            <!-- Mapa interactivo -->
            <div class="bg-gray-800 rounded-3xl overflow-hidden shadow-2xl border border-gray-700 mb-12">
                <iframe
                    src="https://www.google.com/maps?q=-17.7433967590332,-63.1631317138672&hl=es;z=16&output=embed"
                    width="100%"
                    height="500"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    class="w-full">
                </iframe>
            </div>

            <!-- Información adicional -->
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Información de contacto -->
                <div class="bg-gray-800 rounded-2xl p-8 border border-gray-700">
                    <h2 class="font-display text-2xl font-semibold text-white mb-6 flex items-center gap-3">
                        📋 Información de Contacto
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center text-black">
                                📍
                            </div>
                            <div>
                                <p class="text-white font-medium">Dirección</p>
                                <p class="text-gray-400 text-sm">Av. Principal 123, Centro de la Ciudad</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center text-black">
                                📞
                            </div>
                            <div>
                                <p class="text-white font-medium">Teléfono</p>
                                <p class="text-gray-400 text-sm">(555) 123-CAFÉ</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center text-black">
                                🕐
                            </div>
                            <div>
                                <p class="text-white font-medium">Horarios</p>
                                <p class="text-gray-400 text-sm">Lunes a Domingo: 6:00 AM - 10:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center text-black">
                                📧
                            </div>
                            <div>
                                <p class="text-white font-medium">Email</p>
                                <p class="text-gray-400 text-sm">hola@cafearoma.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cómo llegar -->
                <div class="bg-gray-800 rounded-2xl p-8 border border-gray-700">
                    <h2 class="font-display text-2xl font-semibold text-white mb-6 flex items-center gap-3">
                        🚗 Cómo Llegar
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-yellow-600 font-medium mb-2">🚌 Transporte Público</h3>
                            <p class="text-gray-400 text-sm">
                                Líneas de autobús: 15, 23, 47<br>
                                Parada: "Centro Comercial Plaza"
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-yellow-600 font-medium mb-2">🚗 En Vehículo</h3>
                            <p class="text-gray-400 text-sm">
                                Estacionamiento gratuito disponible<br>
                                Acceso por Av. Principal
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-yellow-600 font-medium mb-2">🚶 A Pie</h3>
                            <p class="text-gray-400 text-sm">
                                A 5 minutos de la Plaza Central<br>
                                Entrada principal por calle San Martín
                            </p>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-8 space-y-3">
                        <a href="https://www.google.com/maps/dir//-17.7433967590332,-63.1631317138672" 
                           target="_blank"
                           class="w-full bg-yellow-600 text-black py-3 px-4 rounded-lg font-medium hover:bg-yellow-500 transition-colors flex items-center justify-center gap-2">
                            🧭 Obtener Direcciones
                        </a>
                        
                        <a href="tel:+555123CAFE" 
                           class="w-full bg-gray-700 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-600 transition-colors flex items-center justify-center gap-2">
                            📞 Llamar Ahora
                        </a>
                    </div>
                </div>
            </div>

            <!-- Botón de regreso -->
            <div class="text-center mt-12">
                <a href="{{ route('welcome') }}" 
                   class="inline-flex items-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors border border-gray-700">
                    ← Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>