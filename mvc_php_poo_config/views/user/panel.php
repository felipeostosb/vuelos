<main class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-[1000px] mx-auto px-6">
        
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-bold text-[#0A192F] mb-2">Mi Panel de Viajes</h1>
                <p class="text-gray-500">Gestiona tus próximas aventuras</p>
            </div>
            <div class="w-16 h-16 rounded-full bg-[#0070F3] text-white flex items-center justify-center text-2xl font-bold shadow-md">
                <?php echo substr($_SESSION['user_name'], 0, 1); ?>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <div>
                    <p class="font-bold">¡Reserva confirmada con éxito!</p>
                    <p class="text-sm">Tu código de reserva (PNR) es: <strong class="uppercase"><?php echo htmlspecialchars($_GET['pnr']); ?></strong></p>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="text-xl font-bold text-[#0A192F] mb-6">Mis Próximos Vuelos</h2>

        <?php if (empty($misReservas)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-3xl mx-auto mb-4">
                    <i class="fa-solid fa-plane-slash"></i>
                </div>
                <h3 class="text-xl font-bold text-[#0A192F] mb-2">Aún no tienes reservas</h3>
                <p class="text-gray-500 mb-6">Explora nuestros destinos y planifica tu próximo viaje.</p>
                <a href="?action=home" class="inline-block bg-[#0070F3] hover:bg-[#0051CC] text-white px-8 py-3 rounded-xl font-bold transition-all shadow-md">Buscar Vuelos</a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach (array_reverse($misReservas) as $reserva): 
                    $vuelo = $reserva['vuelo'];
                    $pnr = $reserva['pnr'];
                    $isCheckedIn = ($reserva['estado'] === 'Checked-in');
                ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                    <!-- Ticket Design -->
                    <div class="flex flex-col md:flex-row">
                        <!-- Left Side: Flight Info -->
                        <div class="p-6 flex-1 border-b md:border-b-0 md:border-r border-dashed border-gray-300 relative">
                            <!-- Semi circles for ticket effect -->
                            <div class="hidden md:block w-6 h-6 bg-gray-50 rounded-full absolute -right-3 top-[-12px]"></div>
                            <div class="hidden md:block w-6 h-6 bg-gray-50 rounded-full absolute -right-3 bottom-[-12px]"></div>

                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#EAF4FF] text-[#0070F3] flex items-center justify-center">
                                        <i class="fa-solid fa-plane"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#0A192F]"><?php echo htmlspecialchars($vuelo['airline']); ?></p>
                                        <p class="text-xs text-gray-500">Vuelo <?php echo htmlspecialchars($vuelo['flight_number']); ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Pasajero</p>
                                    <p class="font-bold text-[#0A192F]"><?php echo htmlspecialchars($reserva['pasajero_nombre']); ?></p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-center mt-8">
                                <div>
                                    <p class="text-2xl font-bold text-[#0A192F]"><?php echo htmlspecialchars($vuelo['departure_time']); ?></p>
                                    <p class="text-sm font-medium text-gray-500"><?php echo htmlspecialchars($vuelo['departure_airport']); ?></p>
                                </div>
                                <div class="flex-1 px-4 relative flex flex-col items-center">
                                    <?php if(($reserva['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta'): ?>
                                        <span class="bg-[#0070F3] text-white text-[10px] font-bold px-2 py-0.5 rounded-full absolute -top-6">Ida y Vuelta</span>
                                    <?php endif; ?>
                                    
                                    <i class="fa-solid <?php echo ($reserva['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta' ? 'fa-arrow-right-arrow-left text-[#0070F3]' : 'fa-plane text-gray-300'; ?> mb-1"></i>
                                    <div class="w-full h-[2px] bg-gray-200 border-dashed border-t-2 border-gray-300"></div>
                                    
                                    <?php if(($reserva['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta' && !empty($reserva['fecha_retorno'])): ?>
                                        <span class="text-[10px] font-bold text-gray-500 mt-1">Regreso: <?php echo htmlspecialchars($reserva['fecha_retorno']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-[#0A192F]"><?php echo htmlspecialchars($vuelo['arrival_time']); ?></p>
                                    <p class="text-sm font-medium text-gray-500"><?php echo htmlspecialchars($vuelo['arrival_airport']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: PNR & Actions -->
                        <div class="p-6 md:w-64 flex flex-col justify-center items-center bg-gray-50/50">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Código de Reserva</p>
                            <p class="text-3xl font-extrabold text-[#0070F3] tracking-widest mb-6"><?php echo htmlspecialchars($pnr); ?></p>
                            
                            <?php if ($isCheckedIn): ?>
                                <div class="w-full bg-green-100 text-green-700 py-2.5 rounded-xl font-bold text-center flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-qrcode"></i> Boarding Pass Listo
                                </div>
                            <?php else: ?>
                                <form action="index.php" method="POST" class="w-full">
                                    <input type="hidden" name="action" value="procesarCheckin">
                                    <input type="hidden" name="pnr" value="<?php echo htmlspecialchars($pnr); ?>">
                                    <button type="submit" class="w-full bg-[#0A192F] hover:bg-gray-800 text-white py-2.5 rounded-xl font-bold transition-all shadow-md">
                                        Hacer Check-in
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
