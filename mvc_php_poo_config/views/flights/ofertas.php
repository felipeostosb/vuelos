<main class="bg-gray-50 pb-20">
    
    <section class="bg-[#0090FF] text-white py-20">
        <div class="max-w-[1280px] mx-auto px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Ofertas y promociones</h1>
            <p class="text-lg md:text-xl text-white/90">Precios actualizados por IA cada hora</p>
        </div>
    </section>

    <div class="max-w-[1280px] mx-auto px-8 mt-12">
        
        <?php
            require_once 'models/Vuelo.php';
            $vueloModel = new Vuelo();
            $todos = $vueloModel->obtenerTodos();
            $mejores_ofertas = array_filter($todos, function($v) {
                return (bool)$v['es_mejor_precio'];
            });
            // Limitar a los 6 mejores
            $mejores_ofertas = array_slice($mejores_ofertas, 0, 6);
        ?>
        
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-[#0A192F] mb-6">🔥 Mejores Ofertas</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($mejores_ofertas as $vuelo): ?>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 tarjeta-animada group flex flex-col">
                    <div class="h-48 overflow-hidden bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-plane-departure text-6xl text-blue-200 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-[#0A192F] mb-1">
                                <?php echo htmlspecialchars($vuelo['origen_nombre'] ?? 'LIM'); ?> ➔ <?php echo htmlspecialchars($vuelo['destino_ciudad']); ?>
                            </h3>
                            <p class="text-sm text-gray-500 mb-1">Con <?php echo htmlspecialchars($vuelo['aerolinea_nombre']); ?> · <?php echo $vuelo['escalas'] == 0 ? 'Directo' : $vuelo['escalas'] . ' escala(s)'; ?></p>
                            <p class="text-sm text-[#FF3B30] font-medium mb-6"><i class="fa-regular fa-clock mr-1"></i> Oferta limitada</p>
                        </div>
                        
                        <div class="flex justify-between items-center mt-auto">
                            <span class="text-2xl font-bold text-[#0070F3]">S/. <?php echo number_format($vuelo['precio'], 2); ?></span>
                            <a href="?action=buscar&destino=<?php echo urlencode($vuelo['destino_ciudad']); ?>" class="bg-[#0070F3] hover:bg-[#0051CC] text-white px-6 py-2.5 rounded-xl font-medium transition-colors text-center">Ver</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
</main>
