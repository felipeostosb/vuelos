<main class="panel-page">
    <div class="panel-container">
        
        <div class="panel-header">
            <div>
                <h1 class="panel-title">Mi Panel de Viajes</h1>
                <p class="panel-subtitle">Gestiona tus próximas aventuras</p>
            </div>
            <div class="user-avatar">
                <?php echo substr($_SESSION['user_name'], 0, 1); ?>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fa-solid fa-circle-check success-icon"></i>
                <div>
                    <p class="success-title">¡Reserva confirmada con éxito!</p>
                    <p class="success-text">Tu código de reserva (PNR) es: <strong class="success-pnr"><?php echo htmlspecialchars($_GET['pnr']); ?></strong></p>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="section-heading">Mis Próximos Vuelos</h2>

        <?php if (empty($misReservas)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fa-solid fa-plane-slash"></i>
                </div>
                <h3 class="empty-state-title">Aún no tienes reservas</h3>
                <p class="empty-state-text">Explora nuestros destinos y planifica tu próximo viaje.</p>
                <a href="?action=home" class="btn btn--primary btn--large">Buscar Vuelos</a>
            </div>
        <?php else: ?>
            <div class="reservations-list">
                <?php 
                // Invertimos el arreglo de forma manual usando un ciclo for (de atrás hacia adelante)
                // para que las reservas más recientes salgan primero.
                for ($i = count($misReservas) - 1; $i >= 0; $i--) { 
                    $reserva = $misReservas[$i];
                    $vuelo = $reserva['vuelo'];
                    $pnr = $reserva['pnr'];
                    $isCheckedIn = ($reserva['estado'] === 'Checked-in');
                ?>
                <div class="ticket-card">
                    <!-- Ticket Design -->
                    <div class="flex flex-col md:flex-row" style="width: 100%;">
                        <!-- Left Side: Flight Info -->
                        <div class="ticket-main">
                            <!-- Semi circles for ticket effect -->
                            <div class="ticket-cutout-top"></div>
                            <div class="ticket-cutout-bottom"></div>

                            <div class="ticket-header">
                                <div class="ticket-airline-info">
                                    <div class="ticket-airline-logo">
                                        <i class="fa-solid fa-plane"></i>
                                    </div>
                                    <div>
                                        <p class="ticket-airline-name"><?php echo htmlspecialchars($vuelo['airline']); ?></p>
                                        <p class="ticket-flight-number">Vuelo <?php echo htmlspecialchars($vuelo['flight_number']); ?></p>
                                    </div>
                                </div>
                                <div class="ticket-passenger">
                                    <p class="ticket-passenger-label">Pasajero</p>
                                    <p class="ticket-passenger-name"><?php echo htmlspecialchars($reserva['pasajero_nombre']); ?></p>
                                </div>
                            </div>

                            <div class="ticket-flight-times">
                                <div class="ticket-time-block">
                                    <p class="ticket-time"><?php echo htmlspecialchars($vuelo['departure_time']); ?></p>
                                    <p class="ticket-airport"><?php echo htmlspecialchars($vuelo['departure_airport']); ?></p>
                                </div>
                                <div class="ticket-duration-line">
                                    <?php if(($reserva['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta'): ?>
                                        <span class="ticket-roundtrip-badge">Ida y Vuelta</span>
                                    <?php endif; ?>
                                    
                                    <i class="fa-solid <?php echo ($reserva['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta' ? 'fa-arrow-right-arrow-left text-[#0070F3]' : 'fa-plane text-gray-300'; ?> ticket-plane-icon"></i>
                                    <div class="ticket-line"></div>
                                    
                                    <?php if(($reserva['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta' && !empty($reserva['fecha_retorno'])): ?>
                                        <span class="ticket-return-date">Regreso: <?php echo htmlspecialchars($reserva['fecha_retorno']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="ticket-time-block">
                                    <p class="ticket-time"><?php echo htmlspecialchars($vuelo['arrival_time']); ?></p>
                                    <p class="ticket-airport"><?php echo htmlspecialchars($vuelo['arrival_airport']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: PNR & Actions -->
                        <div class="ticket-side">
                            <p class="pnr-label">Código de Reserva</p>
                            <p class="pnr-value"><?php echo htmlspecialchars($pnr); ?></p>
                            
                            <?php if ($isCheckedIn): ?>
                                <div class="boarding-pass-ready">
                                    <i class="fa-solid fa-qrcode"></i> Boarding Pass Listo
                                </div>
                            <?php else: ?>
                                <form action="index.php" method="POST" style="width: 100%;">
                                    <input type="hidden" name="action" value="procesarCheckin">
                                    <input type="hidden" name="pnr" value="<?php echo htmlspecialchars($pnr); ?>">
                                    <button type="submit" class="btn btn--primary btn-checkin" style="width: 100%;">
                                        Hacer Check-in
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        <?php endif; ?>
    </div>
</main>
