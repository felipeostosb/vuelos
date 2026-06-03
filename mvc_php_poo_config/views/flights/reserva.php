<main class="reserva-page">
    
    <section class="route-header">
        <div class="route-container">
            <div class="route-info">
                <?php 
                    $origen = isset($_GET['origen']) ? htmlspecialchars($_GET['origen']) : 'Lima';
                    $destino = isset($_GET['destino']) ? htmlspecialchars($_GET['destino']) : 'Madrid';
                    $fecha = isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '25 Jul 2026';
                    $pasajeros = isset($_GET['pasajeros']) ? htmlspecialchars($_GET['pasajeros']) : '1';
                    $isRoundTrip = ($data['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta';
                ?>
                <span class="route-destinations">
                    <?php echo $origen; ?> 
                    <?php echo $isRoundTrip ? '<i class="fa-solid fa-arrow-right-arrow-left text-[#0070F3] mx-1"></i>' : '→'; ?> 
                    <?php echo $destino; ?>
                </span>
                <span class="route-separator">·</span>
                <span class="route-text"><?php echo $fecha; ?> <?php echo $isRoundTrip && !empty($data['fecha_retorno']) ? ' al ' . htmlspecialchars($data['fecha_retorno']) : ''; ?></span>
                <span class="route-separator">·</span>
                <span class="route-text"><?php echo $pasajeros; ?> pasajero(s) <?php echo $isRoundTrip ? '<span class="ml-2 bg-[#0070F3] text-white px-2 py-0.5 rounded-full text-xs">Ida y Vuelta</span>' : ''; ?></span>
            </div>
            <a href="?action=home" class="route-edit-btn">
                <i class="fa-solid fa-pen"></i> Editar búsqueda
            </a>
        </div>
    </section>

    <section class="ai-message-bar">
        <div class="ai-message-container">
            <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
                <i class="fa-solid fa-wand-magic-sparkles text-[#0070F3]"></i>
                <p>La IA entendió tu solicitud y encontró <span class="font-bold text-[#0070F3]"><?php echo count($vuelos_encontrados); ?> vuelos</span>. El mejor precio sale los martes.</p>
            <?php else: ?>
                <i class="fa-solid fa-plane text-[#0070F3]"></i>
                <p>Hemos encontrado <span class="font-bold text-[#0070F3]"><?php echo count($vuelos_encontrados); ?> vuelos</span> para tu ruta.</p>
            <?php endif; ?>
        </div>
    </section>

    <div class="content-grid">
        
        <aside class="filters-sidebar">
            <h2 class="filters-title">Filtros</h2>
            
            <form method="GET" action="index.php">
                <input type="hidden" name="action" value="reserva">
                
                <div class="filter-section">
                    <h3 class="filter-subtitle">Ruta de vuelo</h3>
                    <?php 
                        $origenSel = isset($_GET['origen']) ? $_GET['origen'] : '';
                        $destinoSel = isset($_GET['destino']) ? $_GET['destino'] : '';
                        $opciones = ['Lima', 'Cusco', 'Arequipa', 'Bogotá', 'Madrid', 'París'];
                    ?>
                    
                    <div class="filter-input-group">
                        <div>
                            <label class="filter-label">Origen</label>
                            <select name="origen" class="filter-select">
                                <option value="">Todos</option>
                                <?php 
                                    for ($i = 0; $i < count($opciones); $i++) {
                                        $op = $opciones[$i];
                                ?>
                                    <option value="<?php echo $op; ?>" <?php echo (stripos($origenSel, $op) !== false) ? 'selected' : ''; ?>><?php echo $op; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="filter-label">Destino</label>
                            <select name="destino" class="filter-select">
                                <option value="">Todos</option>
                                <?php 
                                    for ($i = 0; $i < count($opciones); $i++) {
                                        $op = $opciones[$i];
                                ?>
                                    <option value="<?php echo $op; ?>" <?php echo (stripos($destinoSel, $op) !== false) ? 'selected' : ''; ?>><?php echo $op; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="filter-label">Pasajeros</label>
                            <input type="number" name="pasajeros" min="1" value="<?php echo htmlspecialchars($pasajeros); ?>" class="filter-input-number">
                        </div>
                    </div>
                </div>

                <div class="filter-divider"></div>

                <div class="filter-section">
                    <h3 class="filter-subtitle">Precio máximo</h3>
                    <?php $currentPrice = isset($_GET['max_price']) ? $_GET['max_price'] : 4000; ?>
                    <input type="range" name="max_price" min="500" max="4000" value="<?php echo $currentPrice; ?>" 
                           class="range-slider" oninput="document.getElementById('precio-etiqueta').innerText = 'S/. ' + this.value">
                    
                    <div class="range-labels">
                        <span>S/. 500</span>
                        <span id="precio-etiqueta" class="bg-[#0070F3] text-white font-bold px-3 py-1 rounded-full">S/. <?php echo $currentPrice; ?></span>
                        <span>S/. 4,000</span>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-subtitle">Escalas</h3>
                    <?php $stops = isset($_GET['stops']) ? $_GET['stops'] : ['0', '1']; ?>
                    <div class="filter-input-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="stops[]" value="0" <?php echo in_array('0', $stops) ? 'checked' : ''; ?> class="checkbox-input">
                            <span class="checkbox-text">Directo</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="stops[]" value="1" <?php echo in_array('1', $stops) ? 'checked' : ''; ?> class="checkbox-input">
                            <span class="checkbox-text">1 escala</span>
                        </label>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-subtitle">Aerolíneas</h3>
                    <?php 
                        $airlines = isset($_GET['airlines']) ? $_GET['airlines'] : ['Copa Airlines', 'Avianca', 'LATAM Airlines', 'Iberia']; 
                        $available_airlines = ['Copa Airlines', 'Avianca', 'LATAM Airlines', 'Iberia'];
                    ?>
                    <div class="filter-input-group">
                        <?php 
                            for ($i = 0; $i < count($available_airlines); $i++) {
                                $airline = $available_airlines[$i];
                        ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="airlines[]" value="<?php echo $airline; ?>" <?php echo in_array($airline, $airlines) ? 'checked' : ''; ?> class="checkbox-input">
                            <span class="checkbox-text"><?php echo $airline; ?></span>
                        </label>
                        <?php } ?>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary" style="width: 100%;">
                    Aplicar Filtros
                </button>
            </form>
        </aside>
        
        <div class="results-area">
            
            <div class="sort-buttons">
                <button class="btn-sort btn-sort--active">Mejor precio</button>
                <button class="btn-sort btn-sort--inactive">Duración</button>
            </div>

            <div id="lista-vuelos" class="flight-list">
                <?php if (count($vuelos_encontrados) > 0): ?>
                    <?php 
                        for ($i = 0; $i < count($vuelos_encontrados); $i++) {
                            $vuelo = $vuelos_encontrados[$i];
                    ?>
                    <div class="flight-card">
                        
                        <?php if ($vuelo['best_price']): ?>
                        <div class="best-price-badge">
                            Mejor precio
                        </div>
                        <?php endif; ?>

                        <div class="airline-info <?php echo $vuelo['best_price'] ? 'airline-info--has-badge' : ''; ?>">
                            <div class="airline-logo">
                                <i class="fa-solid fa-plane"></i>
                            </div>
                            <div>
                                <h4 class="airline-name"><?php echo $vuelo['airline']; ?></h4>
                                <p class="flight-number"><?php echo $vuelo['flight_number']; ?></p>
                            </div>
                        </div>

                        <div class="flight-times">
                            <div class="time-block">
                                <p class="time-value"><?php echo $vuelo['departure_time']; ?></p>
                                <p class="time-airport"><?php echo $vuelo['departure_airport']; ?></p>
                            </div>
                            
                            <div class="duration-line">
                                <span class="duration-text"><?php echo $vuelo['duration']; ?></span>
                                <div class="line-graphic"></div>
                                <?php if ($vuelo['stops'] == 0): ?>
                                    <span class="stops-badge stops-badge--direct">Directo</span>
                                <?php else: ?>
                                    <span class="stops-badge stops-badge--stops"><?php echo $vuelo['stops']; ?> escala<?php echo $vuelo['stops'] > 1 ? 's' : ''; ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="time-block">
                                <p class="time-value"><?php echo $vuelo['arrival_time']; ?></p>
                                <p class="time-airport"><?php echo $vuelo['arrival_airport']; ?></p>
                            </div>
                        </div>

                        <div class="flight-action">
                            <div class="price-container">
                                <p class="price-value precio-base" data-precio="<?php echo $vuelo['price']; ?>">S/. <?php echo number_format($vuelo['price'], 2); ?></p>
                                <p class="price-label">por persona</p>
                            </div>
                            
                            <form action="index.php" method="POST" style="width: 100%;">
                                <input type="hidden" name="action" value="checkout">
                                <input type="hidden" name="flight_id" value="<?php echo $vuelo['id']; ?>">
                                <input type="hidden" name="pasajeros" class="input-pasajeros" value="<?php echo $pasajeros; ?>">
                                <input type="hidden" name="origen" value="<?php echo $origen; ?>">
                                <input type="hidden" name="destino" value="<?php echo $destino; ?>">
                                <input type="hidden" name="tipo_viaje" value="<?php echo htmlspecialchars($data['tipo_viaje'] ?? 'solo_ida'); ?>">
                                <input type="hidden" name="fecha_retorno" value="<?php echo htmlspecialchars($data['fecha_retorno'] ?? ''); ?>">
                                <button type="submit" class="btn btn--primary" style="width: 100%;">
                                    Reservar <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>
                                </button>
                            </form>
                            
                        </div>
                        
                    </div>
                    <?php } ?>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fa-solid fa-plane-slash no-results-icon"></i>
                        <h3 class="no-results-title">Sin resultados</h3>
                        <p class="no-results-text">Ajusta los filtros para ver más opciones</p>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</main>

<script>
    // JS se usa ÚNICAMENTE para efectos visuales (actualizar el precio al incrementar los boletos en la vista)
    // La lógica de negocio (filtrado, lista de vuelos) se maneja en el servidor PHP
    function cambiarBoleto(btn, cambio) {
        const contenedor = btn.parentElement;
        const spanCantidad = contenedor.querySelector('.num-boletos');
        let cantidad = parseInt(spanCantidad.innerText);
        
        // Evitamos que baje de 1 boleto
        if (cantidad + cambio >= 1) {
            cantidad += cambio;
            spanCantidad.innerText = cantidad;
            
            // Efecto visual: actualizamos el precio multiplicando por la cantidad
            const tarjeta = btn.closest('.bg-white');
            const pPrecio = tarjeta.querySelector('.precio-base');
            const precioBase = parseInt(pPrecio.getAttribute('data-precio'));
            pPrecio.innerText = `S/. ${precioBase * cantidad}`;

            // Actualizamos el input oculto para que el checkout sepa cuántos pasajeros son
            const inputPasajeros = tarjeta.querySelector('.input-pasajeros');
            if(inputPasajeros) {
                inputPasajeros.value = cantidad;
            }
        }
    }
</script>
