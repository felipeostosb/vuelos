<main class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-[800px] mx-auto px-6">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-[#0A192F] mb-2">Completa tu Reserva</h1>
            <p class="text-gray-500">Estás a un paso de confirmar tu vuelo</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="bg-[#EAF4FF] px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                <h2 class="font-bold text-[#0A192F]"><i class="fa-solid fa-plane-departure text-[#0070F3] mr-2"></i> Resumen del Vuelo</h2>
                <?php if(($tipo_viaje ?? 'solo_ida') === 'ida_vuelta'): ?>
                    <span class="bg-[#0070F3] text-white text-xs font-bold px-3 py-1 rounded-full">Vuelo Redondo (Ida y Vuelta)</span>
                <?php else: ?>
                    <span class="bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1 rounded-full">Solo Ida</span>
                <?php endif; ?>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#0070F3] flex items-center justify-center text-white shrink-0">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#0A192F]"><?php echo htmlspecialchars($vuelo['airline']); ?></h4>
                            <p class="text-xs text-gray-500">Vuelo <?php echo htmlspecialchars($vuelo['flight_number']); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-8 text-center">
                        <div>
                            <p class="text-xl font-bold text-[#0A192F]"><?php echo htmlspecialchars($vuelo['departure_time']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($vuelo['departure_airport']); ?></p>
                        </div>
                        <div class="text-gray-400">
                            <i class="fa-solid <?php echo ($tipo_viaje ?? 'solo_ida') === 'ida_vuelta' ? 'fa-arrow-right-arrow-left' : 'fa-arrow-right'; ?>"></i>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-[#0A192F]"><?php echo htmlspecialchars($vuelo['arrival_time']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($vuelo['arrival_airport']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-gray-600 font-medium">Pasajeros: <?php echo htmlspecialchars($pasajeros); ?></span>
                    <span class="text-2xl font-extrabold text-[#0A192F]">S/. <?php echo htmlspecialchars($vuelo['price'] * $pasajeros); ?></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-[#0A192F]"><i class="fa-solid fa-user text-gray-400 mr-2"></i> Datos del Pasajero Principal</h2>
            </div>
            <div class="p-6">
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="confirmarReserva">
                    <input type="hidden" name="flight_id" value="<?php echo htmlspecialchars($vuelo['id']); ?>">
                    <input type="hidden" name="pasajeros" value="<?php echo htmlspecialchars($pasajeros); ?>">
                    <input type="hidden" name="tipo_viaje" value="<?php echo htmlspecialchars($tipo_viaje ?? 'solo_ida'); ?>">
                    <input type="hidden" name="fecha_retorno" value="<?php echo htmlspecialchars($fecha_retorno ?? ''); ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-bold text-[#0A192F] mb-2">Nombre completo</label>
                            <input type="text" name="nombre" required placeholder="Ej: Ana María García" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3]">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#0A192F] mb-2">Correo electrónico</label>
                            <input type="email" name="email" required placeholder="tu@email.com" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3]">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#0070F3] hover:bg-[#0051CC] text-white py-4 rounded-xl font-bold transition-all shadow-md text-lg flex justify-center items-center gap-3">
                        <i class="fa-solid fa-lock"></i> Confirmar y Pagar S/. <?php echo htmlspecialchars($vuelo['price'] * $pasajeros); ?>
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-4"><i class="fa-solid fa-shield-halved"></i> Pago 100% seguro y encriptado</p>
                </form>
            </div>
        </div>

    </div>
</main>
