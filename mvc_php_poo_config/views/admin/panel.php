<?php
// Aseguramos que solo se incluya si existe la sesión de header
require_once __DIR__ . '/../layout/header.php';
?>

<div class="min-h-screen bg-[#0A1628] text-white py-10 px-4 sm:px-6 lg:px-8 font-sans font-light">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- CABECERA PRINCIPAL DEL PANEL BOUTIQUE -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-[#132238]/80 backdrop-blur border border-[#C5A880]/30 rounded-3xl p-6 shadow-2xl gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-[#C5A880]/15 border border-[#C5A880]/40 flex items-center justify-center text-[#C5A880] text-2xl shadow-lg">
                    ⚡
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-light tracking-wide text-white">Panel de Control Administrador</h1>
                        <span class="px-3 py-1 bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/40 text-[10px] font-light uppercase tracking-widest rounded-full">Super Admin</span>
                    </div>
                    <p class="text-slate-300 text-xs font-light tracking-wide mt-1">Gestión integral de vuelos, boletos, usuarios y búsquedas IA en NovAirlines</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="abrirModalCrearUsuario()" 
                        class="px-5 py-2.5 bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] font-light text-xs uppercase tracking-widest rounded-xl transition duration-300 flex items-center space-x-2 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    <span>Nuevo Usuario / Admin</span>
                </button>
            </div>
        </div>

        <!-- ALERTAS DE ÉXITO O ERROR DE ACCIÓN -->
        <?php if (!empty($_GET['mensaje'])): ?>
            <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-light tracking-wide flex items-center space-x-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span><?= htmlspecialchars($_GET['mensaje']) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 text-xs font-light tracking-wide flex items-center space-x-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <!-- TARJETAS DE MÉTRICAS (KPIS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <!-- Ventas Totales -->
            <div class="bg-[#132238]/60 border border-[#C5A880]/20 p-5 rounded-2xl relative overflow-hidden group hover:border-[#C5A880]/60 transition duration-300">
                <div class="text-[10px] font-light text-[#C5A880] uppercase tracking-widest">Ingresos Totales</div>
                <div class="text-2xl font-light text-[#C5A880] mt-2 tracking-wider">S/. <?= number_format($stats['total_ventas'] ?? 0, 2) ?></div>
                <div class="text-xs text-slate-400 font-light mt-1">Reservas vigentes</div>
            </div>
            <!-- Reservas Totales -->
            <div class="bg-[#132238]/60 border border-[#C5A880]/20 p-5 rounded-2xl relative overflow-hidden group hover:border-blue-400/60 transition duration-300">
                <div class="text-[10px] font-light text-slate-300 uppercase tracking-widest">Total Reservas</div>
                <div class="text-2xl font-light text-blue-400 mt-2 tracking-wider"><?= $stats['total_reservas'] ?? 0 ?></div>
                <div class="text-xs text-slate-400 font-light mt-1">Boletos emitidos</div>
            </div>
            <!-- Pasajeros Registrados -->
            <div class="bg-[#132238]/60 border border-[#C5A880]/20 p-5 rounded-2xl relative overflow-hidden group hover:border-emerald-400/60 transition duration-300">
                <div class="text-[10px] font-light text-slate-300 uppercase tracking-widest">Total Pasajeros</div>
                <div class="text-2xl font-light text-emerald-400 mt-2 tracking-wider"><?= $stats['total_pasajeros'] ?? 0 ?></div>
                <div class="text-xs text-slate-400 font-light mt-1">Pasajeros registrados</div>
            </div>
            <!-- Usuarios Registrados -->
            <div class="bg-[#132238]/60 border border-[#C5A880]/20 p-5 rounded-2xl relative overflow-hidden group hover:border-indigo-400/60 transition duration-300">
                <div class="text-[10px] font-light text-slate-300 uppercase tracking-widest">Cuentas Registradas</div>
                <div class="text-2xl font-light text-indigo-400 mt-2 tracking-wider"><?= $stats['total_usuarios'] ?? 0 ?></div>
                <div class="text-xs text-slate-400 font-light mt-1">Clientes y admins</div>
            </div>
            <!-- Consultas IA -->
            <div class="bg-[#132238]/60 border border-[#C5A880]/20 p-5 rounded-2xl relative overflow-hidden group hover:border-purple-400/60 transition duration-300">
                <div class="text-[10px] font-light text-slate-300 uppercase tracking-widest">Consultas IA</div>
                <div class="text-2xl font-light text-purple-400 mt-2 tracking-wider"><?= $stats['total_consultas_ia'] ?? 0 ?></div>
                <div class="text-xs text-slate-400 font-light mt-1">Consultas Gemini</div>
            </div>
        </div>

        <?php $modo_actual_ofertas = obtener_modo_ofertas(); ?>
        <!-- CONFIGURACIÓN DE OFERTAS DEL SITIO -->
        <div class="bg-[#132238]/80 backdrop-blur border border-[#C5A880]/30 rounded-3xl p-6 shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-[#C5A880] text-lg">⚙️</span>
                        <h2 class="text-lg font-light text-white tracking-wide">Configuración de Ofertas y Promociones</h2>
                    </div>
                    <p class="text-xs text-slate-300 font-light mt-1">Seleccione el origen de las ofertas mostradas en la portada y la sección de Promociones</p>
                </div>

                <form action="index.php?action=admin_guardar_modo_ofertas" method="POST" class="flex flex-wrap items-center gap-3">
                    <label class="flex items-center space-x-2 bg-[#0A1628] border border-[#C5A880]/30 px-4 py-2.5 rounded-xl cursor-pointer hover:border-[#C5A880] transition">
                        <input type="radio" name="modo_ofertas" value="peru_destacadas" <?= $modo_actual_ofertas === 'peru_destacadas' ? 'checked' : '' ?> class="accent-[#C5A880]">
                        <span class="text-xs font-light text-white">🇵🇪 3 Ofertas Perú (Tarapoto, Cusco, Arequipa)</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 bg-[#0A1628] border border-[#C5A880]/30 px-4 py-2.5 rounded-xl cursor-pointer hover:border-[#C5A880] transition">
                        <input type="radio" name="modo_ofertas" value="duffel_api" <?= $modo_actual_ofertas === 'duffel_api' ? 'checked' : '' ?> class="accent-[#C5A880]">
                        <span class="text-xs font-light text-white">⚡ Ofertas Live Duffel API</span>
                    </label>

                    <button type="submit" class="px-5 py-2.5 bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] font-light text-xs uppercase tracking-widest rounded-xl transition duration-300 shadow-md">
                        Guardar
                    </button>
                </form>
            </div>
        </div>

        <!-- GESTOR DE SUBIDA DE FOTOGRAFÍAS DE DESTINOS -->
        <div class="bg-[#132238]/80 backdrop-blur border border-[#C5A880]/30 rounded-3xl p-6 shadow-xl mt-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-[#C5A880] text-lg">🖼️</span>
                        <h2 class="text-lg font-light text-white tracking-wide">Gestor de Fotografías de Destinos</h2>
                    </div>
                    <p class="text-xs text-slate-300 font-light mt-1">Suba y reemplace fotografías (horizontales o verticales) para los destinos de Perú. Se adaptan automáticamente al diseño.</p>
                </div>

                <form action="index.php?action=admin_subir_imagen_oferta" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                    <select name="destino_slug" required class="bg-[#0A1628] border border-[#C5A880]/30 text-xs font-light text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-[#C5A880]">
                        <option value="" disabled selected>-- Seleccione Destino --</option>
                        <option value="tarapoto">🌴 Tarapoto (TPP)</option>
                        <option value="cusco">🏔️ Cusco (CUZ)</option>
                        <option value="arequipa">🌋 Arequipa (AQP)</option>
                    </select>

                    <input type="file" name="imagen_destino" accept="image/jpeg,image/png,image/webp" required class="text-xs text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-light file:bg-[#C5A880]/15 file:text-[#C5A880] hover:file:bg-[#C5A880]/25 cursor-pointer">

                    <button type="submit" class="px-5 py-2.5 bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] font-light text-xs uppercase tracking-widest rounded-xl transition duration-300 shadow-md flex items-center space-x-1.5">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Subir Imagen</span>
                    </button>
                </form>
            </div>
            
            <!-- VISTAS PREVIAS DE LAS FOTOS ACTUALES -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-4 border-t border-[#C5A880]/15">
                <?php foreach (['Tarapoto' => 'tarapoto', 'Cusco' => 'cusco', 'Arequipa' => 'arequipa'] as $nombre_c => $slug_c): ?>
                    <?php $img_actual = obtener_imagen_destino($nombre_c); ?>
                    <div class="flex items-center space-x-3 bg-[#0A1628]/60 p-2.5 rounded-xl border border-[#C5A880]/15">
                        <div class="w-12 h-14 rounded-lg overflow-hidden bg-[#132238] flex-shrink-0 flex items-center justify-center border border-[#C5A880]/20">
                            <?php if ($img_actual): ?>
                                <img src="<?= htmlspecialchars($img_actual) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fa-solid fa-plane-departure text-slate-500 text-xs"></i>
                            <?php endif; ?>
                        </div>
                        <div class="overflow-hidden">
                            <span class="block text-xs font-light text-white truncate"><?= $nombre_c ?></span>
                            <span class="block text-[10px] text-slate-400 font-light truncate"><?= $img_actual ? 'Foto Personalizada' : 'Sin foto (Ícono)' ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PESTAÑAS NAVEGABLES -->
        <div class="border-b border-[#C5A880]/20 flex space-x-6">
            <button onclick="cambiarPestana('reservas')" id="tab-btn-reservas" class="tab-btn pb-3 font-light text-xs uppercase tracking-widest border-b-2 border-[#C5A880] text-[#C5A880] transition flex items-center space-x-2">
                <span>🎫 Reservas y Boletos PDF</span>
                <span class="px-2 py-0.5 text-[10px] bg-[#C5A880]/15 text-[#C5A880] rounded-full font-light"><?= count($reservas) ?></span>
            </button>
            <button onclick="cambiarPestana('usuarios')" id="tab-btn-usuarios" class="tab-btn pb-3 font-light text-xs uppercase tracking-widest border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center space-x-2">
                <span>👥 Gestión de Usuarios</span>
                <span class="px-2 py-0.5 text-[10px] bg-slate-800 text-slate-300 rounded-full font-light"><?= count($usuarios) ?></span>
            </button>
            <button onclick="cambiarPestana('ia')" id="tab-btn-ia" class="tab-btn pb-3 font-light text-xs uppercase tracking-widest border-b-2 border-transparent text-slate-400 hover:text-white transition flex items-center space-x-2">
                <span>🤖 Búsquedas por IA</span>
                <span class="px-2 py-0.5 text-[10px] bg-purple-500/15 text-purple-400 rounded-full font-light"><?= count($consultas_ia) ?></span>
            </button>
        </div>

        <!-- PESTAÑA 1: GESTIÓN DE RESERVAS Y BOLETOS PDF -->
        <div id="tab-content-reservas" class="tab-content space-y-4">
            <div class="bg-[#132238]/60 border border-[#C5A880]/20 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-5 border-b border-[#C5A880]/15 flex justify-between items-center">
                    <h3 class="font-light text-white text-base tracking-wide">Listado General de Reservas</h3>
                    <span class="text-xs text-slate-400 font-light">Total: <?= count($reservas) ?> reservas activas</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300 font-light">
                        <thead class="bg-[#0A1628]/80 text-[11px] uppercase tracking-widest text-[#C5A880] font-light border-b border-[#C5A880]/20">
                            <tr>
                                <th class="px-6 py-4">PNR</th>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Ruta</th>
                                <th class="px-6 py-4">Pasajeros</th>
                                <th class="px-6 py-4">Monto Total</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4 text-center">Acciones / Boleto PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#C5A880]/10">
                            <?php if (empty($reservas)): ?>
                                <tr><td colspan="7" class="px-6 py-8 text-center text-slate-500">No hay reservas registradas.</td></tr>
                            <?php else: ?>
                                <?php foreach ($reservas as $res): ?>
                                    <tr class="hover:bg-white/5 transition duration-150">
                                        <td class="px-6 py-4 font-mono font-light text-[#C5A880] tracking-widest">
                                            <?= htmlspecialchars($res['pnr']) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-light text-white"><?= htmlspecialchars($res['usuario_nombre'] ?? 'Invitado / Anon') ?></div>
                                            <div class="text-[11px] text-slate-400"><?= htmlspecialchars($res['usuario_email'] ?? 'Sin correo') ?></div>
                                        </td>
                                        <td class="px-6 py-4 font-light">
                                            <span class="text-white"><?= htmlspecialchars($res['origen_iata'] ?? 'LIM') ?></span>
                                            <span class="text-slate-500 mx-1">&rarr;</span>
                                            <span class="text-white"><?= htmlspecialchars($res['destino_iata'] ?? 'DEST') ?></span>
                                            <div class="text-[11px] text-slate-400 font-light"><?= htmlspecialchars($res['aerolinea_nombre'] ?? 'NovAirlines') ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button onclick='abrirModalPasajeros(<?= json_encode($res['pasajeros']) ?>, "<?= htmlspecialchars($res['pnr']) ?>")' 
                                                    class="px-3 py-1 bg-[#0A1628] hover:bg-slate-800 text-slate-200 text-xs font-light rounded-lg border border-[#C5A880]/30 flex items-center space-x-1 transition">
                                                <span>👥 <?= max(1, (int)$res['pasajeros_count']) ?> pasajero(s)</span>
                                                <svg class="w-3 h-3 text-[#C5A880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 font-light text-emerald-400">
                                            S/. <?= number_format($res['precio_total'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <form action="index.php?action=admin_cambiar_estado" method="POST" class="inline-block">
                                                <input type="hidden" name="pnr" value="<?= htmlspecialchars($res['pnr']) ?>">
                                                <select name="estado" onchange="this.form.submit()" class="bg-[#0A1628] border text-xs font-light rounded-lg px-2.5 py-1 focus:ring-1 focus:ring-[#C5A880] border-[#C5A880]/30
                                                    <?php 
                                                        if ($res['estado'] === 'Checked-in') echo 'text-emerald-400 border-emerald-500/40';
                                                        elseif ($res['estado'] === 'Confirmada') echo 'text-blue-400 border-blue-500/40';
                                                        elseif ($res['estado'] === 'Cancelada') echo 'text-rose-400 border-rose-500/40';
                                                        else echo 'text-[#C5A880] border-[#C5A880]/40';
                                                    ?>">
                                                    <option value="Pendiente" <?= $res['estado'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                                    <option value="Confirmada" <?= $res['estado'] === 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                                                    <option value="Checked-in" <?= $res['estado'] === 'Checked-in' ? 'selected' : '' ?>>Checked-in</option>
                                                    <option value="Cancelada" <?= $res['estado'] === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="index.php?action=descargar_boleto&pnr=<?= htmlspecialchars($res['pnr']) ?>" 
                                               class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-[#C5A880]/10 hover:bg-[#C5A880]/20 border border-[#C5A880]/30 text-[#C5A880] font-light text-xs uppercase tracking-wider rounded-xl transition">
                                                <svg class="w-4 h-4 text-[#C5A880]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span>Descargar PDF</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PESTAÑA 2: GESTIÓN DE USUARIOS Y ELIMINACIÓN SEGURA -->
        <div id="tab-content-usuarios" class="tab-content hidden space-y-4">
            <div class="bg-[#132238]/60 border border-[#C5A880]/20 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-5 border-b border-[#C5A880]/15 flex justify-between items-center">
                    <h3 class="font-light text-white text-base tracking-wide">Cuentas Registradas y Roles</h3>
                    <span class="text-xs text-slate-400 font-light">Total: <?= count($usuarios) ?> usuarios</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300 font-light">
                        <thead class="bg-[#0A1628]/80 text-[11px] uppercase tracking-widest text-[#C5A880] font-light border-b border-[#C5A880]/20">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Nombre Completo</th>
                                <th class="px-6 py-4">Correo Electrónico</th>
                                <th class="px-6 py-4">Rol</th>
                                <th class="px-6 py-4">Compras Efectuadas</th>
                                <th class="px-6 py-4 text-center">Acciones / Eliminar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#C5A880]/10">
                            <?php foreach ($usuarios as $u): ?>
                                <tr class="hover:bg-white/5 transition duration-150">
                                    <td class="px-6 py-4 text-slate-400 font-mono">#<?= $u['id'] ?></td>
                                    <td class="px-6 py-4 font-light text-white"><?= htmlspecialchars($u['nombre']) ?></td>
                                    <td class="px-6 py-4 text-slate-300"><?= htmlspecialchars($u['email']) ?></td>
                                    <td class="px-6 py-4">
                                        <form action="index.php?action=admin_cambiar_rol" method="POST" class="inline-block">
                                            <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                                            <select name="rol" onchange="this.form.submit()" class="bg-[#0A1628] border text-xs font-light rounded-lg px-2.5 py-1 border-[#C5A880]/30 <?= $u['rol'] === 'admin' ? 'text-[#C5A880]' : 'text-blue-400' ?>">
                                                <option value="cliente" <?= $u['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                                <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 font-light text-emerald-400">
                                        <?= (int)$u['total_compras'] ?> reserva(s) (S/. <?= number_format($u['total_gastado'], 2) ?>)
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ((int)$u['id'] === (int)($_SESSION['usuario']['id'] ?? 0)): ?>
                                            <span class="text-xs text-slate-500 italic font-light">En Sesión</span>
                                        <?php else: ?>
                                            <button onclick="confirmarEliminarUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['nombre']) ?>')" 
                                                    class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 font-light text-xs rounded-xl transition">
                                                🗑️ Eliminar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PESTAÑA 3: BÚSQUEDAS POR INTELIGENCIA ARTIFICIAL -->
        <div id="tab-content-ia" class="tab-content hidden space-y-4">
            <div class="bg-[#132238]/60 border border-[#C5A880]/20 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-5 border-b border-[#C5A880]/15 flex justify-between items-center">
                    <h3 class="font-light text-white text-base tracking-wide">Histórico de Búsquedas con Gemini AI</h3>
                    <span class="text-xs text-slate-400 font-light">Últimas 50 consultas</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300 font-light">
                        <thead class="bg-[#0A1628]/80 text-[11px] uppercase tracking-widest text-[#C5A880] font-light border-b border-[#C5A880]/20">
                            <tr>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Usuario</th>
                                <th class="px-6 py-4">Prompt Original</th>
                                <th class="px-6 py-4">Parámetros Extraídos (JSON)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#C5A880]/10">
                            <?php foreach ($consultas_ia as $ia): ?>
                                <tr class="hover:bg-white/5 transition duration-150">
                                    <td class="px-6 py-4 text-xs text-slate-400 font-mono"><?= htmlspecialchars($ia['fecha_consulta']) ?></td>
                                    <td class="px-6 py-4 font-light text-white"><?= htmlspecialchars($ia['usuario_nombre'] ?? 'Invitado') ?></td>
                                    <td class="px-6 py-4 italic text-[#C5A880]">"<?= htmlspecialchars($ia['prompt_original']) ?>"</td>
                                    <td class="px-6 py-4 font-mono text-xs text-emerald-400">
                                        <?= htmlspecialchars($ia['parametros_extraidos']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL: DETALLE DE PASAJEROS -->
<div id="modalPasajeros" class="fixed inset-0 bg-[#0A1628]/90 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-[#132238] border border-[#C5A880]/30 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 text-white font-light">
        <div class="flex justify-between items-center border-b border-[#C5A880]/20 pb-3">
            <h3 class="font-light text-white text-base tracking-wide">Pasajeros de Reserva PNR: <span id="modalPnrTitle" class="text-[#C5A880] font-mono"></span></h3>
            <button onclick="cerrarModalPasajeros()" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <div id="modalPasajerosBody" class="space-y-3 max-h-80 overflow-y-auto pr-1"></div>
        <div class="pt-2 text-right">
            <button onclick="cerrarModalPasajeros()" class="px-4 py-2 bg-[#0A1628] border border-[#C5A880]/30 text-white rounded-xl text-xs font-light">Cerrar</button>
        </div>
    </div>
</div>

<!-- MODAL: CREAR NUEVO USUARIO / ADMIN -->
<div id="modalCrearUsuario" class="fixed inset-0 bg-[#0A1628]/90 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-[#132238] border border-[#C5A880]/30 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-white font-light">
        <div class="flex justify-between items-center border-b border-[#C5A880]/20 pb-3">
            <h3 class="font-light text-white text-base tracking-wide">Crear Nuevo Usuario o Admin</h3>
            <button onclick="cerrarModalCrearUsuario()" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="index.php?action=admin_crear_usuario" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-1">Nombre Completo</label>
                <input type="text" name="nombre" required class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs text-white focus:border-[#C5A880] focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-1">Correo Electrónico</label>
                <input type="email" name="email" required class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs text-white focus:border-[#C5A880] focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-1">Contraseña</label>
                <input type="password" name="password" required class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs text-white focus:border-[#C5A880] focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-1">Rol Asignado</label>
                <select name="rol" class="w-full bg-[#0A1628] border border-[#C5A880]/30 rounded-xl px-3 py-2 text-xs text-white focus:border-[#C5A880] focus:outline-none">
                    <option value="cliente">Cliente Regular</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="cerrarModalCrearUsuario()" class="px-4 py-2 bg-[#0A1628] border border-[#C5A880]/30 text-slate-300 rounded-xl text-xs font-light">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-[#C5A880] hover:bg-[#b4966e] text-[#0A1628] rounded-xl text-xs font-light uppercase tracking-widest">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS PARA INTERACTIVIDAD Y NAVEGACIÓN -->
<script>
function cambiarPestana(nombrePestana) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-[#C5A880]', 'text-[#C5A880]');
        el.classList.add('border-transparent', 'text-slate-400');
    });

    const targetTab = document.getElementById('tab-content-' + nombrePestana);
    const targetBtn = document.getElementById('tab-btn-' + nombrePestana);
    if (targetTab && targetBtn) {
        targetTab.classList.remove('hidden');
        targetBtn.classList.remove('border-transparent', 'text-slate-400');
        targetBtn.classList.add('border-[#C5A880]', 'text-[#C5A880]');
    }
}

function abrirModalPasajeros(pasajeros, pnr) {
    document.getElementById('modalPnrTitle').innerText = pnr;
    const body = document.getElementById('modalPasajerosBody');
    body.innerHTML = '';

    if (!pasajeros || pasajeros.length === 0) {
        body.innerHTML = '<div class="text-slate-400 text-xs font-light">No se encontraron detalles de pasajeros.</div>';
    } else {
        pasajeros.forEach(p => {
            const div = document.createElement('div');
            div.className = 'p-3 bg-[#0A1628] border border-[#C5A880]/20 rounded-xl text-xs space-y-1 font-light';
            div.innerHTML = `
                <div class="font-light text-white">${p.nombre || ''} ${p.apellido || ''}</div>
                <div class="text-xs text-slate-400">${p.tipo_documento || 'DNI'}: <span class="text-[#C5A880] font-mono">${p.numero_documento || 'No especificado'}</span></div>
                <div class="text-xs text-slate-400">Asiento: ${p.asiento || 'Asignado en Check-in'}</div>
            `;
            body.appendChild(div);
        });
    }
    document.getElementById('modalPasajeros').classList.remove('hidden');
}

function cerrarModalPasajeros() {
    document.getElementById('modalPasajeros').classList.add('hidden');
}

function abrirModalCrearUsuario() {
    document.getElementById('modalCrearUsuario').classList.remove('hidden');
}

function cerrarModalCrearUsuario() {
    document.getElementById('modalCrearUsuario').classList.add('hidden');
}

function confirmarEliminarUsuario(id, nombre) {
    if (confirm(`¿Estás seguro de eliminar permanentemente al usuario "${nombre}"?\n\nLas reservas de este usuario no se borrarán, pero quedarán desvinculadas de forma segura.`)) {
        window.location.href = `index.php?action=admin_eliminar_usuario&id=${id}`;
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
