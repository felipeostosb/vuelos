<?php
$usuario_perfil = obtener_usuario_por_id($_SESSION['user_id']);
$acompanantes   = obtener_acompanantes_usuario($_SESSION['user_id']);
$modo_autopilot_activo = (int)($usuario_perfil['modo_autopilot'] ?? 0);
?>
<main class="panel-page bg-[#0A1628] min-h-screen text-white font-sans py-12 px-4 sm:px-6 lg:px-8">
    <div class="panel-container max-w-6xl mx-auto space-y-8">
        
        <!-- HEADER DEL PANEL BOUTIQUE -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-[#132238]/80 backdrop-blur border border-[#C5A880]/30 rounded-3xl p-6 shadow-2xl gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-[#C5A880]/15 border border-[#C5A880]/40 flex items-center justify-center text-[#C5A880] text-2xl font-light shadow-lg">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <div>
                    <h1 class="text-2xl font-light tracking-wide text-white">Mi Panel de Viajes VIP</h1>
                    <p class="text-slate-300 text-xs font-light tracking-wide mt-0.5">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?> · NovAirlines Member</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <?php if ($modo_autopilot_activo === 1): ?>
                    <span class="px-4 py-2 bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/40 text-xs font-light uppercase tracking-widest rounded-full flex items-center gap-2 shadow-md animate-pulse">
                        <i class="fa-solid fa-bolt"></i> Auto-Pilot Activado
                    </span>
                <?php else: ?>
                    <span class="px-4 py-2 bg-slate-800/60 text-slate-400 border border-slate-700 text-xs font-light uppercase tracking-widest rounded-full flex items-center gap-2">
                        <i class="fa-solid fa-power-off"></i> Auto-Pilot Inactivo
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-light tracking-wide flex items-center space-x-3 shadow-lg">
                <i class="fa-solid fa-circle-check text-base"></i>
                <div>
                    <p class="font-light text-sm text-emerald-300">¡Reserva realizada con éxito!</p>
                    <p class="text-xs text-emerald-400/90 mt-0.5">Código de reserva (PNR): <strong class="text-[#C5A880] text-sm tracking-widest"><?php echo htmlspecialchars($_GET['pnr'] ?? ''); ?></strong></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['config_saved'])): ?>
            <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-light tracking-wide flex items-center space-x-3 shadow-lg">
                <i class="fa-solid fa-circle-check text-base"></i>
                <span>Preferencias de Modo Auto-Pilot actualizadas correctamente.</span>
            </div>
        <?php endif; ?>

        <!-- BARRA DE PESTAÑAS (TABS) BOUTIQUE -->
        <div class="flex border-b border-[#C5A880]/20 space-x-8">
            <button onclick="switchTab('vuelos')" id="tab-btn-vuelos" class="pb-3 text-xs font-light uppercase tracking-widest border-b-2 border-[#C5A880] text-[#C5A880] transition flex items-center gap-2">
                <i class="fa-solid fa-ticket"></i> Mis Próximos Vuelos (<?php echo count($misReservas); ?>)
            </button>
            <button onclick="switchTab('autopilot')" id="tab-btn-autopilot" class="pb-3 text-xs font-light uppercase tracking-widest border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center gap-2">
                <i class="fa-solid fa-bolt text-[#C5A880]"></i> ⚡ Configuración Auto-Pilot & Acompañantes
            </button>
        </div>

        <!-- PESTAÑA 1: VUELOS RESERVADOS -->
        <div id="tab-vuelos" class="space-y-6">
            <?php if (empty($misReservas)): ?>
                <div class="bg-[#132238]/60 border border-[#C5A880]/20 rounded-3xl p-12 text-center space-y-4">
                    <div class="w-16 h-16 bg-[#C5A880]/15 border border-[#C5A880]/30 rounded-full flex items-center justify-center mx-auto text-[#C5A880] text-3xl">
                        <i class="fa-solid fa-plane-slash"></i>
                    </div>
                    <h3 class="text-xl font-light text-white tracking-wide">Aún no posee reservas activas</h3>
                    <p class="text-xs text-slate-400 font-light max-w-md mx-auto">Explore nuestros destinos o active el Modo Auto-Pilot para realizar reservas exprés en 1-Clic.</p>
                    <a href="?action=home" class="inline-block bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] font-light text-xs uppercase tracking-widest px-8 py-3.5 rounded-xl transition duration-300 shadow-md">
                        Buscar Vuelos Ahora
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-6">
                    <?php
                    for ($i = count($misReservas) - 1; $i >= 0; $i--) {
                        $reserva = $misReservas[$i];
                        $vuelo   = $reserva['vuelo'];
                        $pnr     = $reserva['pnr'];
                        $isCheckedIn = ($reserva['estado'] === 'Checked-in');
                        $es_ida_vuelta = (($reserva['tipo_viaje'] ?? 'solo_ida') === 'ida_vuelta');
                        $vuelo_vuelta  = $reserva['vuelo_vuelta'] ?? null;

                        if (!empty($reserva['pasajeros'])) {
                            $nombres_pax = array_map(function($p) {
                                return htmlspecialchars(trim($p['nombre'] . ' ' . $p['apellido']));
                            }, $reserva['pasajeros']);
                            $nombres_str = implode(', ', $nombres_pax);
                        } else {
                            $nombres_str = htmlspecialchars($reserva['pasajero_nombre']);
                        }

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
                            <div class="bg-[#132238]/80 border border-[#C5A880]/30 rounded-2xl overflow-hidden shadow-xl flex flex-col md:flex-row">
                                <div class="p-6 flex-1 space-y-4">
                                    <div class="flex items-center justify-between border-b border-[#C5A880]/15 pb-3">
                                        <span class="text-xs font-light text-[#C5A880] uppercase tracking-widest flex items-center gap-2">
                                            <i class="fa-solid <?php echo $leg_icon; ?>"></i> <?php echo $leg_label; ?>
                                        </span>
                                        <span class="text-xs font-light text-slate-400">PNR: <strong class="text-[#C5A880] font-light tracking-wider"><?php echo htmlspecialchars($pnr); ?></strong></span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-lg font-light text-white tracking-wide"><?php echo $airline; ?></p>
                                            <p class="text-xs font-light text-slate-400">Vuelo <?php echo $flight_number; ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-[#C5A880] font-light uppercase tracking-widest"><?php echo $pax_count; ?> Pasajero(s)</p>
                                            <p class="text-xs text-slate-300 font-light truncate max-w-xs"><?php echo $nombres_str; ?></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-2">
                                        <div>
                                            <p class="text-2xl font-light text-white tracking-wide"><?php echo $dep_time; ?></p>
                                            <p class="text-xs font-light text-[#C5A880]"><?php echo $dep_airport; ?> <?php echo $dep_city ? "($dep_city)" : ''; ?></p>
                                            <?php if ($dep_date): ?><p class="text-[10px] text-slate-400 font-light"><?php echo $dep_date; ?></p><?php endif; ?>
                                        </div>
                                        <div class="text-center px-4">
                                            <i class="fa-solid fa-plane text-[#C5A880] text-sm"></i>
                                            <div class="w-24 h-[1px] bg-gradient-to-r from-transparent via-[#C5A880]/40 to-transparent my-1"></div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-light text-white tracking-wide"><?php echo $arr_time; ?></p>
                                            <p class="text-xs font-light text-[#C5A880]"><?php echo $arr_airport; ?> <?php echo $arr_city ? "($arr_city)" : ''; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-[#0A1628]/80 p-6 md:w-64 border-t md:border-t-0 md:border-l border-[#C5A880]/20 flex flex-col justify-center items-center text-center space-y-3">
                                    <span class="text-[10px] uppercase font-light text-slate-400 tracking-widest">Total Boleto</span>
                                    <span class="text-2xl font-light text-[#C5A880] tracking-wider"><?php echo htmlspecialchars($currency); ?> <?php echo $precio_fmt; ?></span>
                                    
                                    <a href="index.php?action=generarBoleto&pnr=<?php echo htmlspecialchars($pnr); ?>" target="_blank" 
                                       class="w-full bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] font-light text-xs uppercase tracking-widest py-2.5 rounded-xl transition duration-300 shadow-md flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-file-pdf"></i> Boleto PDF
                                    </a>
                                </div>
                            </div>
                            <?php
                        };

                        if ($es_ida_vuelta) {
                            $precio_ida = isset($reserva['precio_total']) ? round((float)$reserva['precio_total'] * 0.5, 2) : 0;
                            $currency = $vuelo_vuelta['currency'] ?? 'S/.';
                            $renderTarjeta($vuelo, 'Vuelo de Ida', 'fa-plane-departure', '#C5A880', $precio_ida, $currency, false);
                            if (!empty($vuelo_vuelta)) {
                                $precio_vuelta = round((float)$reserva['precio_total'] * 0.5, 2);
                                $renderTarjeta($vuelo_vuelta, 'Vuelo de Vuelta', 'fa-plane-arrival', '#C5A880', $precio_vuelta, $currency, true);
                            }
                        } else {
                            $renderTarjeta($vuelo, 'Vuelo Directo', 'fa-plane-departure', '#C5A880', (float)$reserva['precio_total'], 'S/.', false);
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- PESTAÑA 2: CONFIGURACIÓN MODO AUTO-PILOT & ACOMPAÑANTES -->
        <div id="tab-autopilot" class="hidden space-y-8">
            
            <!-- PANEL 1: SWITCH MODO AUTO-PILOT -->
            <div class="bg-[#132238]/80 border border-[#C5A880]/30 rounded-3xl p-8 shadow-2xl backdrop-blur-md">
                <form action="index.php" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="guardar_config_autopilot">
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-[#C5A880]/15 pb-6">
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-light text-white tracking-wide">Reserva Automática 1-Clic ("Modo Auto-Pilot")</h3>
                                <span class="px-3 py-1 bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/30 text-[10px] font-light uppercase tracking-widest rounded-full">Exclusivo VIP</span>
                            </div>
                            <p class="text-xs text-slate-300 font-light tracking-wide max-w-2xl">
                                Al estar activado, cada búsqueda que realice por IA o formulario clásico omitirá listas largas de resultados y checkout manual. El sistema seleccionará la opción más barata y conveniente y le desplegará el **Resumen Pre-Compra Exprés** para confirmar en 1-Clic.
                            </p>
                        </div>

                        <!-- TOGGLE SWITCH BOUTIQUE -->
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="modo_autopilot" value="1" class="sr-only peer" <?php echo $modo_autopilot_activo === 1 ? 'checked' : ''; ?>>
                            <div class="w-14 h-7 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-[#0A1628] after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#C5A880]"></div>
                            <span class="ml-3 text-xs font-light text-[#C5A880] uppercase tracking-widest">
                                <?php echo $modo_autopilot_activo === 1 ? 'ACTIVADO' : 'DESACTIVADO'; ?>
                            </span>
                        </label>
                    </div>

                    <!-- FORMULARIO DE DATOS PREDETERMINADOS -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                        <div>
                            <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-2">Tipo de Documento Predeterminado</label>
                            <select name="tipo_documento_pref" class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 rounded-xl px-4 py-3 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                                <option value="DNI" <?php echo ($usuario_perfil['tipo_documento_pref'] ?? '') === 'DNI' ? 'selected' : ''; ?>>DNI (Documento Nacional)</option>
                                <option value="PASAPORTE" <?php echo ($usuario_perfil['tipo_documento_pref'] ?? '') === 'PASAPORTE' ? 'selected' : ''; ?>>Pasaporte Internacional</option>
                                <option value="CE" <?php echo ($usuario_perfil['tipo_documento_pref'] ?? '') === 'CE' ? 'selected' : ''; ?>>Carnet de Extranjería</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-2">Número de Documento Titular</label>
                            <input type="text" name="numero_documento_pref" value="<?php echo htmlspecialchars($usuario_perfil['numero_documento_pref'] ?? ''); ?>" placeholder="Ej: 71829384" required 
                                   class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 rounded-xl px-4 py-3 text-xs font-light text-white focus:outline-none focus:border-[#C5A880] placeholder:text-slate-600">
                        </div>

                        <div>
                            <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-2">Tarjeta de Pago Preferida</label>
                            <input type="text" name="tarjeta_mascarada_pref" value="<?php echo htmlspecialchars($usuario_perfil['tarjeta_mascarada_pref'] ?? 'Visa **** 4892'); ?>" placeholder="Visa **** 1234" required 
                                   class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 rounded-xl px-4 py-3 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] font-light text-xs uppercase tracking-widest px-6 py-3 rounded-xl transition duration-300 shadow-md flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Configuración Auto-Pilot
                        </button>
                    </div>
                </form>
            </div>

            <!-- PANEL 2: GESTIÓN DE ACOMPAÑANTES HABITUALES -->
            <div class="bg-[#132238]/80 border border-[#C5A880]/30 rounded-3xl p-8 shadow-2xl backdrop-blur-md space-y-6">
                <div>
                    <h3 class="text-xl font-light text-white tracking-wide mb-1">Acompañantes Habituales Guardados</h3>
                    <p class="text-xs text-slate-300 font-light">Agregue familiares o compañeros de viaje para que la IA o búsqueda clásica los agregue automáticamente en sus reservas de grupo.</p>
                </div>

                <!-- FORMULARIO PARA REGISTRAR ACOMPAÑANTE -->
                <form action="index.php" method="POST" class="bg-[#0A1628]/70 border border-[#C5A880]/20 p-5 rounded-2xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <input type="hidden" name="action" value="agregar_acompanante">
                    
                    <div>
                        <label class="block text-[10px] font-light text-[#C5A880] uppercase tracking-widest mb-1">Nombre</label>
                        <input type="text" name="nombre" placeholder="Ej. Ana" required class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-light text-[#C5A880] uppercase tracking-widest mb-1">Apellido</label>
                        <input type="text" name="apellido" placeholder="Ej. Martínez" required class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-light text-[#C5A880] uppercase tracking-widest mb-1">Tipo Doc.</label>
                        <select name="tipo_documento" class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                            <option value="DNI">DNI</option>
                            <option value="PASAPORTE">Pasaporte</option>
                            <option value="CE">Carnet Extranjería</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-light text-[#C5A880] uppercase tracking-widest mb-1">N° Documento</label>
                        <input type="text" name="numero_documento" placeholder="78901234" required class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs font-light text-white focus:outline-none focus:border-[#C5A880]">
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] font-light text-xs uppercase tracking-widest py-2.5 rounded-xl transition duration-300 shadow-md">
                            <i class="fa-solid fa-plus mr-1"></i> Agregar
                        </button>
                    </div>
                </form>

                <!-- LISTA DE ACOMPAÑANTES REGISTRADOS -->
                <?php if (empty($acompanantes)): ?>
                    <p class="text-xs text-slate-500 font-light text-center py-4">No posee acompañantes registrados aún.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($acompanantes as $ac): ?>
                            <div class="bg-[#0A1628]/80 border border-[#C5A880]/20 rounded-2xl p-4 flex justify-between items-center shadow-md hover:border-[#C5A880]/50 transition">
                                <div>
                                    <h4 class="text-sm font-light text-white tracking-wide"><?php echo htmlspecialchars($ac['nombre'] . ' ' . $ac['apellido']); ?></h4>
                                    <p class="text-xs text-[#C5A880] font-light mt-0.5"><?php echo htmlspecialchars($ac['tipo_documento']); ?>: <?php echo htmlspecialchars($ac['numero_documento']); ?></p>
                                </div>
                                <form action="index.php" method="POST" onsubmit="return confirm('¿Desea eliminar este acompañante?');">
                                    <input type="hidden" name="action" value="eliminar_acompanante">
                                    <input type="hidden" name="acompanante_id" value="<?php echo $ac['id']; ?>">
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 text-sm p-2 transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div>
</main>

<script>
function switchTab(tab) {
    const btnVuelos = document.getElementById('tab-btn-vuelos');
    const btnAutopilot = document.getElementById('tab-btn-autopilot');
    const tabVuelos = document.getElementById('tab-vuelos');
    const tabAutopilot = document.getElementById('tab-autopilot');

    if (tab === 'vuelos') {
        tabVuelos.classList.remove('hidden');
        tabAutopilot.classList.add('hidden');
        btnVuelos.classList.add('border-[#C5A880]', 'text-[#C5A880]');
        btnVuelos.classList.remove('border-transparent', 'text-slate-400');
        btnAutopilot.classList.remove('border-[#C5A880]', 'text-[#C5A880]');
        btnAutopilot.classList.add('border-transparent', 'text-slate-400');
    } else {
        tabVuelos.classList.add('hidden');
        tabAutopilot.classList.remove('hidden');
        btnAutopilot.classList.add('border-[#C5A880]', 'text-[#C5A880]');
        btnAutopilot.classList.remove('border-transparent', 'text-slate-400');
        btnVuelos.classList.remove('border-[#C5A880]', 'text-[#C5A880]');
        btnVuelos.classList.add('border-transparent', 'text-slate-400');
    }
}
</script>
