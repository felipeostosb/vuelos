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
                // Mostrar de más reciente a más antigua
                for ($i = count($misReservas) - 1; $i >= 0; $i--) {
                    $reserva = $misReservas[$i];
                    $vuelo   = $reserva['vuelo'];
                    $pnr     = $reserva['pnr'];
                    $isCheckedIn = ($reserva['estado'] === 'Checked-in');
                    $es_ida_vuelta = (($reserva['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta');
                    $vuelo_vuelta  = $reserva['vuelo_vuelta'] ?? null;

                    // Nombres de pasajeros
                    if (!empty($reserva['pasajeros'])) {
                        $nombres_pax = array_map(function($p) {
                            return htmlspecialchars(trim($p['nombre'] . ' ' . $p['apellido']));
                        }, $reserva['pasajeros']);
                        $nombres_str = implode(', ', $nombres_pax);
                    } else {
                        $nombres_str = htmlspecialchars($reserva['pasajero_nombre']);
                    }

                    // ── Función interna para renderizar una tarjeta de trayecto
                    $renderTarjeta = function($v_data, $leg_label, $leg_icon, $leg_color, $precio_tramo, $currency, $is_vuelta_leg = false) use ($pnr, $isCheckedIn, $reserva, $nombres_str) {
                        $airline        = htmlspecialchars($v_data['airline'] ?? 'Aerolínea');
                        $flight_number  = htmlspecialchars($v_data['flight_number'] ?? 'N/A');
                        $dep_time       = htmlspecialchars($v_data['departure_time'] ?? '--:--');
                        $dep_airport    = htmlspecialchars($v_data['departure_airport'] ?? '---');
                        $dep_city       = htmlspecialchars($v_data['departure_city'] ?? '');
                        $dep_date       = htmlspecialchars($v_data['departure_date'] ?? ($v_data['date'] ?? ''));
                        $arr_time       = htmlspecialchars($v_data['arrival_time'] ?? '--:--');
                        $arr_airport    = htmlspecialchars($v_data['arrival_airport'] ?? '---');
                        $arr_city       = htmlspecialchars($v_data['arrival_city'] ?? '');
                        $pax_count      = (int)$reserva['pasajeros_count'];
                        $precio_fmt     = number_format((float)$precio_tramo, 2);
                        ?>
                        <div class="ticket-card" style="border-left: 4px solid <?php echo $leg_color; ?>;">
                            <!-- Etiqueta del trayecto -->
                            <div style="display:flex; align-items:center; gap:0.5rem; padding: 0.65rem 1.25rem; background: <?php echo $leg_color; ?>18; border-bottom: 1px solid <?php echo $leg_color; ?>30;">
                                <i class="fa-solid <?php echo $leg_icon; ?>" style="color:<?php echo $leg_color; ?>; font-size:0.85rem;"></i>
                                <span style="font-size:0.78rem; font-weight:800; color:<?php echo $leg_color; ?>; text-transform:uppercase; letter-spacing:0.06em;"><?php echo $leg_label; ?></span>
                                <span style="margin-left:auto; font-size:0.7rem; font-weight:600; color:#64748b; background:#f1f5f9; padding:2px 8px; border-radius:99px;">PNR: <strong style="color:#0070F3;"><?php echo htmlspecialchars($pnr); ?></strong></span>
                            </div>

                            <div class="flex flex-col md:flex-row" style="width:100%;">
                                <!-- Lado izquierdo: Información del vuelo -->
                                <div class="ticket-main">
                                    <div class="ticket-cutout-top"></div>
                                    <div class="ticket-cutout-bottom"></div>

                                    <div class="ticket-header">
                                        <div class="ticket-airline-info">
                                            <div class="ticket-airline-logo">
                                                <i class="fa-solid fa-plane"></i>
                                            </div>
                                            <div>
                                                <p class="ticket-airline-name"><?php echo $airline; ?></p>
                                                <p class="ticket-flight-number">Vuelo <?php echo $flight_number; ?></p>
                                            </div>
                                        </div>
                                        <div class="ticket-passenger" style="text-align:right;">
                                            <p class="ticket-passenger-label">Pasajero(s) (<?php echo $pax_count; ?> boleto<?php echo $pax_count > 1 ? 's' : ''; ?>)</p>
                                            <p class="ticket-passenger-name"><?php echo $nombres_str; ?></p>
                                        </div>
                                    </div>

                                    <div class="ticket-flight-times">
                                        <div class="ticket-time-block">
                                            <p class="ticket-time"><?php echo $dep_time; ?></p>
                                            <p class="ticket-airport"><?php echo $dep_airport; ?></p>
                                            <?php if ($dep_city): ?><p style="font-size:0.72rem;color:#6b7280;margin-top:2px;"><?php echo $dep_city; ?></p><?php endif; ?>
                                            <?php if ($dep_date): ?><p style="font-size:0.72rem;color:#6b7280;"><?php echo $dep_date; ?></p><?php endif; ?>
                                        </div>
                                        <div class="ticket-duration-line">
                                            <i class="fa-solid fa-plane ticket-plane-icon" style="color:<?php echo $leg_color; ?>;"></i>
                                            <div class="ticket-line"></div>
                                        </div>
                                        <div class="ticket-time-block">
                                            <p class="ticket-time"><?php echo $arr_time; ?></p>
                                            <p class="ticket-airport"><?php echo $arr_airport; ?></p>
                                            <?php if ($arr_city): ?><p style="font-size:0.72rem;color:#6b7280;margin-top:2px;"><?php echo $arr_city; ?></p><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lado derecho: PNR, precio y acción -->
                                <div class="ticket-side">
                                    <div style="margin-bottom:0.75rem;">
                                        <span style="background:<?php echo $leg_color; ?>15; color:<?php echo $leg_color; ?>; font-size:0.75rem; font-weight:700; padding:3px 12px; border-radius:99px; border:1px solid <?php echo $leg_color; ?>40;">
                                            <i class="fa-solid fa-ticket mr-1"></i> <?php echo $pax_count; ?> Boleto<?php echo $pax_count > 1 ? 's' : ''; ?>
                                        </span>
                                    </div>

                                    <p class="pnr-label">Código de Reserva</p>
                                    <p class="pnr-value"><?php echo htmlspecialchars($pnr); ?></p>

                                    <div style="margin-top:0.5rem; margin-bottom:1rem;">
                                        <p class="text-xs text-slate-500 font-medium">Precio <?php echo $leg_label; ?></p>
                                        <p class="text-lg font-extrabold text-[#0A192F]"><?php echo htmlspecialchars($currency); ?> <?php echo $precio_fmt; ?></p>
                                    </div>

                                    <?php if ($isCheckedIn): ?>
                                        <div class="boarding-pass-ready">
                                            <i class="fa-solid fa-qrcode"></i> Boarding Pass Listo
                                        </div>
                                    <?php else: ?>
                                        <form action="index.php" method="POST" style="width:100%;">
                                            <input type="hidden" name="action" value="procesarCheckin">
                                            <input type="hidden" name="pnr" value="<?php echo htmlspecialchars($pnr); ?>">
                                            <button type="submit" class="btn btn--primary btn-checkin" style="width:100%;">
                                                Hacer Check-in
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    };

                    if ($es_ida_vuelta) {
                        // ──── TARJETA 1: VUELO DE IDA ────
                        $precio_ida = isset($reserva['precio_total']) ? round((float)$reserva['precio_total'] * 0.5, 2) : 0;
                        $currency = $vuelo_vuelta['currency'] ?? 'USD';
                        $renderTarjeta($vuelo, '🛫 Vuelo de Ida', 'fa-plane-departure', '#0070F3', $precio_ida, $currency, false);

                        // ──── TARJETA 2: VUELO DE VUELTA ────
                        if (!empty($vuelo_vuelta)) {
                            $precio_vuelta = round((float)$reserva['precio_total'] * 0.5, 2);
                            $renderTarjeta($vuelo_vuelta, '🛬 Vuelo de Vuelta', 'fa-plane-arrival', '#7c3aed', $precio_vuelta, $currency, true);
                        } else {
                            // Fallback: construir vuelta con datos invertidos del vuelo de ida
                            $vuelo_vuelta_fallback = [
                                'airline'           => $vuelo['airline'],
                                'flight_number'     => $vuelo['flight_number'],
                                'departure_airport' => $vuelo['arrival_airport'],
                                'departure_city'    => '',
                                'arrival_airport'   => $vuelo['departure_airport'],
                                'arrival_city'      => '',
                                'departure_time'    => '--:--',
                                'arrival_time'      => '--:--',
                            ];
                            $precio_vuelta = round((float)$reserva['precio_total'] * 0.5, 2);
                            $renderTarjeta($vuelo_vuelta_fallback, '🛬 Vuelo de Vuelta', 'fa-plane-arrival', '#7c3aed', $precio_vuelta, 'USD', true);
                        }
                    } else {
                        // ──── SOLO IDA: una sola tarjeta ────
                        $renderTarjeta($vuelo, '🛫 Vuelo de Ida', 'fa-plane-departure', '#0070F3', (float)$reserva['precio_total'], 'S/.', false);
                    }
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</main>
