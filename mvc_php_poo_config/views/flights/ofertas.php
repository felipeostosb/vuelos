<main class="bg-[#0A1628] pb-24 text-white font-sans min-h-screen">
    
    <section class="relative bg-gradient-to-b from-[#0A1628] via-[#132238] to-[#0A1628] border-b border-[#C5A880]/15 text-white py-20 px-6 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#C5A880]/15 via-transparent to-transparent pointer-events-none"></div>
        <div class="max-w-6xl mx-auto text-center relative z-10 space-y-4">
            <span class="px-5 py-2 bg-[#C5A880]/10 text-[#C5A880] border border-[#C5A880]/30 text-xs font-light tracking-[0.25em] uppercase rounded-full inline-block">
                Promociones Exclusivas Boutique
            </span>
            <h1 class="text-3xl md:text-5xl font-light tracking-[0.06em] text-white">Ofertas & Promociones de Vuelo</h1>
            <p class="text-[#C5A880]/80 text-sm md:text-base max-w-2xl mx-auto font-light tracking-wide">
                Tarifas privilegiadas para los mejores destinos de Perú y el mundo
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        
        <?php
            require_once 'models/Vuelo.php';
            $modo_actual = obtener_modo_ofertas();

            if ($modo_actual === 'peru_destacadas') {
                $mejores_ofertas = obtener_ofertas_peru_destacadas();
                $titulo_seccion = "🇵🇪 Ofertas Exclusivas de Perú (Destinos Principales)";
            } else {
                $todos = obtener_todos_los_vuelos();
                $mejores_ofertas = array_filter($todos, function($v) {
                    return (bool)($v['es_mejor_precio'] ?? true);
                });
                $mejores_ofertas = array_slice($mejores_ofertas, 0, 6);
                
                // Asignar imagen helper si existe a cada vuelo
                foreach ($mejores_ofertas as &$v) {
                    $v['imagen'] = obtener_imagen_destino($v['destino_ciudad'] ?? '');
                    $v['origen_nombre'] = $v['origen_nombre'] ?? 'LIM';
                    $v['destino_iata'] = $v['destino_iata'] ?? 'DEST';
                }
                unset($v);
                $titulo_seccion = "⚡ Ofertas en Tiempo Real de la API Duffel";
            }
        ?>
        
        <section class="mb-16">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-4 border-b border-[#C5A880]/15">
                <h2 class="text-xl md:text-2xl font-light text-[#C5A880] tracking-wider uppercase flex items-center gap-3">
                    <span>✦</span> <?= $titulo_seccion ?>
                </h2>
                <span class="text-xs font-light text-slate-400">
                    Modo: <strong class="text-white"><?= $modo_actual === 'peru_destacadas' ? 'Destacados Perú' : 'Live API Duffel' ?></strong>
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($mejores_ofertas as $vuelo): ?>
                    <?php 
                        $imagen_ruta = $vuelo['imagen'] ?? obtener_imagen_destino($vuelo['destino_ciudad'] ?? '');
                    ?>
                <div class="bg-[#132238]/60 backdrop-blur-md rounded-2xl shadow-xl border border-[#C5A880]/20 group flex flex-col overflow-hidden hover:border-[#C5A880]/60 hover:shadow-2xl hover:shadow-[#C5A880]/10 transition-all duration-500">
                    
                    <!-- ENCABEZADO CON FOTO PERSONALIZADA O FALLBACK A ÍCONO POR DEFECTO -->
                    <div class="h-72 sm:h-80 overflow-hidden bg-[#0A1628] flex items-center justify-center border-b border-[#C5A880]/15 relative">
                        <span class="absolute top-4 right-4 z-20 px-3 py-1 bg-[#0A1628]/80 text-[#C5A880] border border-[#C5A880]/40 text-[10px] font-light tracking-widest uppercase rounded-full backdrop-blur-md">
                            <?= htmlspecialchars($vuelo['tag'] ?? 'Oferta Top') ?>
                        </span>
                        
                        <span class="absolute top-4 left-4 z-20 px-2.5 py-0.5 bg-[#C5A880] text-[#0A1628] font-mono text-[11px] font-medium rounded-md tracking-wider shadow-md">
                            <?= htmlspecialchars($vuelo['destino_iata'] ?? 'PER') ?>
                        </span>

                        <?php if (!empty($imagen_ruta)): ?>
                            <!-- FOTO REAL DEL DESTINO ADAPTADA PARA FORMATO VERTICAL/HORIZONTAL -->
                            <img src="<?= htmlspecialchars($imagen_ruta) ?>" 
                                 alt="<?= htmlspecialchars($vuelo['destino_ciudad']) ?>" 
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0A1628] via-[#0A1628]/25 to-transparent pointer-events-none"></div>
                        <?php else: ?>
                            <!-- FALLBACK AUTOMÁTICO AL ÍCONO SI NO EXISTE IMAGEN -->
                            <div class="w-full h-full bg-gradient-to-br from-[#0A1628] to-[#132238] flex items-center justify-center">
                                <i class="fa-solid fa-plane-departure text-5xl text-[#C5A880]/40 group-hover:scale-110 group-hover:text-[#C5A880] transition-all duration-500"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-xl font-light text-white tracking-wide group-hover:text-[#C5A880] transition-colors">
                                    <?= htmlspecialchars($vuelo['origen_nombre'] ?? 'Lima'); ?> ➔ <?= htmlspecialchars($vuelo['destino_ciudad']); ?>
                                </h3>
                            </div>
                            
                            <p class="text-xs text-slate-300 font-light tracking-wide mb-2">
                                Con <?= htmlspecialchars($vuelo['aerolinea_nombre']); ?> · <?= ($vuelo['escalas'] ?? 0) == 0 ? 'Vuelo Directo' : $vuelo['escalas'] . ' escala(s)'; ?>
                            </p>
                            
                            <?php if (!empty($vuelo['desc'])): ?>
                                <p class="text-xs text-slate-400 font-light leading-relaxed line-clamp-2 mb-2">
                                    <?= htmlspecialchars($vuelo['desc']) ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-[11px] text-[#C5A880]/80 font-light tracking-wider flex items-center gap-1">
                                <i class="fa-regular fa-clock text-[10px]"></i> Disponibilidad Limitada Boutique
                            </p>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-[#C5A880]/15">
                            <div>
                                <span class="block text-[10px] font-light text-slate-400 uppercase tracking-widest">Desde</span>
                                <span class="text-2xl font-light text-[#C5A880] tracking-wider">S/. <?= number_format($vuelo['precio'], 2); ?></span>
                            </div>
                            
                            <a href="index.php?action=buscar&origen=Lima&destino=<?= urlencode($vuelo['destino_ciudad']); ?>" 
                               class="bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] px-5 py-2.5 rounded-xl font-light text-xs uppercase tracking-widest transition-all duration-300 shadow-sm text-center">
                                Volar a <?= htmlspecialchars($vuelo['destino_ciudad']); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
</main>
