<?php
/**
 * VISTA: BUSCADOR DE LUGARES TURÍSTICOS Y JOYAS OCULTAS
 * Recibe $resultado_lugares desde index.php (listar_lugares_turisticos()).
 */

$datos = $resultado_lugares ?? listar_lugares_turisticos();
$lugares = $datos['lugares'] ?? [];
$destino = $datos['destino'] ?? null;
$destinos = $datos['destinos'] ?? [];
$sugerencias = $datos['sugerencias'] ?? [];
$estadisticas = $datos['estadisticas'] ?? [];
$proveedor = $datos['proveedor'] ?? 'ninguno';
$por_fuente = $estadisticas['por_fuente'] ?? [];

$tipos_info = [
    'arqueologico' => ['label' => 'Arqueológico', 'icono' => '🏛️'],
    'naturaleza'   => ['label' => 'Naturaleza',   'icono' => '🌿'],
    'mirador'      => ['label' => 'Mirador',      'icono' => '🌅'],
    'playa'        => ['label' => 'Playa',        'icono' => '🏖️'],
    'museo'        => ['label' => 'Museo',        'icono' => '🖼️'],
    'cultural'     => ['label' => 'Cultural',     'icono' => '🎭'],
    'aventura'     => ['label' => 'Aventura',     'icono' => '🧭'],
];

$badges_info = [
    'joya'          => ['texto' => 'Joya oculta',              'clases' => 'bg-[#C5A880]/15 text-[#C5A880] border-[#C5A880]/45', 'icono' => '✦'],
    'imperdible'    => ['texto' => 'Imperdible (concurrido)',  'clases' => 'bg-amber-400/10 text-amber-300 border-amber-300/40',  'icono' => '★'],
    'recomendado'   => ['texto' => 'Recomendado',              'clases' => 'bg-emerald-400/10 text-emerald-300 border-emerald-300/40', 'icono' => '✓'],
    'pocas_resenas' => ['texto' => 'Pocas reseñas',            'clases' => 'bg-slate-500/15 text-slate-300 border-slate-400/30',  'icono' => '·'],
    'sin_puntaje'   => ['texto' => 'Sin puntaje todavía',      'clases' => 'bg-slate-500/15 text-slate-400 border-slate-500/30',  'icono' => '?'],
    'normal'        => ['texto' => 'Normal',                   'clases' => 'bg-slate-500/15 text-slate-300 border-slate-400/30',  'icono' => '·'],
];

$proveedor_info = [
    'google'   => ['texto' => 'Datos en vivo · Google Places',        'clases' => 'text-emerald-300 border-emerald-400/40 bg-emerald-400/10'],
    'geoapify' => ['texto' => 'Datos en vivo · Geoapify',             'clases' => 'text-sky-300 border-sky-400/40 bg-sky-400/10'],
    'tomtom'   => ['texto' => 'Datos en vivo · TomTom',               'clases' => 'text-indigo-300 border-indigo-400/40 bg-indigo-400/10'],
    'ambos'    => ['texto' => 'Datos en vivo · Geoapify + TomTom',    'clases' => 'text-sky-300 border-sky-400/40 bg-sky-400/10'],
    'ninguno'  => ['texto' => 'Sin proveedor configurado',            'clases' => 'text-rose-300 border-rose-400/40 bg-rose-400/10'],
];
$proveedor_actual = $proveedor_info[$proveedor] ?? $proveedor_info['ninguno'];

$fuentes_info = [
    'google'   => 'Google',
    'geoapify' => 'Geoapify',
    'tomtom'   => 'TomTom',
];

$estrellas = function ($rating) {
    $llenas = (int)max(0, min(5, round((float)$rating)));
    return str_repeat('★', $llenas) . str_repeat('☆', 5 - $llenas);
};

$tipos_presentes = [];
foreach ($lugares as $lugar) {
    $tipo = $lugar['tipo'] ?? 'cultural';
    if (!isset($tipos_presentes[$tipo])) {
        $tipos_presentes[$tipo] = $tipos_info[$tipo] ?? ['label' => ucfirst($tipo), 'icono' => '✦'];
    }
}

