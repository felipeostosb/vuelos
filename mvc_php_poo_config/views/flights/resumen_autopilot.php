<?php
// views/flights/resumen_autopilot.php
$vuelo_ida = $vuelo_seleccionado;
$vuelo_vuelta = $vuelo_vuelta_seleccionado ?? null;
$es_ida_vuelta = !empty($vuelo_vuelta);

// Datos del usuario titular
$perfil_titular = $usuario_perfil;
$tarjeta_pref   = !empty($perfil_titular['tarjeta_mascarada_pref']) ? $perfil_titular['tarjeta_mascarada_pref'] : 'Visa **** 4892';
$doc_pref       = !empty($perfil_titular['numero_documento_pref']) ? $perfil_titular['numero_documento_pref'] : '71928374';
$tipo_doc_pref  = !empty($perfil_titular['tipo_documento_pref']) ? $perfil_titular['tipo_documento_pref'] : 'DNI';

// Precio base por pasajero (ida o ida+vuelta)
$precio_unitario_ida = (float)($vuelo_ida['precio'] ?? 120.00);
$precio_unitario_vuelta = $es_ida_vuelta ? (float)($vuelo_vuelta['precio'] ?? 110.00) : 0;
$precio_unitario_total = $precio_unitario_ida + $precio_unitario_vuelta;

// Acompañantes registrados
$lista_acompanantes = $acompanantes_registrados ?? [];
?>

