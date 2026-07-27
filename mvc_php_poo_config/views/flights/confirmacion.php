<main class="py-12 bg-gray-50 min-h-[80vh] flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-center text-white">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm shadow-inner">
                    <i class="fa-solid fa-check text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">¡Reserva Confirmada!</h1>
                <p class="text-green-100">Tu vuelo ha sido reservado exitosamente y el pago procesado.</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="text-center mb-8">
                    <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider mb-2">Código de Reserva (PNR)</p>
                    <div class="inline-block bg-gray-100 border-2 border-dashed border-gray-300 px-8 py-3 rounded-xl">
                        <span class="text-4xl font-mono font-bold text-[#0A192F] tracking-widest"><?php echo htmlspecialchars($_GET['pnr'] ?? 'XXXXXX'); ?></span>
                    </div>
                </div>

                <?php if (isset($reserva) && !empty($reserva)): ?>
                <!-- Detalle de Boletos y Vuelo -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-8">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-4">
                        <span class="text-sm font-bold text-slate-600 uppercase tracking-wider">
                            <i class="fa-solid fa-ticket text-[#0070F3] mr-2"></i> Boletos Comprados
                        </span>
                        <span class="bg-[#0070F3] text-white text-xs font-bold px-3 py-1 rounded-full">
                            <?php echo htmlspecialchars($reserva['pasajeros_count']); ?> boleto<?php echo $reserva['pasajeros_count'] > 1 ? 's' : ''; ?>
                        </span>
                    </div>

                    <?php if (isset($reserva['vuelo'])): $v = $reserva['vuelo']; ?>
                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                        <div>
                            <p class="text-xs text-slate-500">Aerolínea / Vuelo</p>
                            <p class="font-bold text-slate-800"><?php echo htmlspecialchars($v['airline']); ?> (<?php echo htmlspecialchars($v['flight_number']); ?>)</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Ruta</p>
                            <p class="font-bold text-slate-800"><?php echo htmlspecialchars($v['departure_airport']); ?> ➔ <?php echo htmlspecialchars($v['arrival_airport']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Horarios</p>
                            <p class="font-bold text-slate-800"><?php echo htmlspecialchars($v['departure_time']); ?> - <?php echo htmlspecialchars($v['arrival_time']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Tipo de Viaje</p>
                            <p class="font-bold text-slate-800"><?php echo ($reserva['tipo_viaje'] === 'ida_vuelta') ? 'Ida y Vuelta' : 'Solo Ida'; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Pasajeros -->
                    <?php if (!empty($reserva['pasajeros'])): ?>
                    <div class="pt-4 border-t border-slate-200 mb-4">
                        <p class="text-xs text-slate-500 mb-2 font-semibold">Pasajero(s) Registrado(s):</p>
                        <ul class="list-disc list-inside text-sm text-slate-700 font-medium">
                            <?php foreach ($reserva['pasajeros'] as $pas): ?>
                                <li><?php echo htmlspecialchars(trim($pas['nombre'] . ' ' . $pas['apellido'])); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Desglose de Precios -->
                    <?php 
                        $total_reserva = (float)$reserva['precio_total'];
                        $pasajeros_res = max(1, (int)$reserva['pasajeros_count']);
                        $precio_x_persona = $total_reserva / $pasajeros_res;
                        $es_ida_vuelta = ($reserva['tipo_viaje'] === 'ida_vuelta');
                    ?>
                    <div class="pt-4 border-t border-slate-200 mb-4 text-xs text-slate-600 space-y-1.5">
                        <p class="font-bold text-slate-700 uppercase tracking-wider mb-2">Desglose del Pago:</p>
                        <?php if ($es_ida_vuelta): ?>
                            <div class="flex justify-between">
                                <span>Pasaje Vuelo de Ida (c/u):</span>
                                <span class="font-semibold">S/. <?php echo number_format($precio_x_persona * 0.5, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pasaje Vuelo de Vuelta (c/u):</span>
                                <span class="font-semibold">S/. <?php echo number_format($precio_x_persona * 0.5, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-blue-700 font-semibold pt-1 border-t border-dashed border-slate-200">
                                <span>Suma Ida + Vuelta (por boleto):</span>
                                <span>S/. <?php echo number_format($precio_x_persona * 0.5, 2); ?> + S/. <?php echo number_format($precio_x_persona * 0.5, 2); ?> = S/. <?php echo number_format($precio_x_persona, 2); ?></span>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-between">
                                <span>Pasaje Solo Ida (c/u):</span>
                                <span class="font-semibold">S/. <?php echo number_format($precio_x_persona, 2); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between font-medium pt-1">
                            <span>Cantidad de Boletos:</span>
                            <span class="font-bold text-slate-800"><?php echo $pasajeros_res; ?> boleto<?php echo $pasajeros_res > 1 ? 's' : ''; ?></span>
                        </div>
                    </div>

                    <!-- Total Pagado -->
                    <div class="pt-4 border-t-2 border-[#0070F3] flex justify-between items-center">
                        <span class="text-slate-800 font-extrabold text-base">TOTAL PAGADO:</span>
                        <span class="text-3xl font-black text-[#0070F3]">S/. <?php echo number_format($reserva['precio_total'], 2); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-8 flex gap-4 items-start">
                    <i class="fa-solid fa-circle-info text-blue-500 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-blue-900 mb-1">Guarda este código</h4>
                        <p class="text-blue-800 text-sm">Necesitarás tu código PNR para realizar el Check-in online 24 horas antes de tu vuelo.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="?action=panel" class="btn btn--primary bg-[#0070F3] hover:bg-[#0051CC] text-white font-bold py-3 px-8 rounded-xl transition-colors text-center shadow-lg">
                            Ver Mis Viajes
                        </a>
                    <?php endif; ?>
                    <a href="?action=home" class="btn border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-3 px-8 rounded-xl transition-colors text-center">
                        Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
