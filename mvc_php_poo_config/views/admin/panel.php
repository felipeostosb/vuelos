<?php
// Aseguramos que solo se incluya si existe la sesión de header
require_once __DIR__ . '/../layout/header.php';
?>

<div class="min-h-screen bg-slate-900 text-slate-100 py-8 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- CABECERA PRINCIPAL DEL PANEL -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-slate-800/80 backdrop-blur border border-slate-700/60 rounded-2xl p-6 shadow-xl gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center shadow-lg shadow-amber-500/20 text-slate-950 font-black text-2xl">
                    ⚡
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-white">Panel de Control Administrador</h1>
                        <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/30 text-xs font-semibold rounded-full uppercase tracking-wider">Super Admin</span>
                    </div>
                    <p class="text-slate-400 text-sm mt-1">Gestión integral de vuelos, boletos, usuarios y búsquedas IA en NovAirlines</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="abrirModalCrearUsuario()" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-medium text-sm rounded-xl shadow-lg shadow-blue-500/20 transition duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    <span>Nuevo Usuario / Admin</span>
                </button>
            </div>
        </div>

        <!-- ALERTAS DE ÉXITO O ERROR DE ACCIÓN -->
        <?php if (!empty($_GET['mensaje'])): ?>
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center space-x-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span><?= htmlspecialchars($_GET['mensaje']) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm flex items-center space-x-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <!-- TARJETAS DE MÉTRICAS (KPIS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <!-- Ventas Totales -->
            <div class="bg-slate-800/70 border border-slate-700/60 p-5 rounded-2xl relative overflow-hidden group hover:border-amber-500/40 transition">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ingresos Totales</div>
                <div class="text-2xl font-black text-amber-400 mt-2">S/. <?= number_format($stats['total_ventas'] ?? 0, 2) ?></div>
                <div class="text-xs text-slate-500 mt-1">Suma de reservas vigentes</div>
            </div>
            <!-- Reservas Totales -->
            <div class="bg-slate-800/70 border border-slate-700/60 p-5 rounded-2xl relative overflow-hidden group hover:border-blue-500/40 transition">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Reservas</div>
                <div class="text-2xl font-black text-blue-400 mt-2"><?= $stats['total_reservas'] ?? 0 ?></div>
                <div class="text-xs text-slate-500 mt-1">Boletos emitidos</div>
            </div>
            <!-- Pasajeros Registrados -->
            <div class="bg-slate-800/70 border border-slate-700/60 p-5 rounded-2xl relative overflow-hidden group hover:border-emerald-500/40 transition">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pasajeros</div>
                <div class="text-2xl font-black text-emerald-400 mt-2"><?= $stats['total_pasajeros'] ?? 0 ?></div>
                <div class="text-xs text-slate-500 mt-1">Pasajeros registrados</div>
            </div>
            <!-- Usuarios Registrados -->
            <div class="bg-slate-800/70 border border-slate-700/60 p-5 rounded-2xl relative overflow-hidden group hover:border-indigo-500/40 transition">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Cuentas Registradas</div>
                <div class="text-2xl font-black text-indigo-400 mt-2"><?= $stats['total_usuarios'] ?? 0 ?></div>
                <div class="text-xs text-slate-500 mt-1">Clientes y administradores</div>
            </div>
            <!-- Consultas IA -->
            <div class="bg-slate-800/70 border border-slate-700/60 p-5 rounded-2xl relative overflow-hidden group hover:border-purple-500/40 transition">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Consultas IA</div>
                <div class="text-2xl font-black text-purple-400 mt-2"><?= $stats['total_consultas_ia'] ?? 0 ?></div>
                <div class="text-xs text-slate-500 mt-1">Interacciones con Gemini</div>
            </div>
        </div>

        <!-- PESTAÑAS NAVEGABLES -->
        <div class="border-b border-slate-700/60 flex space-x-6">
            <button onclick="cambiarPestana('reservas')" id="tab-btn-reservas" class="tab-btn pb-3 font-semibold text-sm border-b-2 border-amber-400 text-amber-400 transition flex items-center space-x-2">
                <span>🎫 Reservas y Boletos PDF</span>
                <span class="px-2 py-0.5 text-xs bg-amber-400/10 text-amber-400 rounded-full font-bold"><?= count($reservas) ?></span>
            </button>
            <button onclick="cambiarPestana('usuarios')" id="tab-btn-usuarios" class="tab-btn pb-3 font-semibold text-sm border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-2">
                <span>👥 Gestión de Usuarios</span>
                <span class="px-2 py-0.5 text-xs bg-slate-700 text-slate-300 rounded-full font-bold"><?= count($usuarios) ?></span>
            </button>
            <button onclick="cambiarPestana('ia')" id="tab-btn-ia" class="tab-btn pb-3 font-semibold text-sm border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-2">
                <span>🤖 Búsquedas por IA</span>
                <span class="px-2 py-0.5 text-xs bg-purple-500/10 text-purple-400 rounded-full font-bold"><?= count($consultas_ia) ?></span>
            </button>
        </div>

        <!-- PESTAÑA 1: GESTIÓN DE RESERVAS Y BOLETOS PDF -->
        <div id="tab-content-reservas" class="tab-content space-y-4">
            <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-5 border-b border-slate-700/60 flex justify-between items-center">
                    <h3 class="font-bold text-white text-lg">Listado General de Reservas de Vuelos</h3>
                    <span class="text-xs text-slate-400">Total: <?= count($reservas) ?> reservas activas</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-900/60 text-xs uppercase tracking-wider text-slate-400 font-semibold border-b border-slate-700/60">
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
                        <tbody class="divide-y divide-slate-700/50">
                            <?php if (empty($reservas)): ?>
                                <tr><td colspan="7" class="px-6 py-8 text-center text-slate-500">No hay reservas registradas.</td></tr>
                            <?php else: ?>
                                <?php foreach ($reservas as $res): ?>
                                    <tr class="hover:bg-slate-700/30 transition duration-150">
                                        <td class="px-6 py-4 font-mono font-bold text-amber-400 tracking-wider">
                                            <?= htmlspecialchars($res['pnr']) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-white"><?= htmlspecialchars($res['usuario_nombre'] ?? 'Invitado / Anon') ?></div>
                                            <div class="text-xs text-slate-400"><?= htmlspecialchars($res['usuario_email'] ?? 'Sin correo') ?></div>
                                        </td>
                                        <td class="px-6 py-4 font-medium">
                                            <span class="text-white"><?= htmlspecialchars($res['origen_iata'] ?? 'LIM') ?></span>
                                            <span class="text-slate-500 mx-1">&rarr;</span>
                                            <span class="text-white"><?= htmlspecialchars($res['destino_iata'] ?? 'DEST') ?></span>
                                            <div class="text-xs text-slate-400 font-normal"><?= htmlspecialchars($res['aerolinea_nombre'] ?? 'NovAirlines') ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button onclick='abrirModalPasajeros(<?= json_encode($res['pasajeros']) ?>, "<?= htmlspecialchars($res['pnr']) ?>")' class="px-3 py-1 bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-semibold rounded-lg border border-slate-600 flex items-center space-x-1 transition">
                                                <span>👥 <?= max(1, (int)$res['pasajeros_count']) ?> pasajero(s)</span>
                                                <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-emerald-400">
                                            S/. <?= number_format($res['precio_total'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <form action="index.php?action=admin_cambiar_estado" method="POST" class="inline-block">
                                                <input type="hidden" name="pnr" value="<?= htmlspecialchars($res['pnr']) ?>">
                                                <select name="estado" onchange="this.form.submit()" class="bg-slate-900 border text-xs font-semibold rounded-lg px-2.5 py-1 focus:ring-1 focus:ring-amber-400 border-slate-700
                                                    <?php 
                                                        if ($res['estado'] === 'Checked-in') echo 'text-emerald-400 border-emerald-500/40';
                                                        elseif ($res['estado'] === 'Confirmada') echo 'text-blue-400 border-blue-500/40';
                                                        elseif ($res['estado'] === 'Cancelada') echo 'text-rose-400 border-rose-500/40';
                                                        else echo 'text-amber-400 border-amber-500/40';
                                                    ?>">
                                                    <option value="Pendiente" <?= $res['estado'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                                    <option value="Confirmada" <?= $res['estado'] === 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                                                    <option value="Checked-in" <?= $res['estado'] === 'Checked-in' ? 'selected' : '' ?>>Checked-in</option>
                                                    <option value="Cancelada" <?= $res['estado'] === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="index.php?action=descargar_boleto&pnr=<?= htmlspecialchars($res['pnr']) ?>" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-300 font-semibold text-xs rounded-xl transition">
                                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
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
            <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-5 border-b border-slate-700/60 flex justify-between items-center">
                    <h3 class="font-bold text-white text-lg">Cuentas Registradas y Roles</h3>
                    <span class="text-xs text-slate-400">Total: <?= count($usuarios) ?> usuarios</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-900/60 text-xs uppercase tracking-wider text-slate-400 font-semibold border-b border-slate-700/60">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Nombre Completo</th>
                                <th class="px-6 py-4">Correo Electrónico</th>
                                <th class="px-6 py-4">Rol</th>
                                <th class="px-6 py-4">Compras Efectuadas</th>
                                <th class="px-6 py-4 text-center">Acciones / Eliminar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            <?php foreach ($usuarios as $u): ?>
                                <tr class="hover:bg-slate-700/30 transition duration-150">
                                    <td class="px-6 py-4 text-slate-400 font-mono">#<?= $u['id'] ?></td>
                                    <td class="px-6 py-4 font-semibold text-white"><?= htmlspecialchars($u['nombre']) ?></td>
                                    <td class="px-6 py-4 text-slate-300"><?= htmlspecialchars($u['email']) ?></td>
                                    <td class="px-6 py-4">
                                        <form action="index.php?action=admin_cambiar_rol" method="POST" class="inline-block">
                                            <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                                            <select name="rol" onchange="this.form.submit()" class="bg-slate-900 border text-xs font-semibold rounded-lg px-2.5 py-1 border-slate-700 <?= $u['rol'] === 'admin' ? 'text-amber-400 border-amber-500/40' : 'text-blue-400 border-blue-500/40' ?>">
                                                <option value="cliente" <?= $u['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                                <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-emerald-400">
                                        <?= (int)$u['total_compras'] ?> reserva(s) (S/. <?= number_format($u['total_gastado'], 2) ?>)
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ((int)$u['id'] === (int)($_SESSION['usuario']['id'] ?? 0)): ?>
                                            <span class="text-xs text-slate-500 italic">Cuenta Activa (En sesión)</span>
                                        <?php else: ?>
                                            <button onclick="confirmarEliminarUsuario(<?= $u['id'] ?>, '<?= htmlspecialchars($u['nombre']) ?>')" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 font-medium text-xs rounded-xl transition">
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
            <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl overflow-hidden shadow-xl">
                <div class="p-5 border-b border-slate-700/60 flex justify-between items-center">
                    <h3 class="font-bold text-white text-lg">Histórico de Búsquedas Inteligentes con Gemini AI</h3>
                    <span class="text-xs text-slate-400">Últimas 50 consultas</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-900/60 text-xs uppercase tracking-wider text-slate-400 font-semibold border-b border-slate-700/60">
                            <tr>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Usuario</th>
                                <th class="px-6 py-4">Frase Ingresada (Prompt)</th>
                                <th class="px-6 py-4">Parámetros Extraídos (JSON)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            <?php foreach ($consultas_ia as $ia): ?>
                                <tr class="hover:bg-slate-700/30 transition duration-150">
                                    <td class="px-6 py-4 text-xs text-slate-400 font-mono"><?= htmlspecialchars($ia['fecha_consulta']) ?></td>
                                    <td class="px-6 py-4 font-medium text-white"><?= htmlspecialchars($ia['usuario_nombre'] ?? 'Invitado') ?></td>
                                    <td class="px-6 py-4 italic text-amber-300">"<?= htmlspecialchars($ia['prompt_original']) ?>"</td>
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
<div id="modalPasajeros" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-700 pb-3">
            <h3 class="font-bold text-white text-lg">Pasajeros de la Reserva PNR: <span id="modalPnrTitle" class="text-amber-400 font-mono"></span></h3>
            <button onclick="cerrarModalPasajeros()" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <div id="modalPasajerosBody" class="space-y-3 max-h-80 overflow-y-auto pr-1"></div>
        <div class="pt-2 text-right">
            <button onclick="cerrarModalPasajeros()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl text-sm font-semibold">Cerrar</button>
        </div>
    </div>
</div>

<!-- MODAL: CREAR NUEVO USUARIO / ADMIN -->
<div id="modalCrearUsuario" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-700 pb-3">
            <h3 class="font-bold text-white text-lg">Crear Nuevo Usuario o Admin</h3>
            <button onclick="cerrarModalCrearUsuario()" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="index.php?action=admin_crear_usuario" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nombre Completo</label>
                <input type="text" name="nombre" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:border-amber-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Correo Electrónico</label>
                <input type="email" name="email" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:border-amber-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Contraseña</label>
                <input type="password" name="password" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:border-amber-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Rol Asignado</label>
                <select name="rol" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:border-amber-400 focus:outline-none">
                    <option value="cliente">Cliente Regular</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="cerrarModalCrearUsuario()" class="px-4 py-2 bg-slate-700 text-slate-300 rounded-xl text-sm font-semibold">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-sm font-bold">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS PARA INTERACTIVIDAD Y NAVEGACIÓN -->
<script>
function cambiarPestana(nombrePestana) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-amber-400', 'text-amber-400');
        el.classList.add('border-transparent', 'text-slate-400');
    });

    const targetTab = document.getElementById('tab-content-' + nombrePestana);
    const targetBtn = document.getElementById('tab-btn-' + nombrePestana);
    if (targetTab && targetBtn) {
        targetTab.classList.remove('hidden');
        targetBtn.classList.remove('border-transparent', 'text-slate-400');
        targetBtn.classList.add('border-amber-400', 'text-amber-400');
    }
}

function abrirModalPasajeros(pasajeros, pnr) {
    document.getElementById('modalPnrTitle').innerText = pnr;
    const body = document.getElementById('modalPasajerosBody');
    body.innerHTML = '';

    if (!pasajeros || pasajeros.length === 0) {
        body.innerHTML = '<div class="text-slate-400 text-sm">No se encontraron detalles de pasajeros.</div>';
    } else {
        pasajeros.forEach(p => {
            const div = document.createElement('div');
            div.className = 'p-3 bg-slate-900 border border-slate-700/60 rounded-xl text-sm space-y-1';
            div.innerHTML = `
                <div class="font-bold text-white">${p.nombre || ''} ${p.apellido || ''}</div>
                <div class="text-xs text-slate-400">${p.tipo_documento || 'DNI'}: <span class="text-amber-300 font-mono">${p.numero_documento || 'No especificado'}</span></div>
                <div class="text-xs text-slate-500">Asiento: ${p.asiento || 'Asignado en Check-in'}</div>
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
