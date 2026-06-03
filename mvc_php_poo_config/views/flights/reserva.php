<main class="bg-gray-50 min-h-screen pb-20">
    
    <section class="bg-[#0A192F] border-t border-gray-800 text-white py-4">
        <div class="max-w-[1280px] mx-auto px-8 flex justify-between items-center text-sm">
            <div class="flex items-center gap-2">
                <?php 
                    $origen = isset($_GET['origen']) ? htmlspecialchars($_GET['origen']) : 'Lima';
                    $destino = isset($_GET['destino']) ? htmlspecialchars($_GET['destino']) : 'Madrid';
                    $fecha = isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '25 Jul 2026';
                    $pasajeros = isset($_GET['pasajeros']) ? htmlspecialchars($_GET['pasajeros']) : '1';
                    $isRoundTrip = ($data['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta';
                ?>
                <span class="font-bold">
                    <?php echo $origen; ?> 
                    <?php echo $isRoundTrip ? '<i class="fa-solid fa-arrow-right-arrow-left text-[#0070F3] mx-1"></i>' : '→'; ?> 
                    <?php echo $destino; ?>
                </span>
                <span class="text-gray-400">·</span>
                <span class="text-gray-300"><?php echo $fecha; ?> <?php echo $isRoundTrip && !empty($data['fecha_retorno']) ? ' al ' . htmlspecialchars($data['fecha_retorno']) : ''; ?></span>
                <span class="text-gray-400">·</span>
                <span class="text-gray-300"><?php echo $pasajeros; ?> pasajero(s) <?php echo $isRoundTrip ? '<span class="ml-2 bg-[#0070F3] text-white px-2 py-0.5 rounded-full text-xs">Ida y Vuelta</span>' : ''; ?></span>
            </div>
            <a href="?action=home" class="text-[#0070F3] hover:text-blue-400 flex items-center gap-2 transition-colors font-medium">
                <i class="fa-solid fa-pen"></i> Editar búsqueda
            </a>
        </div>
    </section>

    <section class="bg-[#EAF4FF] border-b border-blue-100 py-3">
        <div class="max-w-[1280px] mx-auto px-8 flex items-center gap-3 text-sm text-[#0A192F]">
            <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
                <i class="fa-solid fa-wand-magic-sparkles text-[#0070F3]"></i>
                <p>La IA entendió tu solicitud y encontró <span class="font-bold text-[#0070F3]"><?php echo count($vuelos_encontrados); ?> vuelos</span>. El mejor precio sale los martes.</p>
            <?php else: ?>
                <i class="fa-solid fa-plane text-[#0070F3]"></i>
                <p>Hemos encontrado <span class="font-bold text-[#0070F3]"><?php echo count($vuelos_encontrados); ?> vuelos</span> para tu ruta.</p>
            <?php endif; ?>
        </div>
    </section>

    <div class="max-w-[1280px] mx-auto px-8 mt-8 grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        
        <aside class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-1 sticky top-28">
            <h2 class="text-xl font-bold text-[#0A192F] text-center mb-8">Filtros</h2>
            
            <form method="GET" action="index.php">
                <input type="hidden" name="action" value="reserva">
                
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-[#0A192F] text-center mb-3">Ruta de vuelo</h3>
                    <?php 
                        $origenSel = isset($_GET['origen']) ? $_GET['origen'] : '';
                        $destinoSel = isset($_GET['destino']) ? $_GET['destino'] : '';
                        $opciones = ['Lima', 'Cusco', 'Arequipa', 'Bogotá', 'Madrid', 'París'];
                    ?>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Origen</label>
                            <select name="origen" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:border-[#0070F3] bg-white">
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
                            <label class="block text-xs font-medium text-gray-500 mb-1">Destino</label>
                            <select name="destino" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:border-[#0070F3] bg-white">
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
                            <label class="block text-xs font-medium text-gray-500 mb-1">Pasajeros</label>
                            <input type="number" name="pasajeros" min="1" value="<?php echo htmlspecialchars($pasajeros); ?>" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:border-[#0070F3] bg-white">
                        </div>
                    </div>
                </div>

                <div class="w-full h-px bg-gray-100 my-6"></div>

                <div class="mb-8">
                    <h3 class="text-sm font-bold text-[#0A192F] text-center mb-4">Precio máximo</h3>
                    <?php $currentPrice = isset($_GET['max_price']) ? $_GET['max_price'] : 4000; ?>
                    <input type="range" name="max_price" min="500" max="4000" value="<?php echo $currentPrice; ?>" 
                           class="w-full accent-[#0070F3] mb-4" oninput="document.getElementById('precio-etiqueta').innerText = 'S/. ' + this.value">
                    
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>S/. 500</span>
                        <span id="precio-etiqueta" class="bg-[#0070F3] text-white font-bold px-3 py-1 rounded-full">S/. <?php echo $currentPrice; ?></span>
                        <span>S/. 4,000</span>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-sm font-bold text-[#0A192F] text-center mb-4">Escalas</h3>
                    <?php $stops = isset($_GET['stops']) ? $_GET['stops'] : ['0', '1']; ?>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="stops[]" value="0" <?php echo in_array('0', $stops) ? 'checked' : ''; ?> class="w-5 h-5 accent-[#0070F3] border-gray-300 rounded cursor-pointer">
                            <span class="text-sm text-gray-700 group-hover:text-[#0070F3] transition-colors">Directo</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="stops[]" value="1" <?php echo in_array('1', $stops) ? 'checked' : ''; ?> class="w-5 h-5 accent-[#0070F3] border-gray-300 rounded cursor-pointer">
                            <span class="text-sm text-gray-700 group-hover:text-[#0070F3] transition-colors">1 escala</span>
                        </label>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-sm font-bold text-[#0A192F] text-center mb-4">Aerolíneas</h3>
                    <?php 
                        $airlines = isset($_GET['airlines']) ? $_GET['airlines'] : ['Copa Airlines', 'Avianca', 'LATAM Airlines', 'Iberia']; 
                        $available_airlines = ['Copa Airlines', 'Avianca', 'LATAM Airlines', 'Iberia'];
                    ?>
                    <div class="space-y-3">
                        <?php 
                            for ($i = 0; $i < count($available_airlines); $i++) {
                                $airline = $available_airlines[$i];
                        ?>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="airlines[]" value="<?php echo $airline; ?>" <?php echo in_array($airline, $airlines) ? 'checked' : ''; ?> class="w-5 h-5 accent-[#0070F3] border-gray-300 rounded cursor-pointer">
                            <span class="text-sm text-gray-700 group-hover:text-[#0070F3] transition-colors"><?php echo $airline; ?></span>
                        </label>
                        <?php } ?>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#0070F3] hover:bg-[#0051CC] text-white py-2.5 rounded-xl font-bold transition-all shadow-md">
                    Aplicar Filtros
                </button>
            </form>
        </aside>

        <div class="lg:col-span-3">
            
            <div class="flex gap-4 mb-6">
                <button class="bg-[#0070F3] text-white px-6 py-2.5 rounded-xl font-medium shadow-sm transition-colors">Mejor precio</button>
                <button class="bg-white text-gray-600 hover:text-[#0070F3] px-6 py-2.5 rounded-xl font-medium border border-gray-200 transition-colors">Duración</button>
            </div>

            <div id="lista-vuelos" class="space-y-6">
                <?php if (count($vuelos_encontrados) > 0): ?>
                    <?php 
                        for ($i = 0; $i < count($vuelos_encontrados); $i++) {
                            $vuelo = $vuelos_encontrados[$i];
                    ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row items-center justify-between gap-6 hover:shadow-md transition-shadow relative overflow-hidden">
                        
                        <?php if ($vuelo['best_price']): ?>
                        <div class="absolute top-0 left-0 bg-[#0070F3] text-white text-[10px] font-bold px-4 py-1.5 rounded-br-xl">
                            Mejor precio
                        </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-4 w-full md:w-1/4 <?php echo $vuelo['best_price'] ? 'mt-4 md:mt-0' : ''; ?>">
                            <div class="w-12 h-12 rounded-full bg-[#0070F3] flex items-center justify-center text-white shrink-0 shadow-sm">
                                <i class="fa-solid fa-plane"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#0A192F]"><?php echo $vuelo['airline']; ?></h4>
                                <p class="text-xs text-gray-500"><?php echo $vuelo['flight_number']; ?></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between flex-1 w-full gap-4">
                            <div class="text-center">
                                <p class="text-xl font-bold text-[#0A192F]"><?php echo $vuelo['departure_time']; ?></p>
                                <p class="text-xs text-gray-500"><?php echo $vuelo['departure_airport']; ?></p>
                            </div>
                            
                            <div class="flex-1 relative flex flex-col items-center">
                                <span class="text-xs text-gray-500 mb-1"><?php echo $vuelo['duration']; ?></span>
                                <div class="w-full h-[1px] bg-gray-300"></div>
                                <?php if ($vuelo['stops'] == 0): ?>
                                    <span class="absolute top-4 bg-green-50 text-green-600 border border-green-200 text-[10px] font-bold px-3 py-1 rounded-full">Directo</span>
                                <?php else: ?>
                                    <span class="absolute top-4 bg-gray-100 text-gray-600 border border-gray-200 text-[10px] font-medium px-3 py-1 rounded-full"><?php echo $vuelo['stops']; ?> escala<?php echo $vuelo['stops'] > 1 ? 's' : ''; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="text-center">
                                <p class="text-xl font-bold text-[#0A192F]"><?php echo $vuelo['arrival_time']; ?><?php if ($vuelo['arrival_next_day']): ?><span class="text-xs text-red-500 ml-0.5">+1</span><?php endif; ?></p>
                                <p class="text-xs text-gray-500"><?php echo $vuelo['arrival_airport']; ?></p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end w-full md:w-1/4 gap-3 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                            
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <span>Boletos:</span>
                                <div class="flex items-center bg-gray-100 rounded-lg">
                                    <button type="button" onclick="cambiarBoleto(this, -1)" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-l-lg transition-colors">-</button>
                                    <span class="w-6 text-center font-bold text-[#0A192F] num-boletos">1</span>
                                    <button type="button" onclick="cambiarBoleto(this, 1)" class="w-8 h-8 flex items-center justify-center hover:bg-gray-200 rounded-r-lg transition-colors">+</button>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-2xl font-extrabold text-[#0A192F] precio-base" data-precio="<?php echo $vuelo['price']; ?>">S/. <?php echo $vuelo['price']; ?></p>
                                <p class="text-xs text-gray-500">por persona</p>
                            </div>
                            <form action="index.php" method="POST" class="w-full">
                                <input type="hidden" name="action" value="checkout">
                                <input type="hidden" name="flight_id" value="<?php echo $vuelo['id']; ?>">
                                <input type="hidden" name="pasajeros" class="input-pasajeros" value="<?php echo $pasajeros; ?>">
                                <input type="hidden" name="origen" value="<?php echo $origen; ?>">
                                <input type="hidden" name="destino" value="<?php echo $destino; ?>">
                                <input type="hidden" name="tipo_viaje" value="<?php echo htmlspecialchars($data['tipo_viaje'] ?? 'solo_ida'); ?>">
                                <input type="hidden" name="fecha_retorno" value="<?php echo htmlspecialchars($data['fecha_retorno'] ?? ''); ?>">
                                <button type="submit" class="w-full bg-[#0070F3] hover:bg-[#0051CC] text-white py-2.5 rounded-xl font-bold transition-all shadow-md flex items-center justify-center gap-2 group">
                                    Reservar <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php } ?>
                <?php else: ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center mt-6">
                        <div class="relative w-24 h-24 mx-auto mb-6">
                            <div class="absolute inset-0 bg-blue-50 rounded-full animate-ping opacity-20"></div>
                            <div class="relative flex items-center justify-center w-full h-full bg-blue-100 rounded-full text-[#0070F3] text-4xl shadow-inner">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-[#0A192F] mb-2">Sin resultados</h3>
                        <p class="text-gray-500">Ajusta los filtros para ver más opciones</p>
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
