<main class="bg-gray-50 pb-20">
    
    <section class="bg-[#0080FF] text-white py-16">
        <div class="max-w-[1560px] mx-auto px-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Destinos populares</h1>
            <p class="text-lg text-blue-100">Descubre los favoritos de nuestros viajeros</p>
        </div>
    </section>

    <section class="max-w-[1560px] mx-auto px-12 mt-12">
        <?php
            require_once 'config/database.php';
            $db = new Database();
            $conn = $db->getConnection();
            $aeropuertos = [];
            
            if ($conn) {
                $stmt = $conn->query("SELECT * FROM aeropuertos ORDER BY pais, ciudad");
                $aeropuertos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($aeropuertos as $aeropuerto): ?>
            <div class="bg-white rounded-2xl shadow-md overflow-hidden tarjeta-animada group">
                <div class="h-48 overflow-hidden bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-map-location-dot text-6xl text-blue-300 group-hover:scale-110 transition-transform duration-500"></i>
                </div>
                <div class="p-8 text-center">
                    <h3 class="text-xl font-bold text-[#0A2540] mb-2"><?php echo htmlspecialchars($aeropuerto['ciudad']); ?></h3>
                    <p class="text-gray-500 mb-6"><?php echo htmlspecialchars($aeropuerto['pais']); ?> (<?php echo htmlspecialchars($aeropuerto['codigo_iata']); ?>)</p>
                    <a href="?action=buscar&destino=<?php echo urlencode($aeropuerto['ciudad']); ?>" class="w-full inline-block bg-[#0070F3] hover:bg-[#0051CC] text-white py-2 rounded-xl font-medium transition-colors">
                        Ver vuelos
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

</main>
