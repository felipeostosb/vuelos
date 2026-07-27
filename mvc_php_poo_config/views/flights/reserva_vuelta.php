<main class="reserva-page">
    
    <section class="route-header">
        <div class="route-container">
            <div class="route-info">
                <?php 
                    $origen = isset($_GET['origen']) ? htmlspecialchars($_GET['origen']) : 'Origen';
                    $destino = isset($_GET['destino']) ? htmlspecialchars($_GET['destino']) : 'Destino';
                    $fechas = isset($_GET['rango_fechas']) ? htmlspecialchars($_GET['rango_fechas']) : date('Y-m-d');
                    
                    // Split if it's a range "2026-07-25 to 2026-08-01"
                    $fecha_partes = explode(' to ', $fechas);
                    $fecha_ida = $fecha_partes[0];
                    $fecha_vuelta = $fecha_partes[1] ?? '';
                    
                    $pasajeros = $pasajeros ?? $_SESSION['datos_busqueda']['pasajeros'] ?? (isset($_GET['pasajeros']) ? htmlspecialchars($_GET['pasajeros']) : '1');
                    $tipo_viaje = isset($_GET['tipo_viaje']) ? htmlspecialchars($_GET['tipo_viaje']) : ($_SESSION['datos_busqueda']['tipo_viaje'] ?? 'solo_ida');
                    $isRoundTrip = $tipo_viaje === 'ida_vuelta';
                ?>
                <span class="route-destinations">
                    <?php echo $origen; ?> 
                    <?php echo $isRoundTrip ? '<i class="fa-solid fa-arrow-right-arrow-left text-[#0070F3] mx-1"></i>' : '→'; ?> 
                    <?php echo $destino; ?>
                </span>
                <span class="route-separator">·</span>
                <span class="route-text"><?php echo $fecha_ida; ?> <?php echo $isRoundTrip && !empty($fecha_vuelta) ? ' al ' . htmlspecialchars($fecha_vuelta) : ''; ?></span>
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
                <input type="hidden" name="action" value="seleccionar_vuelta">
                <input type="hidden" name="outbound_id" value="<?php echo htmlspecialchars($outbound_signature); ?>">
                
                <div class="filter-section">
                    <h3 class="filter-subtitle">Ruta de vuelo</h3>
                    <?php 
                        $sess = $_SESSION['datos_busqueda'] ?? [];
                        $origenSel = isset($_GET['origen']) ? $_GET['origen'] : ($sess['origen_ciudad'] ?? '');
                        $destinoSel = isset($_GET['destino']) ? $_GET['destino'] : ($sess['destino_ciudad'] ?? '');
                        $pasajerosSel = isset($_GET['pasajeros']) ? $_GET['pasajeros'] : ($sess['pasajeros'] ?? 1);
                        
                        $opciones = isset($vueloModel) ? $vueloModel->obtenerFiltrosDestinos() : [];
                    ?>
                    
                    <div class="filter-input-group">
                        <div>
                            <label class="filter-label">Origen</label>
                            <input list="filtro_origen" name="origen" class="filter-input" style="width:100%; padding:0.5rem; border:1px solid #e5e7eb; border-radius:0.375rem;" placeholder="Todos" value="<?php echo htmlspecialchars($origenSel); ?>">
                            <datalist id="filtro_origen">
                                <?php foreach ($opciones as $op): ?>
                                    <option value="<?php echo htmlspecialchars($op); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        
                        <div style="margin-top:0.75rem;">
                            <label class="filter-label">Destino</label>
                            <input list="filtro_destino" name="destino" class="filter-input" style="width:100%; padding:0.5rem; border:1px solid #e5e7eb; border-radius:0.375rem;" placeholder="Todos" value="<?php echo htmlspecialchars($destinoSel); ?>">
                            <datalist id="filtro_destino">
                                <?php foreach ($opciones as $op): ?>
                                    <option value="<?php echo htmlspecialchars($op); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        
                        <div>
                            <label class="filter-label">Pasajeros</label>
                            <input type="number" name="pasajeros" min="1" value="<?php echo htmlspecialchars($pasajerosSel); ?>" class="filter-input-number">
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
                        <span id="precio-etiqueta" style="background: linear-gradient(135deg, #C5A880, #9C694C); color: #0A1628; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem;">S/. <?php echo $currentPrice; ?></span>
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
                        $available_airlines = [];
                        if (isset($_SESSION['ofertas_actuales'])) {
                            foreach ($_SESSION['ofertas_actuales'] as $oferta) {
                                $air = $oferta['airline'];
                                if (!in_array($air, $available_airlines)) {
                                    $available_airlines[] = $air;
                                }
                            }
                            sort($available_airlines);
                        }
                        $airlines = isset($_GET['airlines']) ? $_GET['airlines'] : $available_airlines; 
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
                <button class="btn-sort btn-sort--active">Mejor precio (Vuelta)</button>
                <button class="btn-sort btn-sort--inactive">Duración</button>
            </div>

            <?php if (isset($vuelo_ida_seleccionado)): ?>
            <div class="flight-card" style="margin-bottom: 2rem; border: 2px solid rgba(197,168,128,0.5); background-color: rgba(19,34,56,0.9);">
                <div style="font-size:0.875rem; font-weight:bold; color:#4ade80; margin-bottom: 1rem; border-bottom: 1px solid rgba(197,168,128,0.15); padding-bottom: 0.5rem;">
                    <i class="fa-solid fa-check-circle"></i> Vuelo de Ida Seleccionado
                </div>
                <div class="flight-times">
                    <div class="time-block">
                        <p class="time-value"><?php echo $vuelo_ida_seleccionado['departure_time']; ?></p>
                        <p class="time-airport"><?php echo $vuelo_ida_seleccionado['departure_airport']; ?></p>
                        <p class="time-date-text"><?php echo $vuelo_ida_seleccionado['departure_date']; ?></p>
                    </div>
                    
                    <div class="duration-line">
                        <span class="duration-text"><?php echo str_replace(['PT', 'H', 'M'], ['','h ','m'], $vuelo_ida_seleccionado['duration']); ?></span>
                        <div class="line-graphic"></div>
                        <span class="stops-badge stops-badge--direct"><?php echo $vuelo_ida_seleccionado['airline']; ?></span>
                    </div>
                    
                    <div class="time-block">
                        <p class="time-value"><?php echo $vuelo_ida_seleccionado['arrival_time']; ?></p>
                        <p class="time-airport"><?php echo $vuelo_ida_seleccionado['arrival_airport']; ?></p>
                        <p class="time-date-text"><?php echo $vuelo_ida_seleccionado['arrival_date']; ?></p>
                    </div>
                </div>
            </div>
            
            <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem; color: #C5A880;">Paso 2: Selecciona tu vuelo de regreso</h3>
            <?php endif; ?>

            <div id="lista-vuelos" class="flight-list">
                <?php if (count($vuelos_encontrados) > 0): ?>
                    <?php 
                        for ($i = 0; $i < count($vuelos_encontrados); $i++) {
                            $vuelo = $vuelos_encontrados[$i];
                    ?>
                    <div class="flight-card">
                        
                        <?php if ($vuelo['best_price']): ?>
                        <div class="best-price-badge">Mejor precio</div>
                        <?php endif; ?>

                        <?php 
                            // Helper function to render a flight slice
                            if (!function_exists('renderSlice')) {
                                function renderSlice($slice, $title) {
                        ?>
                            <div class="slice-container">
                                <span class="slice-title"><?php echo $title; ?></span>

                                <div class="airline-info">
                                    <div class="airline-logo">
                                        <i class="fa-solid fa-plane"></i>
                                    </div>
                                    <div>
                                        <h4 class="airline-name"><?php echo $slice['airline']; ?></h4>
                                        <p class="flight-number"><?php echo $slice['flight_number']; ?></p>
                                    </div>
                                </div>

                                <div class="flight-times">
                                    <div class="time-block">
                                        <p class="time-value"><?php echo $slice['departure_time']; ?></p>
                                        <p class="time-airport" title="<?php echo htmlspecialchars($slice['departure_airport_name'] ?? ''); ?>"><?php echo $slice['departure_airport']; ?></p>
                                        <p class="time-city"><?php echo htmlspecialchars($slice['departure_city'] ?? ''); ?></p>
                                        <p class="time-date-text"><?php echo $slice['departure_date'] ?? ''; ?></p>
                                    </div>
                                    
                                    <div class="duration-line">
                                        <span class="duration-text"><?php echo str_replace(['PT', 'H', 'M'], ['','h ','m'], $slice['duration']); ?></span>
                                        <div class="line-graphic"></div>
                                        <?php if ($slice['stops'] == 0): ?>
                                            <span class="stops-badge stops-badge--direct">Directo</span>
                                        <?php else: ?>
                                            <span class="stops-badge stops-badge--stops"><?php echo $slice['stops']; ?> escala<?php echo $slice['stops'] > 1 ? 's' : ''; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="time-block">
                                        <p class="time-value"><?php echo $slice['arrival_time']; ?></p>
                                        <p class="time-airport" title="<?php echo htmlspecialchars($slice['arrival_airport_name'] ?? ''); ?>"><?php echo $slice['arrival_airport']; ?></p>
                                        <p class="time-city"><?php echo htmlspecialchars($slice['arrival_city'] ?? ''); ?></p>
                                        <p class="time-date-text"><?php echo $slice['arrival_date'] ?? ''; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php 
                                }
                            }
                        ?>

                        <!-- Cuerpo principal: vuelo de regreso -->
                        <div class="flight-card-body">
                            <?php renderSlice($vuelo['inbound'], '🛬 Vuelo de Regreso'); ?>
                        </div>

                        <!-- Panel derecho: precio + botón -->
                        <div class="flight-action">
                            <?php
                                $precio_vuelta_mostrar = $vuelo['inbound_price'] ?? round((float)$vuelo['price'] / max(1,(int)$pasajeros) * 0.5, 2);
                            ?>
                            <div class="price-container">
                                <p class="price-value precio-base" data-precio="<?php echo $precio_vuelta_mostrar; ?>"><?php echo htmlspecialchars($vuelo['currency']); ?> <?php echo number_format($precio_vuelta_mostrar, 2); ?></p>
                                <p class="price-label">VUELO DE VUELTA</p>
                                <p class="price-sublabel">por persona · solo vuelta</p>
                            </div>
                            
                            <form action="index.php" method="POST" style="width:100%;">
                                <input type="hidden" name="action" value="checkout">
                                <input type="hidden" name="flight_id" value="<?php echo $vuelo['id']; ?>">
                                <input type="hidden" name="pasajeros" class="input-pasajeros" value="<?php echo htmlspecialchars($pasajeros); ?>">
                                <input type="hidden" name="tipo_viaje" value="ida_vuelta">
                                <button type="submit" class="btn-reserve">
                                    Seleccionar y Reservar <i class="fa-solid fa-arrow-right"></i>
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
