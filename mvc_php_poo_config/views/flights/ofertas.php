<main class="bg-brand-blue pb-20 text-white">
    
    <section class="bg-gradient-to-r from-brand-purple to-brand-rose border-b border-brand-gold/15 text-white py-20">
        <div class="max-w-[1280px] mx-auto px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 font-sans">Ofertas y promociones</h1>
            <p class="text-lg md:text-xl text-brand-gold/80">Precios actualizados por IA cada hora</p>
        </div>
    </section>

    <div class="max-w-[1280px] mx-auto px-8 mt-12">
        
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
            <h2 class="text-2xl font-bold text-brand-gold mb-6 font-sans">🔥 Mejores Ofertas</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($mejores_ofertas as $vuelo): ?>
                <div class="bg-[#132238]/70 backdrop-blur-md rounded-2xl shadow-lg border border-brand-gold/15 tarjeta-animada group flex flex-col overflow-hidden hover:border-brand-gold/45 hover:shadow-xl transition-all duration-300">
                    <div class="h-48 overflow-hidden bg-brand-purple/20 flex items-center justify-center border-b border-brand-gold/10">
                        <i class="fa-solid fa-plane-departure text-6xl text-brand-gold/40 group-hover:scale-110 group-hover:text-brand-gold transition-all duration-500"></i>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white mb-1 font-sans">
                                <?php echo htmlspecialchars($vuelo['origen_nombre'] ?? 'LIM'); ?> ➔ <?php echo htmlspecialchars($vuelo['destino_ciudad']); ?>
                            </h3>
                            <p class="text-sm text-brand-gold/70 mb-1">Con <?php echo htmlspecialchars($vuelo['aerolinea_nombre']); ?> · <?php echo $vuelo['escalas'] == 0 ? 'Directo' : $vuelo['escalas'] . ' escala(s)'; ?></p>
                            <p class="text-sm text-brand-rose font-medium mb-6"><i class="fa-regular fa-clock mr-1"></i> Oferta limitada</p>
                        </div>
                        
                        <div class="flex justify-between items-center mt-auto">
                            <span class="text-2xl font-bold text-brand-gold">S/. <?php echo number_format($vuelo['precio'], 2); ?></span>
                            <a href="?action=buscar&destino=<?php echo urlencode($vuelo['destino_ciudad']); ?>" class="bg-gradient-to-r from-brand-gold to-brand-rose hover:from-brand-gold/90 hover:to-brand-rose/90 text-brand-blue px-6 py-2.5 rounded-xl font-bold transition-all shadow-[0_2px_10px_rgba(197,168,128,0.15)] text-center">Ver</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
</main>
