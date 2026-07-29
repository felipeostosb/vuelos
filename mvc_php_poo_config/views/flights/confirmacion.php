<main class="py-16 bg-[#0A1628] min-h-[85vh] flex items-center justify-center font-sans text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto bg-[#132238]/90 border border-[#C5A880]/30 rounded-3xl shadow-2xl overflow-hidden backdrop-blur-md">
            <!-- Header Boutique -->
            <div class="bg-gradient-to-r from-[#48324F] via-[#9C694C] to-[#C5A880] p-8 text-center text-white relative">
                <div class="w-16 h-16 bg-[#0A1628]/40 border border-[#C5A880]/40 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm shadow-inner">
                    <i class="fa-solid fa-check text-2xl text-[#C5A880]"></i>
                </div>
                <span class="px-4 py-1 bg-[#0A1628]/50 text-[#C5A880] border border-[#C5A880]/30 text-[10px] font-light tracking-[0.2em] uppercase rounded-full inline-block mb-2">
                    Confirmación de Reserva
                </span>
                <h1 class="text-2xl md:text-3xl font-light tracking-wide mb-1">¡Reserva Exitosa!</h1>
                <p class="text-slate-200 text-xs font-light tracking-wide">Su boleto aeronáutico ha sido procesado con la excelencia de NovAirlines.</p>
            </div>

            <!-- Content -->
            <div class="p-8 space-y-6">
                <div class="text-center">
                    <p class="text-xs font-light text-[#C5A880] uppercase tracking-[0.2em] mb-2">Código de Reserva (PNR)</p>
                    <div class="inline-block bg-[#0A1628] border border-[#C5A880]/40 px-8 py-3 rounded-2xl shadow-inner">
                        <span class="text-3xl font-mono font-light text-[#C5A880] tracking-[0.25em]"><?php echo htmlspecialchars($_GET['pnr'] ?? 'XXXXXX'); ?></span>
                    </div>
                </div>

                <?php if (isset($reserva) && !empty($reserva)): ?>
                <!-- Detalle de Boletos y Vuelo -->
                <div class="bg-[#0A1628]/80 border border-[#C5A880]/20 rounded-2xl p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-[#C5A880]/15 mb-4">
                        <span class="text-xs font-light text-[#C5A880] uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-ticket text-[#C5A880]"></i> Boletos Emitidos
                        </span>
                        <span class="bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/30 text-xs font-light px-3 py-1 rounded-full">
                            <?php echo htmlspecialchars($reserva['pasajeros_count']); ?> boleto<?php echo $reserva['pasajeros_count'] > 1 ? 's' : ''; ?>
                        </span>
                    </div>

                    <?php if (isset($reserva['vuelo'])): $v = $reserva['vuelo']; ?>
                    <div class="grid grid-cols-2 gap-4 mb-4 text-xs font-light">
                        <div>
                            <p class="text-slate-400">Aerolínea / Vuelo</p>
                            <p class="font-light text-white text-sm tracking-wide"><?php echo htmlspecialchars($v['airline']); ?> (<?php echo htmlspecialchars($v['flight_number']); ?>)</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Ruta</p>
                            <p class="font-light text-white text-sm tracking-wide"><?php echo htmlspecialchars($v['departure_airport']); ?> ➔ <?php echo htmlspecialchars($v['arrival_airport']); ?></p>
                        </div>
                        <div>
                            <p class="text-slate-400">Horarios</p>
                            <p class="font-light text-white text-sm tracking-wide"><?php echo htmlspecialchars($v['departure_time']); ?> - <?php echo htmlspecialchars($v['arrival_time']); ?></p>
                        </div>
                        <div>
                            <p class="text-slate-400">Tipo de Viaje</p>
                            <p class="font-light text-white text-sm tracking-wide"><?php echo ($reserva['tipo_viaje'] === 'ida_vuelta') ? 'Ida y Vuelta' : 'Solo Ida'; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Pasajeros -->
                    <div class="pt-4 border-t border-[#C5A880]/15 mb-4">
                        <p class="text-xs text-[#C5A880] mb-3 font-light uppercase tracking-widest">Pasajero(s) Registrado(s):</p>
                        <div class="space-y-2">
                            <?php 
                            $hay_pax = false;
                            if (!empty($reserva['pasajeros'])) {
                                foreach ($reserva['pasajeros'] as $idx => $pas) {
                                    $nombre_p = trim(($pas['nombre'] ?? '') . ' ' . ($pas['apellido'] ?? ''));
                                    if (empty($nombre_p)) continue;
                                    $hay_pax = true;
                                    $doc_p = !empty($pas['numero_documento']) ? ' <span class="text-xs text-slate-400 font-light">(' . htmlspecialchars($pas['tipo_documento'] ?? 'DNI') . ': ' . htmlspecialchars($pas['numero_documento']) . ')</span>' : '';
                            ?>
                                    <div class="flex items-center justify-between bg-[#132238] border border-[#C5A880]/20 px-4 py-2.5 rounded-xl text-xs font-light text-white">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-user-check text-[#C5A880]"></i>
                                            <span><?php echo htmlspecialchars($nombre_p); ?><?php echo $doc_p; ?></span>
                                        </div>
                                        <span class="text-[10px] bg-[#0A1628] text-[#C5A880] border border-[#C5A880]/30 font-light px-2.5 py-0.5 rounded-md">Boleto #<?php echo ($idx + 1); ?></span>
                                    </div>
                            <?php 
                                }
                            }
                            if (!$hay_pax):
                                $nombre_p = !empty(trim($reserva['pasajero_nombre'] ?? '')) ? $reserva['pasajero_nombre'] : 'Pasajero Titular';
                            ?>
                                <div class="flex items-center gap-2 bg-[#132238] border border-[#C5A880]/20 px-4 py-2.5 rounded-xl text-xs font-light text-white">
                                    <i class="fa-solid fa-user-check text-[#C5A880]"></i>
                                    <span><?php echo htmlspecialchars($nombre_p); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Total Pagado -->
                    <div class="pt-4 border-t border-[#C5A880]/30 flex justify-between items-center">
                        <span class="text-slate-300 font-light text-xs uppercase tracking-widest">Monto Total Abonado:</span>
                        <span class="text-2xl font-light text-[#C5A880] tracking-wider">S/. <?php echo number_format($reserva['precio_total'], 2); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <div class="bg-[#C5A880]/10 border border-[#C5A880]/20 rounded-2xl p-4 flex gap-3 items-center text-xs font-light text-[#C5A880]">
                    <i class="fa-solid fa-circle-info text-base"></i>
                    <span>Guarde su código PNR para realizar su Check-in online 24 horas antes del despegue.</span>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-2">
                    <a href="index.php?action=generarBoleto&pnr=<?php echo urlencode($_GET['pnr'] ?? ''); ?>" 
                       class="bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] font-medium text-xs uppercase tracking-widest py-3.5 px-8 rounded-xl transition-all duration-300 text-center shadow-xl hover:shadow-[#C5A880]/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-pdf text-base"></i> Descargar Ticket (PDF)
                    </a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="?action=panel" class="bg-[#48324F] hover:bg-[#3b2841] text-[#C5A880] border border-[#C5A880]/40 font-light text-xs uppercase tracking-widest py-3.5 px-8 rounded-xl transition-all text-center shadow-lg flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plane"></i> Mis Viajes
                        </a>
                    <?php endif; ?>
                    <a href="?action=home" class="border border-[#C5A880]/40 hover:bg-[#C5A880]/10 text-[#C5A880] font-light text-xs uppercase tracking-widest py-3.5 px-8 rounded-xl transition-colors text-center flex items-center justify-center">
                        Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