<main class="bg-[#0A1628] min-h-screen text-white font-sans py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- CABECERA HERO RESUMEN VIP -->
        <div class="text-center space-y-3">
            <span class="px-5 py-2 bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/30 text-xs font-light tracking-[0.25em] uppercase rounded-full inline-flex items-center gap-2">
                <i class="fa-solid fa-bolt text-sm animate-pulse"></i> Experiencia Auto-Pilot 1-Clic
            </span>
            <h1 class="text-3xl md:text-4xl font-light tracking-[0.05em] text-white">Resumen Pre-Compra Exprés</h1>
            <p class="text-[#C5A880]/80 text-xs md:text-sm font-light tracking-wide max-w-xl mx-auto">
                La Inteligencia Artificial de NovAirlines ha seleccionado la tarifa más óptima según sus preferencias.
            </p>
        </div>

        <!-- PANEL EDITAR BÚSQUEDA EXPRÉS (DESPLEGABLE) -->
        <div id="panel-editar-expres" class="hidden bg-[#132238]/90 border border-[#C5A880]/40 rounded-3xl p-6 shadow-2xl backdrop-blur-xl space-y-4">
            <div class="flex justify-between items-center border-b border-[#C5A880]/20 pb-3">
                <h3 class="text-sm font-light text-[#C5A880] uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Modificar Parámetros de Viaje
                </h3>
                <button type="button" onclick="toggleEditarExpres()" class="text-slate-400 hover:text-white text-xs">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            
            <form action="index.php" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <input type="hidden" name="action" value="buscar">
                
                <div>
                    <label class="block text-[10px] font-light text-[#C5A880] uppercase tracking-widest mb-1">Origen</label>
                    <input type="text" name="origen" value="<?php echo htmlspecialchars($origen_query ?? $vuelo_ida['origen_ciudad']); ?>" required 
                           class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                </div>

                <div>
                    <label class="block text-[10px] font-light text-[#C5A880] uppercase tracking-widest mb-1">Destino</label>
                    <input type="text" name="destino" value="<?php echo htmlspecialchars($destino_query ?? $vuelo_ida['destino_ciudad']); ?>" required 
                           class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                </div>

                <div>
                    <label class="block text-[10px] font-light text-[#C5A880] uppercase tracking-widest mb-1">Fecha Salida</label>
                    <input type="date" name="fecha_salida" value="<?php echo htmlspecialchars($fecha_salida_query ?? date('Y-m-d')); ?>" required 
                           class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] font-light text-xs uppercase tracking-widest py-2.5 rounded-xl transition duration-300 shadow-md">
                        Actualizar Búsqueda
                    </button>
                </div>
            </form>
        </div>

        <!-- FORMULARIO PRINCIPAL DE COMPRA 1-CLIC -->
        <form action="index.php" method="POST" id="form-reserva-autopilot" class="space-y-8">
            <input type="hidden" name="action" value="procesar_reserva_autopilot">
            <input type="hidden" name="vuelo_id" value="<?php echo $vuelo_ida['id']; ?>">
            <?php if ($es_ida_vuelta): ?>
                <input type="hidden" name="vuelo_vuelta_id" value="<?php echo $vuelo_vuelta['id']; ?>">
                <input type="hidden" name="tipo_viaje" value="ida_vuelta">
            <?php else: ?>
                <input type="hidden" name="tipo_viaje" value="solo_ida">
            <?php endif; ?>

            <!-- TARJETA 1: ITINERARIO DEL VUELO SELECCIONADO POR LA IA -->
            <div class="bg-[#132238]/80 border border-[#C5A880]/30 rounded-3xl p-6 md:p-8 shadow-2xl backdrop-blur-md space-y-6 relative overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#C5A880]/20 pb-4">
                    <div>
                        <span class="text-xs font-light text-[#C5A880] uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-plane-departure"></i> Vuelo de Ida Seleccionado
                        </span>
                        <h2 class="text-xl font-light text-white tracking-wide mt-1">
                            <?php echo htmlspecialchars($vuelo_ida['origen_ciudad']); ?> (<?php echo htmlspecialchars($vuelo_ida['origen_iata']); ?>) ➔ <?php echo htmlspecialchars($vuelo_ida['destino_ciudad']); ?> (<?php echo htmlspecialchars($vuelo_ida['destino_iata']); ?>)
                        </h2>
                    </div>

                    <button type="button" onclick="toggleEditarExpres()" 
                            class="px-4 py-2 bg-[#C5A880]/15 hover:bg-[#C5A880]/30 border border-[#C5A880]/40 text-[#C5A880] font-light text-xs uppercase tracking-widest rounded-xl transition duration-300 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square"></i> Modificar Búsqueda
                    </button>
                </div>

                <!-- TRAYECTO IDA -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#C5A880]/15 border border-[#C5A880]/30 flex items-center justify-center text-[#C5A880] text-xl">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                        <div>
                            <p class="text-sm font-light text-white tracking-wide"><?php echo htmlspecialchars($vuelo_ida['aerolinea_nombre']); ?></p>
                            <p class="text-xs font-light text-slate-400">Vuelo <?php echo htmlspecialchars($vuelo_ida['numero_vuelo']); ?></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between space-x-8 text-center">
                        <div>
                            <p class="text-2xl font-light text-white tracking-wide"><?php echo htmlspecialchars($vuelo_ida['hora_salida']); ?></p>
                            <p class="text-xs font-light text-[#C5A880]"><?php echo htmlspecialchars($vuelo_ida['origen_iata']); ?></p>
                        </div>
                        <div class="px-4">
                            <p class="text-[10px] font-light text-slate-400 uppercase tracking-widest mb-1"><?php echo htmlspecialchars($vuelo_ida['duracion']); ?></p>
                            <div class="w-28 h-[1px] bg-gradient-to-r from-transparent via-[#C5A880] to-transparent"></div>
                            <p class="text-[10px] font-light text-slate-400 mt-1"><?php echo $vuelo_ida['escalas'] == 0 ? 'Directo' : $vuelo_ida['escalas'] . ' Escala(s)'; ?></p>
                        </div>
                        <div>
                            <p class="text-2xl font-light text-white tracking-wide"><?php echo htmlspecialchars($vuelo_ida['hora_llegada']); ?></p>
                            <p class="text-xs font-light text-[#C5A880]"><?php echo htmlspecialchars($vuelo_ida['destino_iata']); ?></p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 uppercase font-light tracking-widest">Tarifa por persona</span>
                        <p class="text-xl font-light text-[#C5A880] tracking-wider">S/. <?php echo number_format($precio_unitario_ida, 2); ?></p>
                    </div>
                </div>

                <!-- TRAYECTO VUELTA (SI APLICA) -->
                <?php if ($es_ida_vuelta): ?>
                    <div class="border-t border-[#C5A880]/15 pt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-light text-[#C5A880] uppercase tracking-widest flex items-center gap-2">
                                <i class="fa-solid fa-plane-arrival"></i> Vuelo de Retorno Seleccionado
                            </span>
                        </div>
                        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-2xl bg-[#C5A880]/15 border border-[#C5A880]/30 flex items-center justify-center text-[#C5A880] text-xl">
                                    <i class="fa-solid fa-plane"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-light text-white tracking-wide"><?php echo htmlspecialchars($vuelo_vuelta['aerolinea_nombre']); ?></p>
                                    <p class="text-xs font-light text-slate-400">Vuelo <?php echo htmlspecialchars($vuelo_vuelta['numero_vuelo']); ?></p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between space-x-8 text-center">
                                <div>
                                    <p class="text-2xl font-light text-white tracking-wide"><?php echo htmlspecialchars($vuelo_vuelta['hora_salida']); ?></p>
                                    <p class="text-xs font-light text-[#C5A880]"><?php echo htmlspecialchars($vuelo_vuelta['origen_iata']); ?></p>
                                </div>
                                <div class="px-4">
                                    <p class="text-[10px] font-light text-slate-400 uppercase tracking-widest mb-1"><?php echo htmlspecialchars($vuelo_vuelta['duracion']); ?></p>
                                    <div class="w-28 h-[1px] bg-gradient-to-r from-transparent via-[#C5A880] to-transparent"></div>
                                </div>
                                <div>
                                    <p class="text-2xl font-light text-white tracking-wide"><?php echo htmlspecialchars($vuelo_vuelta['hora_llegada']); ?></p>
                                    <p class="text-xs font-light text-[#C5A880]"><?php echo htmlspecialchars($vuelo_vuelta['destino_iata']); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-light tracking-widest">Tarifa retorno</span>
                                <p class="text-xl font-light text-[#C5A880] tracking-wider">S/. <?php echo number_format($precio_unitario_vuelta, 2); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- TARJETA 2: PASAJEROS Y ACOMPAÑANTES INCLUIDOS EN LA RESERVA -->
            <div class="bg-[#132238]/80 border border-[#C5A880]/30 rounded-3xl p-6 md:p-8 shadow-2xl backdrop-blur-md space-y-6">
                <div class="flex justify-between items-center border-b border-[#C5A880]/20 pb-4">
                    <div>
                        <h3 class="text-lg font-light text-white tracking-wide flex items-center gap-2">
                            <i class="fa-solid fa-users text-[#C5A880]"></i> Selección de Pasajeros para la Reserva
                        </h3>
                        <p class="text-xs text-slate-300 font-light">Active o desactive los acompañantes guardados en su perfil para actualizar el total instantáneamente.</p>
                    </div>
                    <span id="badge-pax-count" class="px-3 py-1 bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/30 text-xs font-light uppercase tracking-widest rounded-full">
                        1 Pasajero
                    </span>
                </div>

                <!-- PASAJERO 1: TITULAR (SIEMPRE OBLIGATORIO) -->
                <div class="bg-[#0A1628]/80 border border-[#C5A880]/40 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-[#C5A880] text-[#0A1628] flex items-center justify-center font-light text-base shadow-md">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <div>
                            <p class="text-sm font-light text-white tracking-wide"><?php echo htmlspecialchars($_SESSION['user_name']); ?> <span class="text-[#C5A880] text-xs">(Titular Principal)</span></p>
                            <p class="text-xs text-slate-400 font-light"><?php echo htmlspecialchars($tipo_doc_pref); ?>: <?php echo htmlspecialchars($doc_pref); ?> · <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                        </div>
                    </div>
                    <span class="text-xs font-light text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-3 py-1 rounded-full">Incluido</span>
                </div>

                <!-- LISTA DE ACOMPAÑANTES SELECCIONABLES CON CHECKBOX -->
                <?php if (!empty($lista_acompanantes)): ?>
                    <div class="space-y-3 pt-2">
                        <p class="text-xs font-light text-[#C5A880] uppercase tracking-widest">Acompañantes Guardados en Perfil:</p>
                        <?php foreach ($lista_acompanantes as $idx => $ac): ?>
                            <label class="bg-[#0A1628]/60 border border-[#C5A880]/20 hover:border-[#C5A880]/50 rounded-2xl p-4 flex items-center justify-between cursor-pointer transition">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" name="acompanantes_ids[]" value="<?php echo $ac['id']; ?>" 
                                           onchange="actualizarPrecioResumen()" 
                                           class="chk-acompanante w-5 h-5 accent-[#C5A880] cursor-pointer">
                                    <div>
                                        <p class="text-sm font-light text-white tracking-wide"><?php echo htmlspecialchars($ac['nombre'] . ' ' . $ac['apellido']); ?></p>
                                        <p class="text-xs text-slate-400 font-light"><?php echo htmlspecialchars($ac['tipo_documento']); ?>: <?php echo htmlspecialchars($ac['numero_documento']); ?></p>
                                    </div>
                                </div>
                                <span class="text-xs font-light text-slate-400">+ S/. <?php echo number_format($precio_unitario_total, 2); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 rounded-2xl bg-[#0A1628]/40 border border-[#C5A880]/15 text-center text-xs font-light text-slate-400">
                        No tiene acompañantes registrados en su perfil. Puede agregarlos en <a href="index.php?action=panel" class="text-[#C5A880] underline">Mi Perfil</a> para futuras reservas grupales.
                    </div>
                <?php endif; ?>

            </div>

            <!-- TARJETA 3: FORMA DE PAGO & CONFIRMACIÓN 1-CLIC -->
            <div class="bg-[#132238]/90 border border-[#C5A880]/40 rounded-3xl p-6 md:p-8 shadow-2xl backdrop-blur-md space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-[#C5A880]/20 pb-4 gap-4">
                    <div>
                        <span class="text-xs font-light text-[#C5A880] uppercase tracking-widest">Método de Pago Predeterminado</span>
                        <p class="text-base font-light text-white tracking-wide flex items-center gap-2 mt-0.5">
                            <i class="fa-solid fa-credit-card text-[#C5A880]"></i> <?php echo htmlspecialchars($tarjeta_pref); ?>
                        </p>
                    </div>

                    <div class="text-right">
                        <span class="text-xs font-light text-slate-400 uppercase tracking-widest block">Total a Confirmar</span>
                        <span id="text-precio-total" class="text-3xl font-light text-[#C5A880] tracking-wider">
                            S/. <?php echo number_format($precio_unitario_total, 2); ?>
                        </span>
                    </div>
                </div>

                <!-- ACCIONES DE CONFIRMACIÓN VIP -->
                <div class="space-y-3 pt-2">
                    <button type="submit" 
                            class="w-full bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] font-light text-sm uppercase tracking-[0.15em] py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-[#C5A880]/20 flex items-center justify-center gap-3">
                        <i class="fa-solid fa-bolt text-lg"></i>
                        <span>Confirmar y Reservar en 1-Clic (S/. <span id="btn-precio-total"><?php echo number_format($precio_unitario_total, 2); ?></span>)</span>
                    </button>

                    <div class="flex justify-between items-center text-xs font-light text-slate-400 pt-2">
                        <a href="index.php?action=buscar&origen=<?php echo urlencode($vuelo_ida['origen_ciudad']); ?>&destino=<?php echo urlencode($vuelo_ida['destino_ciudad']); ?>" 
                           class="hover:text-[#C5A880] underline transition">
                            <i class="fa-solid fa-list mr-1"></i> Ver todos los vuelos alternativos
                        </a>
                        <span class="flex items-center gap-1 text-slate-400">
                            <i class="fa-solid fa-[#C5A880] fa-shield-halved text-[#C5A880]"></i> Transacción Segura TLS NovAirlines
                        </span>
                    </div>
                </div>
            </div>

        </form>
    </div>
</main>

<script>
const precioUnitario = <?php echo $precio_unitario_total; ?>;

function actualizarPrecioResumen() {
    const checkboxes = document.querySelectorAll('.chk-acompanante:checked');
    const paxCount = 1 + checkboxes.length;
    const total = (precioUnitario * paxCount).toFixed(2);

    document.getElementById('badge-pax-count').innerText = paxCount + (paxCount === 1 ? ' Pasajero' : ' Pasajeros');
    document.getElementById('text-precio-total').innerText = 'S/. ' + total;
    document.getElementById('btn-precio-total').innerText = total;
}

function toggleEditarExpres() {
    const panel = document.getElementById('panel-editar-expres');
    panel.classList.toggle('hidden');
}
</script>
