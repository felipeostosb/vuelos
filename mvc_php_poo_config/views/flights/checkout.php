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
                <div class="flight-overview">
                    <div class="airline-group">
                        <div class="airline-logo">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                        <div>
                            <h4 class="airline-name"><?php echo htmlspecialchars($vuelo['airline']); ?></h4>
                            <p class="flight-number">Vuelo <?php echo htmlspecialchars($vuelo['flight_number']); ?></p>
                        </div>
                    </div>
                    
                    <div class="time-overview">
                        <div>
                            <p class="time-value"><?php echo htmlspecialchars($vuelo['departure_time']); ?></p>
                            <p class="time-airport"><?php echo htmlspecialchars($vuelo['departure_airport']); ?></p>
                        </div>
                        <div class="time-overview-arrow">
                            <i class="fa-solid <?php echo ($tipo_viaje ?? 'solo_ida') === 'ida_vuelta' ? 'fa-arrow-right-arrow-left' : 'fa-arrow-right'; ?>"></i>
                        </div>
                        <div>
                            <p class="time-value"><?php echo htmlspecialchars($vuelo['arrival_time']); ?></p>
                            <p class="time-airport"><?php echo htmlspecialchars($vuelo['arrival_airport']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="summary-footer">
                    <span class="summary-passengers">Pasajeros: <?php echo htmlspecialchars($pasajeros); ?></span>
                    <span class="summary-total">S/. <?php echo htmlspecialchars($vuelo['price'] * $pasajeros); ?></span>
                </div>
            </div>
        </div>

        <div class="passenger-card">
            <div class="passenger-header">
                <h2 class="passenger-header-title"><i class="fa-solid fa-user text-gray-400" style="margin-right: 0.5rem;"></i> Datos del Pasajero Principal</h2>
            </div>
            <div class="passenger-content">
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="confirmarReserva">
                    <input type="hidden" name="flight_id" value="<?php echo htmlspecialchars($vuelo['id']); ?>">
                    <input type="hidden" name="pasajeros" value="<?php echo htmlspecialchars($pasajeros); ?>">
                    <input type="hidden" name="tipo_viaje" value="<?php echo htmlspecialchars($tipo_viaje ?? 'solo_ida'); ?>">
                    <input type="hidden" name="fecha_retorno" value="<?php echo htmlspecialchars($fecha_retorno ?? ''); ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="nombre" required placeholder="Ej: Ana María García" class="form-input" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" required placeholder="tu@email.com" class="form-input" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                        </div>
                    </div>

                    <div class="payment-section mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-[#0A192F] mb-4"><i class="fa-regular fa-credit-card text-[#0070F3] mr-2"></i> Método de Pago</h3>
                        
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <div class="mb-4">
                                <label class="form-label">Número de Tarjeta</label>
                                <div class="relative">
                                    <input type="text" placeholder="0000 0000 0000 0000" class="form-input pl-10" required maxlength="19">
                                    <i class="fa-brands fa-cc-visa absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Vencimiento (MM/AA)</label>
                                    <input type="text" placeholder="12/28" class="form-input" required maxlength="5">
                                </div>
                                <div>
                                    <label class="form-label">CVC</label>
                                    <input type="password" placeholder="123" class="form-input" required maxlength="4">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="form-label">Nombre en la tarjeta</label>
                                <input type="text" placeholder="Como aparece en la tarjeta" class="form-input" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary btn--large btn-checkout">
                        <i class="fa-solid fa-lock"></i> Confirmar y Pagar S/. <?php echo htmlspecialchars($vuelo['price'] * $pasajeros); ?>
                    </button>
                    <p class="security-notice"><i class="fa-solid fa-shield-halved"></i> Pago 100% seguro y encriptado</p>
                </form>
            </div>
        </div>

    </div>
</main>
