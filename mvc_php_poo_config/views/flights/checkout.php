<main class="checkout-page font-sans text-white bg-[#0A1628] min-h-screen py-12">
    <div class="checkout-container max-w-4xl mx-auto px-4">
        
        <div class="checkout-header text-center mb-10 space-y-2">
            <span class="px-4 py-1.5 bg-[#C5A880]/10 text-[#C5A880] border border-[#C5A880]/30 text-xs font-light tracking-[0.2em] uppercase rounded-full inline-block mb-1">
                Finalizar Compra
            </span>
            <h1 class="text-3xl md:text-4xl font-light tracking-wide text-white">Completa tu Reserva</h1>
            <p class="text-slate-300 text-xs md:text-sm font-light tracking-wide">Estás a un paso de confirmar tu vuelo con NovAirlines</p>
        </div>

        <div class="summary-card bg-[#132238]/70 border border-[#C5A880]/20 rounded-2xl shadow-2xl backdrop-blur-md mb-8 overflow-hidden">
            <div class="summary-header bg-[#0A1628]/60 p-5 border-b border-[#C5A880]/15 flex justify-between items-center">
                <h2 class="text-base font-light text-white tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-plane-departure text-[#C5A880]"></i> Resumen del Vuelo
                </h2>
                <?php if(($tipo_viaje ?? 'solo_ida') === 'ida_vuelta'): ?>
                    <span class="px-3 py-1 bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/30 text-[10px] font-light tracking-widest uppercase rounded-full">Vuelo Redondo (Ida y Vuelta)</span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-white/10 text-slate-300 text-[10px] font-light tracking-widest uppercase rounded-full">Solo Ida</span>
                <?php endif; ?>
            </div>
            <div class="summary-content p-6 space-y-6">
                <?php 
                    if (!function_exists('renderCheckoutSlice')) {
                        function renderCheckoutSlice($slice, $title) {
                ?>
                    <div class="flight-overview pb-4 border-b border-[#C5A880]/10 space-y-3">
                        <h4 class="text-xs font-light text-[#C5A880] uppercase tracking-widest mb-2"><?php echo $title; ?></h4>
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#0A1628] border border-[#C5A880]/20 flex items-center justify-center text-[#C5A880]">
                                    <i class="fa-solid fa-plane"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-light text-white tracking-wide"><?php echo htmlspecialchars($slice['airline']); ?></h4>
                                    <p class="text-xs text-slate-400 font-light">Vuelo <?php echo htmlspecialchars($slice['flight_number']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6 text-right">
                                <div>
                                    <p class="text-lg font-light text-white tracking-wider"><?php echo htmlspecialchars($slice['departure_time']); ?></p>
                                    <p class="text-xs text-[#C5A880] font-light" title="<?php echo htmlspecialchars($slice['departure_airport_name'] ?? ''); ?>"><?php echo htmlspecialchars($slice['departure_airport']); ?></p>
                                    <p class="text-[11px] text-slate-300 font-light mt-0.5"><?php echo htmlspecialchars($slice['departure_city'] ?? ''); ?></p>
                                    <p class="text-[10px] text-slate-400 font-light"><?php echo htmlspecialchars($slice['departure_date'] ?? ''); ?></p>
                                </div>
                                <div class="text-[#C5A880]/60 px-2">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-lg font-light text-white tracking-wider"><?php echo htmlspecialchars($slice['arrival_time']); ?></p>
                                    <p class="text-xs text-[#C5A880] font-light" title="<?php echo htmlspecialchars($slice['arrival_airport_name'] ?? ''); ?>"><?php echo htmlspecialchars($slice['arrival_airport']); ?></p>
                                    <p class="text-[11px] text-slate-300 font-light mt-0.5"><?php echo htmlspecialchars($slice['arrival_city'] ?? ''); ?></p>
                                    <p class="text-[10px] text-slate-400 font-light"><?php echo htmlspecialchars($slice['arrival_date'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                        }
                    }
                ?>

                <?php 
                    if (!empty($vuelo['is_round_trip'])) {
                        renderCheckoutSlice($vuelo['outbound'], '🛫 Vuelo de Ida');
                        renderCheckoutSlice($vuelo['inbound'], '🛬 Vuelo de Vuelta');
                    } else {
                        renderCheckoutSlice($vuelo['outbound'] ?? $vuelo, '🛫 Vuelo de Ida');
                    }
                ?>
                
                <?php
                    $currency = $vuelo['currency'] ?? 'S/.';
                    $num_pasajeros = max(1, (int)$pasajeros);
                    $total_pagar = (float)$vuelo['price'];
                    $precio_por_pasajero = $total_pagar / $num_pasajeros;
                    $is_round_trip = !empty($vuelo['is_round_trip']) || (($tipo_viaje ?? 'solo_ida') === 'ida_vuelta');

                    if ($is_round_trip) {
                        $precio_ida = round($precio_por_pasajero * 0.5, 2);
                        $precio_vuelta = round($precio_por_pasajero - $precio_ida, 2);
                    }
                ?>

                <div class="bg-[#0A1628]/80 p-5 rounded-xl border border-[#C5A880]/20 space-y-3 font-light">
                    <h3 class="text-xs font-light text-[#C5A880] uppercase tracking-widest flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-calculator text-[#C5A880]"></i> Desglose de Precios
                    </h3>

                    <?php if ($is_round_trip): ?>
                        <div class="flex justify-between text-xs text-slate-300">
                            <span><i class="fa-solid fa-plane-departure mr-1 text-[#C5A880]"></i> Pasaje Vuelo de Ida (c/u):</span>
                            <span class="font-light text-white"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_ida, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-300">
                            <span><i class="fa-solid fa-plane-arrival mr-1 text-[#C5A880]"></i> Pasaje Vuelo de Vuelta (c/u):</span>
                            <span class="font-light text-white"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_vuelta, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-200 pt-2 border-t border-dashed border-[#C5A880]/20">
                            <span>Suma (Ida + Vuelta por persona):</span>
                            <span class="text-[#C5A880] font-light"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_ida, 2); ?> + <?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_vuelta, 2); ?> = <?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_por_pasajero, 2); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="flex justify-between text-xs text-slate-300">
                            <span><i class="fa-solid fa-plane-departure mr-1 text-[#C5A880]"></i> Pasaje Solo Ida (c/u):</span>
                            <span class="font-light text-white"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_por_pasajero, 2); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-center pt-2 border-t border-[#C5A880]/15 text-xs">
                        <span class="text-slate-300">
                            <i class="fa-solid fa-users mr-1 text-[#C5A880]"></i> Cantidad de Boletos:
                        </span>
                        <span class="text-[#C5A880] font-light">
                            <?php echo $num_pasajeros; ?> boleto<?php echo $num_pasajeros > 1 ? 's' : ''; ?>
                            <?php if ($num_pasajeros > 1): ?>
                                (x <?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_por_pasajero, 2); ?>)
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="flex justify-between items-center border-t border-[#C5A880]/30 pt-4 mt-2">
                        <span class="text-sm font-light text-white uppercase tracking-widest">TOTAL A PAGAR:</span>
                        <span class="text-2xl font-light text-[#C5A880] tracking-wider"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($total_pagar, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="bg-[#0070F3]/15 border border-[#0070F3]/30 p-4 rounded-2xl mb-8 flex items-center justify-between backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#0070F3] text-white flex items-center justify-center text-sm font-light">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <h3 class="font-light text-white text-sm">Sesión activa: <?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                    <p class="text-slate-300 text-xs font-light">Hemos autocompletado tus datos en el Pasajero 1 (Titular). Tus boletos se guardarán en tu panel.</p>
                </div>
            </div>
            <span class="bg-[#0070F3]/20 text-[#60a5fa] text-[10px] font-light px-3 py-1 rounded-full border border-[#0070F3]/30 uppercase tracking-wider">Cliente Registrado</span>
        </div>
        <?php else: ?>
        <div class="bg-[#0A1628]/80 border border-emerald-500/30 p-4 rounded-2xl mb-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <h3 class="font-light text-emerald-400 text-sm">Comprando como Invitado</h3>
                    <p class="text-slate-300 text-xs font-light">No necesitas una cuenta para comprar. Ingresa los datos completos de los pasajeros para emitir los boletos.</p>
                </div>
            </div>
            <a href="index.php?action=home&login=required" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-xs font-light tracking-wide transition-colors">Iniciar Sesión</a>
        </div>
        <?php endif; ?>

        <div class="passenger-card bg-[#132238]/70 border border-[#C5A880]/20 rounded-2xl shadow-2xl backdrop-blur-md overflow-hidden">
            <div class="passenger-header bg-[#0A1628]/60 p-5 border-b border-[#C5A880]/15">
                <h2 class="text-base font-light text-white tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-[#C5A880]"></i> 
                    Datos de los Pasajeros (<?php echo (int)$pasajeros; ?> boleto<?php echo (int)$pasajeros > 1 ? 's' : ''; ?>)
                </h2>
            </div>
            <div class="passenger-content p-6">
                <form action="index.php" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="confirmarReserva">
                    <input type="hidden" name="flight_id" value="<?php echo htmlspecialchars($vuelo['id']); ?>">
                    <input type="hidden" name="pasajeros" value="<?php echo htmlspecialchars($pasajeros); ?>">
                    <input type="hidden" name="tipo_viaje" value="<?php echo htmlspecialchars($tipo_viaje ?? 'solo_ida'); ?>">
                    <input type="hidden" name="fecha_retorno" value="<?php echo htmlspecialchars($fecha_retorno ?? ''); ?>">
                    
                    <div class="space-y-4">
                        <?php 
                        $num_pax = max(1, (int)$pasajeros);
                        for ($p = 0; $p < $num_pax; $p++): 
                            $is_first = ($p === 0);
                            $default_nombre = '';
                            $default_apellido = '';
                            
                            if ($is_first && isset($_SESSION['user_name'])) {
                                $partes_user = explode(' ', trim($_SESSION['user_name']), 2);
                                $default_nombre = $partes_user[0] ?? '';
                                $default_apellido = $partes_user[1] ?? '';
                            }
                        ?>
                            <div class="bg-[#0A1628]/60 border border-[#C5A880]/20 rounded-2xl p-5 space-y-4 font-light">
                                <div class="flex items-center justify-between pb-3 border-b border-[#C5A880]/15">
                                    <h4 class="text-xs font-light text-[#C5A880] uppercase tracking-widest flex items-center gap-2">
                                        <i class="fa-solid fa-user-tag text-[#C5A880]"></i> Pasajero <?php echo ($p + 1); ?> <?php echo $is_first ? '<span class="text-[10px] text-[#60a5fa] font-light">(Titular)</span>' : ''; ?>
                                    </h4>
                                    <span class="text-[10px] bg-[#C5A880]/10 text-[#C5A880] border border-[#C5A880]/20 px-2.5 py-0.5 rounded-full font-light">
                                        Boleto #<?php echo ($p + 1); ?>
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-light text-slate-300 uppercase tracking-wider mb-1">Nombre(s) *</label>
                                        <input type="text" name="pasajero_nombre_<?php echo $p; ?>" required placeholder="Ej: Ana María" 
                                               class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880] transition-colors" value="<?php echo htmlspecialchars($default_nombre); ?>">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-light text-slate-300 uppercase tracking-wider mb-1">Apellido(s) *</label>
                                        <input type="text" name="pasajero_apellido_<?php echo $p; ?>" required placeholder="Ej: García López" 
                                               class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880] transition-colors" value="<?php echo htmlspecialchars($default_apellido); ?>">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-light text-slate-300 uppercase tracking-wider mb-1">Documento *</label>
                                        <div class="flex gap-2">
                                            <select name="pasajero_tipo_doc_<?php echo $p; ?>" class="bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none">
                                                <option value="DNI">DNI</option>
                                                <option value="Pasaporte">PAS</option>
                                            </select>
                                            <input type="text" name="pasajero_doc_<?php echo $p; ?>" required placeholder="72819203" 
                                                   class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880] transition-colors">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>

                        <div class="pt-4 border-t border-[#C5A880]/15">
                            <label class="block text-xs font-light text-[#C5A880] uppercase tracking-wider mb-1.5">Correo de contacto para comprobantes *</label>
                            <input type="email" name="email" required placeholder="tu@email.com" 
                                   class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-4 py-3 text-xs font-light text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880] transition-colors" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-[#C5A880]/20 space-y-4">
                        <h3 class="text-sm font-light text-[#C5A880] uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-regular fa-credit-card text-[#C5A880]"></i> Método de Pago Seguro
                        </h3>
                        
                        <div class="bg-[#0A1628]/80 p-5 rounded-2xl border border-[#C5A880]/20 space-y-4 font-light">
                            <div>
                                <label class="block text-[11px] font-light text-slate-300 uppercase tracking-wider mb-1">Número de Tarjeta</label>
                                <div class="relative">
                                    <input type="text" placeholder="0000 0000 0000 0000" class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880]" required maxlength="19">
                                    <i class="fa-brands fa-cc-visa absolute left-3 top-1/2 -translate-y-1/2 text-[#C5A880] text-lg"></i>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-light text-slate-300 uppercase tracking-wider mb-1">Vencimiento (MM/AA)</label>
                                    <input type="text" placeholder="12/28" class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880]" required maxlength="5">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-light text-slate-300 uppercase tracking-wider mb-1">CVC</label>
                                    <input type="password" placeholder="123" class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880]" required maxlength="4">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-light text-slate-300 uppercase tracking-wider mb-1">Titular de la Tarjeta</label>
                                <input type="text" placeholder="Nombre como figura en el plástico" class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880]" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] font-medium text-xs uppercase tracking-widest py-4 rounded-xl transition-all duration-300 shadow-xl hover:shadow-[#C5A880]/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock text-sm"></i> Confirmar y Pagar <?php echo htmlspecialchars($vuelo['currency']); ?> <?php echo htmlspecialchars(number_format($vuelo['price'], 2)); ?>
                    </button>
                    <p class="text-center text-[11px] text-slate-400 font-light tracking-wide"><i class="fa-solid fa-shield-halved text-[#C5A880] mr-1"></i> Transacción 100% segura y encriptada bajo protocolo TLS</p>
                </form>
            </div>
        </div>

    </div>
</main>
