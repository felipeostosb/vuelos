<main class="bg-[#0A1628] pb-24 text-white font-sans min-h-screen">
    
    <section class="relative bg-gradient-to-b from-[#0A1628] via-[#132238] to-[#0A1628] border-b border-[#C5A880]/15 text-white py-20 px-6 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#C5A880]/15 via-transparent to-transparent pointer-events-none"></div>
        <div class="max-w-6xl mx-auto text-center relative z-10 space-y-4">
            <span class="px-5 py-2 bg-[#C5A880]/10 text-[#C5A880] border border-[#C5A880]/30 text-xs font-light tracking-[0.25em] uppercase rounded-full inline-block">
                Promociones Exclusivas
            </span>
            <h1 class="text-3xl md:text-5xl font-light tracking-[0.06em] text-white">Ofertas & Promociones de Vuelo</h1>
            <p class="text-[#C5A880]/80 text-sm md:text-base max-w-2xl mx-auto font-light tracking-wide">Precios optimizados dinámicamente por la IA de NovAirlines cada hora</p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        
        <?php
            require_once 'models/Vuelo.php';
            $todos = obtener_todos_los_vuelos();
            $mejores_ofertas = array_filter($todos, function($v) {
                return (bool)$v['es_mejor_precio'];
            });
            // Limitar a los 6 mejores
            $mejores_ofertas = array_slice($mejores_ofertas, 0, 6);
        ?>
        
        <section class="mb-16">
            <h2 class="text-xl md:text-2xl font-light text-[#C5A880] tracking-wider mb-8 uppercase flex items-center gap-3">
                <span>✦</span> Tarjetas de Mejor Precio Garantizado
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($mejores_ofertas as $vuelo): ?>
                <div class="bg-[#132238]/60 backdrop-blur-md rounded-2xl shadow-xl border border-[#C5A880]/20 group flex flex-col overflow-hidden hover:border-[#C5A880]/60 hover:shadow-2xl hover:shadow-[#C5A880]/10 transition-all duration-500">
                    <div class="h-48 overflow-hidden bg-[#0A1628]/60 flex items-center justify-center border-b border-[#C5A880]/15 relative">
                        <span class="absolute top-4 right-4 px-3 py-1 bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/30 text-[10px] font-light tracking-widest uppercase rounded-full">
                            Oferta Top
                        </span>
                        <i class="fa-solid fa-plane-departure text-5xl text-[#C5A880]/40 group-hover:scale-110 group-hover:text-[#C5A880] transition-all duration-500"></i>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="text-xl font-light text-white mb-2 tracking-wide group-hover:text-[#C5A880] transition-colors">
                                <?php echo htmlspecialchars($vuelo['origen_nombre'] ?? 'LIM'); ?> ➔ <?php echo htmlspecialchars($vuelo['destino_ciudad']); ?>
                            </h3>
                            <p class="text-xs text-slate-300 font-light tracking-wide mb-1">
                                Con <?php echo htmlspecialchars($vuelo['aerolinea_nombre']); ?> · <?php echo $vuelo['escalas'] == 0 ? 'Vuelo Directo' : $vuelo['escalas'] . ' escala(s)'; ?>
                            </p>
                            <p class="text-xs text-[#9C694C] font-light tracking-wider"><i class="fa-regular fa-clock mr-1"></i> Disponibilidad Limitada</p>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-[#C5A880]/15">
                            <div>
                                <span class="block text-[10px] font-light text-slate-400 uppercase tracking-widest">Desde</span>
                                <span class="text-2xl font-light text-[#C5A880] tracking-wider">S/. <?php echo number_format($vuelo['precio'], 2); ?></span>
                            </div>
                            <a href="?action=buscar&destino=<?php echo urlencode($vuelo['destino_ciudad']); ?>" 
                               class="bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] px-6 py-2.5 rounded-xl font-light text-xs uppercase tracking-widest transition-all duration-300 shadow-sm text-center">
                                Reservar
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
</main>
