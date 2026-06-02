<main class="bg-gray-50 pb-20">
    
    <section class="bg-[#0090FF] text-white py-20">
        <div class="max-w-[1280px] mx-auto px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Check-in y gestión de viajes</h1>
            <p class="text-lg md:text-xl text-white/90">Consulta, modifica y gestiona fácilmente</p>
        </div>
    </section>

    <div class="max-w-[1280px] mx-auto px-8 mt-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="tarjeta-animada bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-100 flex flex-col">
                <div class="h-40 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=800&q=80" alt="Avión en vuelo" class="w-full h-full object-cover">
                </div>
                <div class="p-8 flex-1 flex flex-col justify-between text-center">
                    <div>
                        <h3 class="text-xl font-bold text-[#0A192F] mb-2">Estado del vuelo</h3>
                        <p class="text-sm text-gray-500 mb-6">Consulta en tiempo real</p>
                        <input type="text" placeholder="Ej: LA 2451" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-center mb-6 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3] transition-colors">
                    </div>
                    <button type="button" class="w-full bg-[#0070F3] hover:bg-[#0051CC] text-white py-3 rounded-xl font-medium transition-colors">
                        Consultar
                    </button>
                </div>
            </div>

            <div class="tarjeta-animada bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-100 flex flex-col">
                <div class="h-40 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=80" alt="Pasajero sonriente" class="w-full h-full object-cover">
                </div>
                <div class="p-8 flex-1 flex flex-col justify-between text-center">
                    <h3 class="text-xl font-bold text-[#0A192F] mb-8">Cambios y cancelaciones</h3>
                    
                    <div class="flex justify-between items-center mb-8 relative px-4">
                        <div class="absolute top-5 left-8 right-8 h-[2px] bg-gray-200 -z-10"></div>
                        
                        <div class="flex flex-col items-center bg-white px-2">
                            <div class="w-10 h-10 rounded-full bg-[#0070F3] text-white flex items-center justify-center font-bold mb-2 shadow-sm">1</div>
                            <span class="text-xs text-gray-500">Selecciona</span>
                        </div>
                        <div class="flex flex-col items-center bg-white px-2">
                            <div class="w-10 h-10 rounded-full bg-[#0070F3] text-white flex items-center justify-center font-bold mb-2 shadow-sm">2</div>
                            <span class="text-xs text-gray-500">Elige fecha</span>
                        </div>
                        <div class="flex flex-col items-center bg-white px-2">
                            <div class="w-10 h-10 rounded-full bg-[#0070F3] text-white flex items-center justify-center font-bold mb-2 shadow-sm">3</div>
                            <span class="text-xs text-gray-500">Confirma</span>
                        </div>
                    </div>

                    <div class="bg-[#FFF8E6] text-[#D9A000] py-3 px-4 rounded-xl text-sm font-medium border border-[#FFE8A1]">
                        ⚠️ Políticas varían por aerolínea
                    </div>
                </div>
            </div>

            <div class="tarjeta-animada bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-100 flex flex-col">
                <div class="h-40 overflow-hidden bg-gray-100 relative">
                    <img src="https://images.unsplash.com/photo-1554652250-86ec1965ce61?auto=format&fit=crop&w=800&q=80" alt="Equipaje" class="w-full h-full object-cover">
                </div>
                <div class="p-8 flex-1 flex flex-col text-center">
                    <h3 class="text-xl font-bold text-[#0A192F] mb-6">Políticas de equipaje</h3>
                    
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-[#0A192F] text-white text-center">
                                <tr>
                                    <th class="py-3 px-3 font-medium"></th>
                                    <th class="py-3 px-3 font-medium">Económica</th>
                                    <th class="py-3 px-3 font-medium">Business</th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-gray-600">
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-3 text-left font-semibold text-[#0A192F]">Cabina</td>
                                    <td class="py-3 px-3">1 x 10kg</td>
                                    <td class="py-3 px-3">2 x 10kg</td>
                                </tr>
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-3 text-left font-semibold text-[#0A192F]">Bodega</td>
                                    <td class="py-3 px-3">1 x 23kg</td>
                                    <td class="py-3 px-3">2 x 32kg</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-3 text-left font-semibold text-[#0A192F]">Extra</td>
                                    <td class="py-3 px-3">S/. 80</td>
                                    <td class="py-3 px-3 text-[#34C759] font-bold">✅ Gratis</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
