<main class="checkout-page">
    <div class="checkout-container">
        
        <div class="checkout-header">
            <h1 class="checkout-title">Completa tu Reserva</h1>
            <p class="checkout-subtitle">Estás a un paso de confirmar tu vuelo</p>
        </div>

        <div class="summary-card">
            <div class="summary-header">
                <h2 class="summary-header-title"><i class="fa-solid fa-plane-departure text-[#0070F3]" style="margin-right: 0.5rem;"></i> Resumen del Vuelo</h2>
                <?php if(($tipo_viaje ?? 'solo_ida') === 'ida_vuelta'): ?>
                    <span class="badge-roundtrip">Vuelo Redondo (Ida y Vuelta)</span>
                <?php else: ?>
                    <span class="badge-oneway">Solo Ida</span>
                <?php endif; ?>
            </div>
            <div class="summary-content">
                <?php 
                    if (!function_exists('renderCheckoutSlice')) {
                        function renderCheckoutSlice($slice, $title) {
                ?>
                    <div class="flight-overview" style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #f3f4f6;">
                        <h4 style="font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; margin-bottom:0.5rem; letter-spacing:0.05em; grid-column: 1 / -1;"><?php echo $title; ?></h4>
                        <div class="airline-group" style="align-items: flex-start;">
                            <div class="airline-logo">
                                <i class="fa-solid fa-plane"></i>
                            </div>
                            <div>
                                <h4 class="airline-name"><?php echo htmlspecialchars($slice['airline']); ?></h4>
                                <p class="flight-number">Vuelo <?php echo htmlspecialchars($slice['flight_number']); ?></p>
                            </div>
                        </div>
                        
                        <div class="time-overview" style="justify-content: flex-end;">
                            <div style="text-align:right;">
                                <p class="time-value"><?php echo htmlspecialchars($slice['departure_time']); ?></p>
                                <p class="time-airport" title="<?php echo htmlspecialchars($slice['departure_airport_name'] ?? ''); ?>"><?php echo htmlspecialchars($slice['departure_airport']); ?></p>
                                <p style="font-size:0.75rem; color:#4b5563; font-weight:600; margin-top:0.25rem;"><?php echo htmlspecialchars($slice['departure_city'] ?? ''); ?></p>
                                <p class="time-date" style="font-size:0.75rem; color:#6b7280; font-weight: 500;"><?php echo htmlspecialchars($slice['departure_date'] ?? ''); ?></p>
                            </div>
                            <div class="time-overview-arrow" style="margin: 0 1rem;">
                                <i class="fa-solid fa-arrow-right text-gray-400"></i>
                            </div>
                            <div>
                                <p class="time-value"><?php echo htmlspecialchars($slice['arrival_time']); ?></p>
                                <p class="time-airport" title="<?php echo htmlspecialchars($slice['arrival_airport_name'] ?? ''); ?>"><?php echo htmlspecialchars($slice['arrival_airport']); ?></p>
                                <p style="font-size:0.75rem; color:#4b5563; font-weight:600; margin-top:0.25rem;"><?php echo htmlspecialchars($slice['arrival_city'] ?? ''); ?></p>
                                <p class="time-date" style="font-size:0.75rem; color:#6b7280; font-weight: 500;"><?php echo htmlspecialchars($slice['arrival_date'] ?? ''); ?></p>
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

                <div class="summary-footer" style="display: flex; flex-direction: column; gap: 0.75rem; background-color: rgba(10,22,40,0.7); padding: 1.5rem; border-radius: 1rem; border: 1px solid rgba(197,168,128,0.2); margin-top: 1rem;">
                    <h3 style="font-size: 0.875rem; font-weight: 700; color: rgba(197,168,128,0.85); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                        <i class="fa-solid fa-calculator text-brand-gold mr-2"></i> Desglose de Precios
                    </h3>

                    <?php if ($is_round_trip): ?>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: rgba(255,255,255,0.8);">
                            <span><i class="fa-solid fa-plane-departure mr-1" style="color: #C5A880;"></i> Pasaje Vuelo de Ida (c/u):</span>
                            <span style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_ida, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: rgba(255,255,255,0.8);">
                            <span><i class="fa-solid fa-plane-arrival mr-1" style="color: #C5A880;"></i> Pasaje Vuelo de Vuelta (c/u):</span>
                            <span style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_vuelta, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #ffffff; font-weight: 600; padding-top: 0.35rem; border-top: 1px dashed rgba(197,168,128,0.3);">
                            <span>Suma (Ida + Vuelta por persona):</span>
                            <span style="color: #C5A880; font-weight: 700;"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_ida, 2); ?> + <?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_vuelta, 2); ?> = <?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_por_pasajero, 2); ?></span>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: rgba(255,255,255,0.8);">
                            <span><i class="fa-solid fa-plane-departure mr-1" style="color: #C5A880;"></i> Pasaje Solo Ida (c/u):</span>
                            <span style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_por_pasajero, 2); ?></span>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-top: 1px solid rgba(197,168,128,0.2);">
                        <span style="font-size: 0.875rem; font-weight: 600; color: rgba(255,255,255,0.8);">
                            <i class="fa-solid fa-users mr-1" style="color: #C5A880;"></i> Cantidad de Boletos:
                        </span>
                        <span style="font-weight: 700; color: #C5A880; font-size: 0.95rem;">
                            <?php echo $num_pasajeros; ?> boleto<?php echo $num_pasajeros > 1 ? 's' : ''; ?>
                            <?php if ($num_pasajeros > 1): ?>
                                (x <?php echo htmlspecialchars($currency); ?> <?php echo number_format($precio_por_pasajero, 2); ?>)
                            <?php endif; ?>
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 2px solid rgba(197,168,128,0.5); padding-top: 0.85rem; margin-top: 0.25rem;">
                        <span style="font-size: 1.125rem; font-weight: 800; color: #ffffff;">TOTAL A PAGAR:</span>
                        <span class="summary-total" style="font-size: 1.65rem; font-weight: 900; color: #C5A880;"><?php echo htmlspecialchars($currency); ?> <?php echo number_format($total_pagar, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="guest-notice" style="background-color: rgba(10,22,40,0.8); border: 1px solid rgba(34,197,94,0.3); padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h3 style="font-weight: 600; color: #4ade80; font-size: 0.875rem;"><i class="fa-solid fa-circle-info" style="margin-right: 0.5rem;"></i> ¿Ya tienes una cuenta?</h3>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.875rem; margin-top: 0.25rem;">Estás comprando como invitado. Inicia sesión si deseas autocompletar tus datos y guardar tu vuelo.</p>
            </div>
            <a href="index.php?action=home&login=required" style="background-color: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; text-decoration: none; white-space: nowrap; margin-left: 1rem;">Iniciar Sesión</a>
        </div>
        <?php endif; ?>

        <div class="passenger-card">
            <div class="passenger-header">
                <h2 class="passenger-header-title"><i class="fa-solid fa-users text-gray-400" style="margin-right: 0.5rem;"></i> Datos de los Pasajeros (<?php echo htmlspecialchars($pasajeros); ?> boleto<?php echo (int)$pasajeros > 1 ? 's' : ''; ?>)</h2>
            </div>
            <div class="passenger-content">
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="confirmarReserva">
                    <input type="hidden" name="flight_id" value="<?php echo htmlspecialchars($vuelo['id']); ?>">
                    <input type="hidden" name="pasajeros" value="<?php echo htmlspecialchars($pasajeros); ?>">
                    <input type="hidden" name="tipo_viaje" value="<?php echo htmlspecialchars($tipo_viaje ?? 'solo_ida'); ?>">
                    <input type="hidden" name="fecha_retorno" value="<?php echo htmlspecialchars($fecha_retorno ?? ''); ?>">
                    
                    <div class="space-y-4 mb-6">
                        <div class="form-group">
                            <label class="form-label">Pasajero 1 (Titular de la Reserva)</label>
                            <input type="text" name="nombre" id="pasajero_nombre_0" required placeholder="Ej: Ana María García" class="form-input" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">
                            <input type="hidden" name="pasajero_nombre_0" value="" id="hidden_p0">
                        </div>

                        <?php for ($p = 1; $p < (int)$pasajeros; $p++): ?>
                            <div class="form-group pt-4" style="border-top: 1px solid rgba(197,168,128,0.15);">
                                <label class="form-label">Pasajero <?php echo ($p + 1); ?></label>
                                <input type="text" name="pasajero_nombre_<?php echo $p; ?>" required placeholder="Nombre completo del Pasajero <?php echo ($p + 1); ?>" class="form-input">
                            </div>
                        <?php endfor; ?>

                        <div class="form-group pt-4 border-t border-gray-100">
                            <label class="form-label">Correo electrónico de contacto (para enviar comprobantes)</label>
                            <input type="email" name="email" required placeholder="tu@email.com" class="form-input" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                        </div>
                    </div>

                    <div class="payment-section mt-8 pt-6 border-t border-brand-gold/20">
                        <h3 class="text-lg font-bold mb-4" style="color: #C5A880;"><i class="fa-regular fa-credit-card mr-2" style="color: #C5A880;"></i> Método de Pago</h3>
                        
                        <div style="background-color: rgba(10,22,40,0.7); padding: 1.5rem; border-radius: 1rem; border: 1px solid rgba(197,168,128,0.2);">
                            <div class="mb-4">
                                <label class="form-label" style="color: rgba(197,168,128,0.8);">Número de Tarjeta</label>
                                <div class="relative">
                                    <input type="text" placeholder="0000 0000 0000 0000" class="form-input pl-10" required maxlength="19">
                                    <i class="fa-brands fa-cc-visa absolute left-3 top-1/2 transform -translate-y-1/2 text-brand-gold text-xl"></i>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label" style="color: rgba(197,168,128,0.8);">Vencimiento (MM/AA)</label>
                                    <input type="text" placeholder="12/28" class="form-input" required maxlength="5">
                                </div>
                                <div>
                                    <label class="form-label" style="color: rgba(197,168,128,0.8);">CVC</label>
                                    <input type="password" placeholder="123" class="form-input" required maxlength="4">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="form-label" style="color: rgba(197,168,128,0.8);">Nombre en la tarjeta</label>
                                <input type="text" placeholder="Como aparece en la tarjeta" class="form-input" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary btn--large btn-checkout">
                        <i class="fa-solid fa-lock"></i> Confirmar y Pagar <?php echo htmlspecialchars($vuelo['currency']); ?> <?php echo htmlspecialchars(number_format($vuelo['price'], 2)); ?>
                    </button>
                    <p class="security-notice"><i class="fa-solid fa-shield-halved"></i> Pago 100% seguro y encriptado</p>
                </form>
            </div>
        </div>

    </div>
</main>