$chips_slugs = ['lima', 'cusco', 'arequipa', 'puno', 'chachapoyas', 'paris', 'tokio', 'nueva-york', 'roma', 'ciudad-de-mexico'];
$chips_destinos = [];
foreach ($destinos as $catalogo_destino) {
    if (in_array($catalogo_destino['slug'], $chips_slugs, true)) {
        $chips_destinos[$catalogo_destino['slug']] = $catalogo_destino;
    }
}

$total_mostrado = count($lugares);
$joyas = (int)($estadisticas['joyas'] ?? 0);
$con_rating = (int)($estadisticas['con_rating'] ?? 0);
$paises = (int)($estadisticas['paises'] ?? 0);

$titulo_resultado = $destino
    ? ($con_rating > 0 ? 'Joyas ocultas en ' . $destino['ciudad'] : 'Lugares en ' . $destino['ciudad'])
    : ($datos['q'] !== '' ? 'Resultados para "' . $datos['q'] . '"' : 'Lugares de Perú y el mundo');
?>

<main class="bg-[#0A1628] min-h-screen pb-24 text-white font-sans">

    <!-- ENCABEZADO HERO -->
    <section class="relative bg-gradient-to-b from-[#0A1628] via-[#132238] to-[#0A1628] border-b border-[#C5A880]/15 py-16 md:py-20 px-6 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#C5A880]/15 via-transparent to-transparent pointer-events-none"></div>

        <div class="max-w-5xl mx-auto text-center relative z-10 space-y-5">
            <div class="flex flex-wrap items-center justify-center gap-2">
                <span class="px-5 py-2 bg-[#C5A880]/10 text-[#C5A880] border border-[#C5A880]/30 text-[11px] font-light tracking-[0.25em] uppercase rounded-full inline-block">
                    Lugares reales, sin relleno
                </span>
                <span class="px-3 py-2 border text-[10px] font-light tracking-[0.18em] uppercase rounded-full inline-flex items-center gap-1.5 <?= $proveedor_actual['clases'] ?>">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    <?= htmlspecialchars($proveedor_actual['texto']) ?>
                </span>
            </div>

            <h1 class="text-3xl md:text-5xl font-light tracking-[0.05em] text-white leading-tight">
                Lugares con alma,<br class="md:hidden"> lejos de las multitudes
            </h1>
            <p class="text-[#C5A880]/80 text-sm md:text-base max-w-2xl mx-auto font-light tracking-wide leading-relaxed">
                Buscamos puntos de interés reales en Perú y el mundo. Cuando la fuente no entrega
                puntuaciones de usuarios, lo decimos claramente en lugar de inventarlas.
            </p>

            <!-- BUSCADOR -->
            <form method="GET" action="index.php" class="max-w-2xl mx-auto mt-8">
                <input type="hidden" name="action" value="lugares">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <label for="inputLugares" class="sr-only">Buscar ciudad o país</label>
                        <input type="text"
                               id="inputLugares"
                               name="q"
                               list="destinosSugeridos"
                               value="<?= htmlspecialchars($datos['q'] ?? '') ?>"
                               placeholder="Cusco, París, Perú, Japón..."
                               autocomplete="off"
                               class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 focus:border-[#C5A880] rounded-2xl py-4 pl-12 pr-4 text-sm font-light text-white placeholder:text-slate-500 focus:outline-none focus:ring-1 focus:ring-[#C5A880]/40 transition-all shadow-2xl tracking-wide">
                        <svg class="w-5 h-5 text-[#C5A880] absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <datalist id="destinosSugeridos">
                            <?php foreach ($destinos as $sugerido): ?>
                                <option value="<?= htmlspecialchars($sugerido['ciudad'] . ', ' . $sugerido['pais']) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <button type="submit"
                            class="px-7 py-4 bg-[#C5A880] hover:bg-[#d4bb96] text-[#0A1628] font-normal text-xs uppercase tracking-[0.18em] rounded-2xl transition-all duration-300 shadow-lg shadow-[#C5A880]/20">
                        Buscar
                    </button>
                </div>
            </form>

            <!-- ACCESOS RÁPIDOS -->
            <div class="flex flex-wrap items-center justify-center gap-2 pt-3">
                <span class="text-[10px] uppercase tracking-[0.2em] text-slate-500 mr-1">Explorar:</span>
                <?php foreach ($chips_destinos as $chip): ?>
                    <a href="index.php?action=lugares&destino=<?= urlencode($chip['slug']) ?>"
                       class="px-3 py-1.5 rounded-full text-[10px] font-light uppercase tracking-[0.14em] border transition-all duration-300 <?= ($destino && $destino['slug'] === $chip['slug']) ? 'bg-[#C5A880] text-[#0A1628] border-[#C5A880]' : 'bg-[#132238]/60 text-slate-300 border-[#C5A880]/20 hover:border-[#C5A880]/60 hover:text-white' ?>">
                        <?= htmlspecialchars($chip['ciudad']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- RESUMEN Y AVISO DE FUENTE -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-[#C5A880]/15 pb-6">
            <div>
                <p class="text-[10px] uppercase tracking-[0.22em] text-[#C5A880]/70 mb-1">Resultados</p>
                <h2 class="text-xl md:text-2xl font-light tracking-wide text-white"><?= htmlspecialchars($titulo_resultado) ?></h2>
                <p class="text-xs text-slate-400 font-light mt-1">
                    <?= $total_mostrado ?> <?= $total_mostrado === 1 ? 'lugar' : 'lugares' ?>
                    <?php if ($con_rating > 0): ?>
                        · <?= $joyas ?> <?= $joyas === 1 ? 'joya oculta' : 'joyas ocultas' ?>
                    <?php endif; ?>
                    · <?= $paises ?> <?= $paises === 1 ? 'país' : 'países' ?>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 text-[11px] font-light">
                <span class="px-3 py-1.5 rounded-full border border-[#C5A880]/20 bg-[#132238]/60 text-slate-300">
                    <span class="text-[#C5A880]"><?= $con_rating ?></span> con puntaje
                </span>
                <?php foreach ($por_fuente as $fuente_slug => $fuente_total): ?>
                    <span class="px-3 py-1.5 rounded-full border border-[#C5A880]/20 bg-[#132238]/60 text-slate-300">
                        <?= htmlspecialchars($fuentes_info[$fuente_slug] ?? ucfirst($fuente_slug)) ?>
                        <span class="text-[#C5A880]"><?= (int)$fuente_total ?></span>
                    </span>
                <?php endforeach; ?>
                <span class="px-3 py-1.5 rounded-full border border-[#C5A880]/20 bg-[#132238]/60 text-slate-300">
                    <span class="text-[#C5A880]"><?= (int)($estadisticas['llamadas_hoy'] ?? 0) ?></span>/<?= (int)($estadisticas['limite_diario'] ?? 100) ?> consultas API hoy
                </span>
                <?php if (!empty($datos['q']) || $destino): ?>
                    <a href="index.php?action=lugares" class="px-3 py-1.5 rounded-full border border-[#C5A880]/30 text-[#C5A880] hover:bg-[#C5A880] hover:text-[#0A1628] transition-all">
                        Limpiar búsqueda
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($datos['aviso'])): ?>
            <div class="mt-4 flex items-start gap-3 rounded-2xl border border-[#C5A880]/20 bg-[#132238]/50 px-4 py-3 text-xs font-light text-slate-300 tracking-wide">
                <i class="fa-solid fa-circle-info text-[#C5A880] mt-0.5"></i>
                <p><?= htmlspecialchars($datos['aviso']) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($total_mostrado > 0 && $con_rating === 0): ?>
            <?php
            $fuentes_usadas = array_keys($por_fuente);
            $fuentes_txt = count($fuentes_usadas) > 0
                ? implode(' y ', array_map(fn($f) => $fuentes_info[$f] ?? ucfirst((string)$f), $fuentes_usadas))
                : 'Las fuentes consultadas';
            ?>
            <div class="mt-4 flex items-start gap-3 rounded-2xl border border-sky-400/25 bg-sky-400/5 px-4 py-3 text-xs font-light text-slate-300 tracking-wide">
                <i class="fa-solid fa-circle-info text-sky-300 mt-0.5"></i>
                <p>
                    <?= htmlspecialchars($fuentes_txt) ?> entregan lugares reales pero <span class="text-sky-300">no puntuaciones de usuarios</span>,
                    así que no se muestra ninguna nota inventada: los resultados van ordenados por cercanía al centro.
                    Para tener estrellas reales hay que activar <span class="text-sky-300">Google Places</span> en el archivo <code>.env</code>.
                </p>
            </div>
        <?php endif; ?>

        <?php if (count($sugerencias) > 0): ?>
            <div class="mt-6 rounded-2xl border border-[#C5A880]/20 bg-[#132238]/50 p-5">
                <p class="text-[10px] uppercase tracking-[0.22em] text-[#C5A880]/70 mb-3">Elige una ciudad</p>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($sugerencias as $sugerido): ?>
                        <a href="index.php?action=lugares&destino=<?= urlencode($sugerido['slug']) ?>"
                           class="px-3 py-1.5 rounded-full text-[10px] font-light uppercase tracking-[0.14em] border border-[#C5A880]/25 bg-[#0A1628]/60 text-slate-300 hover:border-[#C5A880]/70 hover:text-white transition-all">
                            <?= htmlspecialchars($sugerido['ciudad']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- FILTROS -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8" aria-label="Filtros de lugares">
        <div class="bg-[#132238]/40 border border-[#C5A880]/15 rounded-2xl p-4 md:p-5 flex flex-col gap-4">

            <div class="flex flex-wrap items-center gap-2" id="filtrosTipo">
                <button type="button" data-tipo="todos" class="btn-tipo px-4 py-2 rounded-full text-[10px] font-light uppercase tracking-[0.16em] transition-all duration-300 bg-[#C5A880] text-[#0A1628] border border-[#C5A880]">
                    🌍 Todos
                </button>
                <?php foreach ($tipos_presentes as $tipo_slug => $tipo_dato): ?>
                    <button type="button" data-tipo="<?= htmlspecialchars($tipo_slug) ?>" class="btn-tipo px-4 py-2 rounded-full text-[10px] font-light uppercase tracking-[0.16em] transition-all duration-300 bg-[#0A1628]/70 text-slate-300 border border-[#C5A880]/20 hover:border-[#C5A880]/60 hover:text-white">
                        <?= $tipo_dato['icono'] ?> <?= htmlspecialchars($tipo_dato['label']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-3 border-t border-[#C5A880]/10">
                <?php if ($con_rating > 0): ?>
                    <div class="flex items-center gap-2">
                        <label for="filtroRating" class="text-[10px] uppercase tracking-[0.18em] text-slate-400">Puntaje mínimo</label>
                        <select id="filtroRating" class="bg-[#0A1628] border border-[#C5A880]/25 focus:border-[#C5A880] rounded-xl px-3 py-2 text-xs font-light text-white focus:outline-none">
                            <option value="0">Todos</option>
                            <option value="4">4.0+</option>
                            <option value="4.5">4.5+</option>
                            <option value="4.8">4.8+</option>
                        </select>
                    </div>

                    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" id="filtroJoyas" class="w-4 h-4 rounded border-[#C5A880]/40 bg-[#0A1628] text-[#C5A880] focus:ring-[#C5A880]/40">
                        <span class="text-[10px] uppercase tracking-[0.18em] text-slate-300">Solo joyas ocultas</span>
                    </label>
                <?php else: ?>
                    <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500">
                        Los filtros por puntaje se activan cuando hay valoraciones disponibles
                    </p>
                <?php endif; ?>

                <button type="button" id="limpiarFiltros" class="sm:ml-auto text-[10px] uppercase tracking-[0.18em] text-[#C5A880] hover:text-white transition-colors">
                    Limpiar filtros
                </button>

                <span class="text-[10px] uppercase tracking-[0.18em] text-slate-500 sm:border-l sm:border-[#C5A880]/15 sm:pl-4">
                    <span id="contadorVisibles" aria-live="polite"><?= $total_mostrado ?></span> visibles
                </span>
            </div>
        </div>
    </section>

    <!-- GRID DE LUGARES -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div id="gridLugares" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

            <?php foreach ($lugares as $lugar): ?>
                <?php
                    $tipo = $lugar['tipo'] ?? 'cultural';
                    $tipo_dato = $tipos_info[$tipo] ?? ['label' => ucfirst($tipo), 'icono' => '✦'];
                    $badge_dato = $badges_info[$lugar['badge'] ?? 'sin_puntaje'] ?? $badges_info['sin_puntaje'];
                    $rating = $lugar['rating'];
                    $reviews = $lugar['reviews'];
                    $score = $lugar['score'];
                    $descripcion = trim((string)($lugar['descripcion'] ?? ''));
                    if ($descripcion === '') {
                        $direccion_texto = trim((string)($lugar['direccion'] ?? ''));
                        $descripcion = $direccion_texto !== ''
                            ? 'Ubicado en ' . $direccion_texto . '.'
                            : 'Punto de interés de tipo ' . mb_strtolower($tipo_dato['label']) . ' registrado en fuentes de datos abiertas.';
                    }
                    $buscable = mb_strtolower(implode(' ', [
                        $lugar['nombre'] ?? '',
                        $lugar['ciudad'] ?? '',
                        $lugar['pais'] ?? '',
                        $tipo_dato['label'],
                        $descripcion,
                    ]), 'UTF-8');
                    $ubicacion = implode(' · ', array_filter([
                        trim((string)($lugar['ciudad'] ?? '')),
                        trim((string)($lugar['pais'] ?? '')),
                    ], function ($valor) {
                        return $valor !== '';
                    }));
                ?>
                <article class="lugar-card group relative flex flex-col bg-[#132238]/60 border border-[#C5A880]/20 hover:border-[#C5A880]/60 rounded-2xl p-5 shadow-xl hover:shadow-2xl hover:shadow-[#C5A880]/10 transition-all duration-500 backdrop-blur-sm"
                         data-tipo="<?= htmlspecialchars($tipo) ?>"
                         data-rating="<?= $rating !== null ? htmlspecialchars((string)$rating) : '' ?>"
                         data-joya="<?= !empty($lugar['es_joya']) ? '1' : '0' ?>"
                         data-search="<?= htmlspecialchars($buscable) ?>">

                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="w-11 h-11 rounded-xl bg-[#0A1628] border border-[#C5A880]/25 flex items-center justify-center text-xl group-hover:scale-105 transition-transform duration-300">
                            <?= $tipo_dato['icono'] ?>
                        </div>
                        <div class="flex flex-col items-end gap-1.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[9px] font-light uppercase tracking-[0.14em] <?= $badge_dato['clases'] ?>">
                                <span aria-hidden="true"><?= $badge_dato['icono'] ?></span>
                                <?= htmlspecialchars($badge_dato['texto']) ?>
                            </span>
                            <span class="text-[9px] uppercase tracking-[0.18em] text-slate-500"><?= htmlspecialchars($tipo_dato['label']) ?></span>
                        </div>
                    </div>

                    <h3 class="text-lg font-light text-white tracking-wide group-hover:text-[#C5A880] transition-colors leading-snug">
                        <?= htmlspecialchars($lugar['nombre'] ?? 'Lugar sin nombre') ?>
                    </h3>
                    <p class="text-[10px] uppercase tracking-[0.18em] text-[#C5A880]/70 mt-1">
                        <?= htmlspecialchars($ubicacion) ?>
                    </p>

                    <!-- PUNTAJE -->
                    <div class="mt-4">
                        <?php if ($rating !== null && $reviews !== null): ?>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[#C5A880] text-sm tracking-[0.1em]" aria-hidden="true"><?= $estrellas($rating) ?></span>
                                <span class="text-sm text-white font-normal"><?= number_format((float)$rating, 1) ?></span>
                                <span class="text-[11px] text-slate-400 font-light">
                                    (<?= number_format((int)$reviews) ?> reseñas<?= !empty($lugar['fuente_rating']) ? ' · vía ' . htmlspecialchars($fuentes_info[$lugar['fuente_rating']] ?? $lugar['fuente_rating']) : '' ?>)
                                </span>
                            </div>
                        <?php else: ?>
                            <p class="text-[11px] text-slate-400 font-light italic">Sin puntaje todavía: la fuente no entrega valoraciones.</p>
                        <?php endif; ?>
                    </div>

                    <!-- ÍNDICES -->
                    <?php if ($score !== null): ?>
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div>
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-[9px] uppercase tracking-[0.16em] text-slate-400">Puntaje</span>
                                    <span class="text-xs text-white"><?= (int)$score ?><span class="text-slate-500 text-[10px]">/100</span></span>
                                </div>
                                <div class="h-1.5 mt-1.5 rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-[#C5A880] to-amber-200" style="width: <?= (int)$score ?>%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-[9px] uppercase tracking-[0.16em] text-slate-400">Tranquilidad</span>
                                    <span class="text-xs text-white"><?= (int)($lugar['tranquilidad'] ?? 0) ?><span class="text-slate-500 text-[10px]">/100</span></span>
                                </div>
                                <div class="h-1.5 mt-1.5 rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-sky-300" style="width: <?= (int)($lugar['tranquilidad'] ?? 0) ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <p class="text-xs text-slate-300/80 font-light leading-relaxed mt-4 line-clamp-3">
                        <?= htmlspecialchars($descripcion) ?>
                    </p>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4 text-[10px] uppercase tracking-[0.14em] text-slate-400">
                        <?php if (!empty($lugar['distancia_km'])): ?>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-[#C5A880]"></i>
                                <?= htmlspecialchars((string)$lugar['distancia_km']) ?> km del centro
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($lugar['mejor_momento'])): ?>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-[#C5A880]"></i>
                                <?= htmlspecialchars($lugar['mejor_momento']) ?>
                            </span>
                        <?php endif; ?>
                        <?php foreach ((array)($lugar['fuentes'] ?? []) as $fuente_lugar): ?>
                            <span class="inline-flex items-center gap-1.5 text-slate-500">
                                <i class="fa-solid fa-database text-[#C5A880]/60"></i>
                                <?= htmlspecialchars($fuentes_info[$fuente_lugar] ?? ucfirst((string)$fuente_lugar)) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-auto pt-5">
                        <a href="<?= htmlspecialchars($lugar['google_maps_uri'] ?: '#') ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="w-full py-2.5 bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] font-light tracking-[0.15em] uppercase text-[10px] rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <span>Ver en Google Maps</span>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- ESTADO VACÍO -->
        <div id="sinResultadosLugares" class="<?= $total_mostrado > 0 ? 'hidden' : '' ?> text-center py-20 space-y-4">
            <div class="text-3xl text-[#C5A880]">✦</div>
            <h3 class="text-lg font-light text-white tracking-wider">
                <?= $total_mostrado > 0 ? 'No hay lugares con esos filtros' : 'No encontramos lugares para esta búsqueda' ?>
            </h3>
            <p class="text-xs text-slate-400 font-light tracking-wide max-w-md mx-auto">
                <?php if ($total_mostrado > 0): ?>
                    Prueba con otro tipo de lugar o desactiva los filtros activos.
                <?php else: ?>
                    Escribe el nombre de una ciudad o país. Si no está en la lista, la buscamos
                    con el geocodificador para encontrar lugares a su alrededor.
                <?php endif; ?>
            </p>
            <button type="button" id="resetVacio" class="px-5 py-2.5 mt-2 border border-[#C5A880]/40 text-[#C5A880] hover:bg-[#C5A880] hover:text-[#0A1628] rounded-xl text-[10px] uppercase tracking-[0.18em] transition-all">
                Ver todos los lugares
            </button>
        </div>
    </section>

    <!-- METODOLOGÍA -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        <div class="border-t border-[#C5A880]/15 pt-10">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <p class="text-[10px] uppercase tracking-[0.24em] text-[#C5A880]/70 mb-2">Transparencia</p>
                <h2 class="text-2xl font-light tracking-wide text-white">¿De dónde salen estos lugares?</h2>
                <p class="text-xs text-slate-400 font-light mt-2 leading-relaxed">
                    No usamos ningún catálogo de ejemplo. Todo lo que ves viene de APIs reales,
                    y explicamos qué aporta cada una.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-[#132238]/40 border border-[#C5A880]/15 rounded-2xl p-5">
                    <div class="text-2xl mb-3">🛰️</div>
                    <h3 class="text-sm font-light text-white tracking-wide mb-2">Geoapify</h3>
                    <p class="text-xs text-slate-400 font-light leading-relaxed">
                        Lugares sobre <span class="text-[#C5A880]">OpenStreetMap</span>: patrimonio, miradores,
                        templos, parques y playas. Plan gratuito sin tarjeta.
                    </p>
                </div>
                <div class="bg-[#132238]/40 border border-[#C5A880]/15 rounded-2xl p-5">
                    <div class="text-2xl mb-3">🧭</div>
                    <h3 class="text-sm font-light text-white tracking-wide mb-2">TomTom</h3>
                    <p class="text-xs text-slate-400 font-light leading-relaxed">
                        Complementa con <span class="text-[#C5A880]">plazas, iglesias, parques y monumentos</span>
                        que la otra fuente no devuelve. Se descartan duplicados comparando nombre y distancia.
                    </p>
                </div>
                <div class="bg-[#132238]/40 border border-[#C5A880]/15 rounded-2xl p-5">
                    <div class="text-2xl mb-3">⚖️</div>
                    <h3 class="text-sm font-light text-white tracking-wide mb-2">Puntajes honestos</h3>
                    <p class="text-xs text-slate-400 font-light leading-relaxed">
                        Las estrellas solo aparecen con <span class="text-[#C5A880]">Google Places</span>, la única
                        fuente con valoraciones de usuarios. Sin ella se muestra
                        <span class="text-[#C5A880]">“Sin puntaje todavía”</span> y los resultados se ordenan por cercanía.
                    </p>
                </div>
            </div>

            <p class="text-[10px] text-slate-500 font-light text-center mt-6 tracking-wide">
                Un lugar es joya oculta cuando tiene 4.5 o más y entre 20 y 800 reseñas. Criterio aplicable
                solo cuando hay valoraciones reales disponibles.
                <br>
                Cuando el mismo sitio aparece con dos nombres distintos, se fusiona comparando nombre y distancia.
                La ubicación de algunos puntos de TomTom puede ser aproximada: el enlace abre Google Maps para verificarla.
            </p>
        </div>
    </section>

</main>

<!-- FILTROS INTERACTIVOS -->
<script>
(function () {
    const estado = { tipo: 'todos', ratingMin: 0, soloJoyas: false };

    const botonesTipo = document.querySelectorAll('.btn-tipo');
    const filtroRating = document.getElementById('filtroRating');
    const filtroJoyas = document.getElementById('filtroJoyas');
    const contador = document.getElementById('contadorVisibles');
    const sinResultados = document.getElementById('sinResultadosLugares');

    function marcarBotonActivo(tipo) {
        botonesTipo.forEach(function (boton) {
            const activo = boton.dataset.tipo === tipo;
            boton.classList.toggle('bg-[#C5A880]', activo);
            boton.classList.toggle('text-[#0A1628]', activo);
            boton.classList.toggle('border-[#C5A880]', activo);
            boton.classList.toggle('bg-[#0A1628]/70', !activo);
            boton.classList.toggle('text-slate-300', !activo);
            boton.classList.toggle('border-[#C5A880]/20', !activo);
        });
    }

    function aplicarFiltros() {
        let visibles = 0;
        document.querySelectorAll('.lugar-card').forEach(function (tarjeta) {
            const tipoOk = estado.tipo === 'todos' || tarjeta.dataset.tipo === estado.tipo;
            const rating = parseFloat(tarjeta.dataset.rating || '0');
            const ratingOk = rating >= estado.ratingMin;
            const joyaOk = !estado.soloJoyas || tarjeta.dataset.joya === '1';
            const mostrar = tipoOk && ratingOk && joyaOk;

            tarjeta.classList.toggle('hidden', !mostrar);
            if (mostrar) {
                visibles++;
            }
        });

        if (contador) {
            contador.textContent = visibles;
        }
        if (sinResultados) {
            sinResultados.classList.toggle('hidden', visibles > 0);
        }
    }

    botonesTipo.forEach(function (boton) {
        boton.addEventListener('click', function () {
            estado.tipo = boton.dataset.tipo;
            marcarBotonActivo(estado.tipo);
            aplicarFiltros();
        });
    });

    if (filtroRating) {
        filtroRating.addEventListener('change', function () {
            estado.ratingMin = parseFloat(filtroRating.value) || 0;
            aplicarFiltros();
        });
    }

    if (filtroJoyas) {
        filtroJoyas.addEventListener('change', function () {
            estado.soloJoyas = filtroJoyas.checked;
            aplicarFiltros();
        });
    }

    function limpiar() {
        estado.tipo = 'todos';
        estado.ratingMin = 0;
        estado.soloJoyas = false;
        if (filtroRating) filtroRating.value = '0';
        if (filtroJoyas) filtroJoyas.checked = false;
        marcarBotonActivo('todos');
        aplicarFiltros();
    }

    const limpiarBtn = document.getElementById('limpiarFiltros');
    if (limpiarBtn) limpiarBtn.addEventListener('click', limpiar);

    const resetVacio = document.getElementById('resetVacio');
    if (resetVacio) resetVacio.addEventListener('click', limpiar);
})();
</script>
