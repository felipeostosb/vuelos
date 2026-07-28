<main class="bg-[#0A1628] min-h-screen pb-24 text-white font-sans">
    
    <!-- ENCABEZADO HERO BOUTIQUE -->
    <section class="relative bg-gradient-to-b from-[#0A1628] via-[#132238] to-[#0A1628] border-b border-[#C5A880]/15 text-white py-20 px-6 overflow-hidden">
        <!-- Resplandor Dorado de Fondo (Amanecer Dorado #C5A880) -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#C5A880]/15 via-transparent to-transparent pointer-events-none"></div>
        
        <div class="max-w-6xl mx-auto text-center relative z-10 space-y-5">
            <span class="px-5 py-2 bg-[#C5A880]/10 text-[#C5A880] border border-[#C5A880]/30 text-xs font-light tracking-[0.25em] uppercase rounded-full inline-block">
                Colección de Destinos Boutique
            </span>
            <h1 class="text-3xl md:text-5xl font-light tracking-[0.06em] text-white">
                Rutas Nacionales & Destinos del Mundo
            </h1>
            <p class="text-[#C5A880]/80 text-sm md:text-base max-w-2xl mx-auto font-light tracking-wide leading-relaxed">
                Descubra la majestuosidad de cada rincón peruano y las ciudades más distinguidas del planeta con la excelencia aeronáutica de NovAirlines.
            </p>

            <!-- BARRA DE BÚSQUEDA REFINADA -->
            <div class="max-w-xl mx-auto mt-10 relative">
                <div class="relative flex items-center">
                    <input type="text" id="inputBuscarDestino" onkeyup="filtrarDestinos()" 
                           placeholder="Buscar por ciudad, país o código IATA (ej: Cusco, París, LIM)..." 
                           class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 focus:border-[#C5A880] rounded-2xl py-4 pl-12 pr-4 text-sm font-light text-white placeholder:text-slate-500 focus:outline-none transition-all shadow-2xl tracking-wide">
                    <svg class="w-5 h-5 text-[#C5A880] absolute left-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- NAVEGACIÓN Y FILTROS POR CONTINENTE -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        <div class="flex flex-wrap items-center justify-center gap-2 md:gap-3 py-4 border-b border-[#C5A880]/15">
            <button onclick="filtrarCategoria('todos')" id="btn-cat-todos" class="btn-filtro active px-6 py-2.5 rounded-full font-light text-xs uppercase tracking-[0.15em] transition-all duration-300 bg-[#C5A880] text-[#0A1628] shadow-lg shadow-[#C5A880]/20">
                <span>🌍 Todos</span>
            </button>
            <button onclick="filtrarCategoria('peru')" id="btn-cat-peru" class="btn-filtro px-6 py-2.5 rounded-full font-light text-xs uppercase tracking-[0.15em] transition-all duration-300 bg-[#132238]/60 text-slate-300 hover:text-white border border-[#C5A880]/20 hover:border-[#C5A880]/50">
                <span>🇵🇪 Perú (Nacional)</span>
            </button>
            <button onclick="filtrarCategoria('latam')" id="btn-cat-latam" class="btn-filtro px-6 py-2.5 rounded-full font-light text-xs uppercase tracking-[0.15em] transition-all duration-300 bg-[#132238]/60 text-slate-300 hover:text-white border border-[#C5A880]/20 hover:border-[#C5A880]/50">
                <span>🌎 América Latina</span>
            </button>
            <button onclick="filtrarCategoria('norteamerica')" id="btn-cat-norteamerica" class="btn-filtro px-6 py-2.5 rounded-full font-light text-xs uppercase tracking-[0.15em] transition-all duration-300 bg-[#132238]/60 text-slate-300 hover:text-white border border-[#C5A880]/20 hover:border-[#C5A880]/50">
                <span>🗽 América del Norte</span>
            </button>
            <button onclick="filtrarCategoria('europa')" id="btn-cat-europa" class="btn-filtro px-6 py-2.5 rounded-full font-light text-xs uppercase tracking-[0.15em] transition-all duration-300 bg-[#132238]/60 text-slate-300 hover:text-white border border-[#C5A880]/20 hover:border-[#C5A880]/50">
                <span>🏰 Europa</span>
            </button>
            <button onclick="filtrarCategoria('asia_oceania')" id="btn-cat-asia_oceania" class="btn-filtro px-6 py-2.5 rounded-full font-light text-xs uppercase tracking-[0.15em] transition-all duration-300 bg-[#132238]/60 text-slate-300 hover:text-white border border-[#C5A880]/20 hover:border-[#C5A880]/50">
                <span>⛩️ Asia & Oceanía</span>
            </button>
        </div>
    </section>

    <!-- CONTENEDOR DE TARJETAS DE DESTINO -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        
        <?php
            // Matriz de destinos top curados por categoría
            $destinos = [
                // 🇵🇪 PERÚ (NACIONAL)
                ['ciudad' => 'Cusco', 'pais' => 'Perú', 'iata' => 'CUZ', 'cat' => 'peru', 'icono' => '🏔️', 'tag' => 'Nacional', 'desc' => 'Maravilla del Mundo & Valle Sagrado'],
                ['ciudad' => 'Lima', 'pais' => 'Perú', 'iata' => 'LIM', 'cat' => 'peru', 'icono' => '🇵🇪', 'tag' => 'Nacional', 'desc' => 'Capital Gastronómica de América'],
                ['ciudad' => 'Arequipa', 'pais' => 'Perú', 'iata' => 'AQP', 'cat' => 'peru', 'icono' => '🌋', 'tag' => 'Nacional', 'desc' => 'La Ciudad Blanca & Cañón del Colca'],
                ['ciudad' => 'Trujillo', 'pais' => 'Perú', 'iata' => 'TRU', 'cat' => 'peru', 'icono' => '🏛️', 'tag' => 'Nacional', 'desc' => 'Ciudad de la Eterna Primavera'],
                ['ciudad' => 'Chiclayo', 'pais' => 'Perú', 'iata' => 'CIX', 'cat' => 'peru', 'icono' => '👑', 'tag' => 'Nacional', 'desc' => 'Capital de la Amistad & Sipán'],
                ['ciudad' => 'Piura', 'pais' => 'Perú', 'iata' => 'PIU', 'cat' => 'peru', 'icono' => '🏖️', 'tag' => 'Nacional', 'desc' => 'Sol & Playas del Norte (Máncora)'],
                ['ciudad' => 'Iquitos', 'pais' => 'Perú', 'iata' => 'IQT', 'cat' => 'peru', 'icono' => '🌿', 'tag' => 'Nacional', 'desc' => 'Corazón del Amazonas Peruano'],
                ['ciudad' => 'Tarapoto', 'pais' => 'Perú', 'iata' => 'TPP', 'cat' => 'peru', 'icono' => '🌴', 'tag' => 'Nacional', 'desc' => 'Ciudad de las Palmeras'],
                ['ciudad' => 'Juliaca / Puno', 'pais' => 'Perú', 'iata' => 'JUL', 'cat' => 'peru', 'icono' => '⛵', 'tag' => 'Nacional', 'desc' => 'El Sagrado Lago Titicaca'],
                ['ciudad' => 'Pucallpa', 'pais' => 'Perú', 'iata' => 'PCL', 'cat' => 'peru', 'icono' => '🌊', 'tag' => 'Nacional', 'desc' => 'Selva Central & Laguna Yarinacocha'],
                ['ciudad' => 'Tacna', 'pais' => 'Perú', 'iata' => 'TCQ', 'cat' => 'peru', 'icono' => '🎖️', 'tag' => 'Nacional', 'desc' => 'Ciudad Heroica del Sur'],
                ['ciudad' => 'Andahuaylas', 'pais' => 'Perú', 'iata' => 'ANS', 'cat' => 'peru', 'icono' => '🌾', 'tag' => 'Nacional', 'desc' => 'Pradera de los Chankas'],

                // 🌎 AMÉRICA LATINA
                ['ciudad' => 'Buenos Aires', 'pais' => 'Argentina', 'iata' => 'EZE', 'cat' => 'latam', 'icono' => '💃', 'tag' => 'Internacional', 'desc' => 'Capital del Tango & Arquitectura'],
                ['ciudad' => 'Río de Janeiro', 'pais' => 'Brasil', 'iata' => 'GIG', 'cat' => 'latam', 'icono' => '🏖️', 'tag' => 'Internacional', 'desc' => 'Cristo Redentor & Copacabana'],
                ['ciudad' => 'Santiago', 'pais' => 'Chile', 'iata' => 'SCL', 'cat' => 'latam', 'icono' => '🏔️', 'tag' => 'Internacional', 'desc' => 'Valle Central & Los Andes'],
                ['ciudad' => 'Bogotá', 'pais' => 'Colombia', 'iata' => 'BOG', 'cat' => 'latam', 'icono' => '☕', 'tag' => 'Internacional', 'desc' => 'Arte & Historia Andina'],
                ['ciudad' => 'Cancún', 'pais' => 'México', 'iata' => 'CUN', 'cat' => 'latam', 'icono' => '🏝️', 'tag' => 'Internacional', 'desc' => 'Caribe Mexicano & Playas'],
                ['ciudad' => 'Sao Paulo', 'pais' => 'Brasil', 'iata' => 'GRU', 'cat' => 'latam', 'icono' => '🏙️', 'tag' => 'Internacional', 'desc' => 'Centro Financiero de Sudamérica'],

                // 🗽 AMÉRICA DEL NORTE
                ['ciudad' => 'Nueva York', 'pais' => 'Estados Unidos', 'iata' => 'JFK', 'cat' => 'norteamerica', 'icono' => '🗽', 'tag' => 'Internacional', 'desc' => 'La Metrópoli Cosmopolita'],
                ['ciudad' => 'Miami', 'pais' => 'Estados Unidos', 'iata' => 'MIA', 'cat' => 'norteamerica', 'icono' => '🌴', 'tag' => 'Internacional', 'desc' => 'Sol, Vida Nocturna & Compras'],
                ['ciudad' => 'Los Ángeles', 'pais' => 'Estados Unidos', 'iata' => 'LAX', 'cat' => 'norteamerica', 'icono' => '🎬', 'tag' => 'Internacional', 'desc' => 'Hollywood & Costa de California'],
                ['ciudad' => 'Orlando', 'pais' => 'Estados Unidos', 'iata' => 'MCO', 'cat' => 'norteamerica', 'icono' => '🎢', 'tag' => 'Internacional', 'desc' => 'Capital de los Parques Temáticos'],
                ['ciudad' => 'Ciudad de México', 'pais' => 'México', 'iata' => 'MEX', 'cat' => 'norteamerica', 'icono' => '🌮', 'tag' => 'Internacional', 'desc' => 'Historia Azteca & Gastronomía'],
                ['ciudad' => 'Toronto', 'pais' => 'Canadá', 'iata' => 'YYZ', 'cat' => 'norteamerica', 'icono' => '🍁', 'tag' => 'Internacional', 'desc' => 'Torre CN & Vanguardia'],

                // 🏰 EUROPA
                ['ciudad' => 'Madrid', 'pais' => 'España', 'iata' => 'MAD', 'cat' => 'europa', 'icono' => '🇪🇸', 'tag' => 'Internacional', 'desc' => 'Arte, Tapas & Tradición Española'],
                ['ciudad' => 'París', 'pais' => 'Francia', 'iata' => 'CDG', 'cat' => 'europa', 'icono' => '🗼', 'tag' => 'Internacional', 'desc' => 'La Ciudad de la Luz & Alta Costura'],
                ['ciudad' => 'Londres', 'pais' => 'Reino Unido', 'iata' => 'LHR', 'cat' => 'europa', 'icono' => '👑', 'tag' => 'Internacional', 'desc' => 'Big Ben & Elegancia Británica'],
                ['ciudad' => 'Roma', 'pais' => 'Italia', 'iata' => 'FCO', 'cat' => 'europa', 'icono' => '🏛️', 'tag' => 'Internacional', 'desc' => 'El Coliseo & El Vaticano'],
                ['ciudad' => 'Barcelona', 'pais' => 'España', 'iata' => 'BCN', 'cat' => 'europa', 'icono' => '🎨', 'tag' => 'Internacional', 'desc' => 'Gaudí & Mar Mediterráneo'],
                ['ciudad' => 'Ámsterdam', 'pais' => 'Países Bajos', 'iata' => 'AMS', 'cat' => 'europa', 'icono' => '🌷', 'tag' => 'Internacional', 'desc' => 'Canales & Cultura Vanguardista'],

                // ⛩️ ASIA & OCEANÍA
                ['ciudad' => 'Tokio', 'pais' => 'Japón', 'iata' => 'HND', 'cat' => 'asia_oceania', 'icono' => '🎌', 'tag' => 'Internacional', 'desc' => 'Futurismo & Tradición Milenaria'],
                ['ciudad' => 'Dubái', 'pais' => 'Emiratos Árabes', 'iata' => 'DXB', 'cat' => 'asia_oceania', 'icono' => '🏙️', 'tag' => 'Internacional', 'desc' => 'Lujo Exclusivo & Rascacielos'],
                ['ciudad' => 'Bangkok', 'pais' => 'Tailandia', 'iata' => 'BKK', 'cat' => 'asia_oceania', 'icono' => '🛕', 'tag' => 'Internacional', 'desc' => 'Templos Dorados & Gastronomía'],
                ['ciudad' => 'Singapur', 'pais' => 'Singapur', 'iata' => 'SIN', 'cat' => 'asia_oceania', 'icono' => '🦁', 'tag' => 'Internacional', 'desc' => 'Jardines de la Bahía & Innovación'],
                ['ciudad' => 'Sídney', 'pais' => 'Australia', 'iata' => 'SYD', 'cat' => 'asia_oceania', 'icono' => '🦘', 'tag' => 'Internacional', 'desc' => 'Ópera de Sídney & Paisajes'],
                ['ciudad' => 'El Cairo', 'pais' => 'Egipto', 'iata' => 'CAI', 'cat' => 'asia_oceania', 'icono' => '🐫', 'tag' => 'Internacional', 'desc' => 'Pirámides de Giza & El Nilo']
            ];
        ?>

        <div id="gridDestinos" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($destinos as $d): ?>
                <div class="card-destino bg-[#132238]/60 border border-[#C5A880]/20 hover:border-[#C5A880]/60 rounded-2xl p-6 shadow-xl hover:shadow-2xl hover:shadow-[#C5A880]/10 transition-all duration-500 flex flex-col justify-between backdrop-blur-sm group" 
                     data-cat="<?= $d['cat'] ?>" 
                     data-search="<?= strtolower($d['ciudad'] . ' ' . $d['pais'] . ' ' . $d['iata']) ?>">
                    
                    <div>
                        <!-- ENCABEZADO DE TARJETA CON BADGES BOUTIQUE -->
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-2xl opacity-90 group-hover:scale-110 transition-transform duration-300"><?= $d['icono'] ?></span>
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-light uppercase tracking-widest <?= $d['cat'] === 'peru' ? 'bg-[#C5A880]/15 text-[#C5A880] border border-[#C5A880]/40' : 'bg-slate-700/40 text-slate-300 border border-slate-600/40' ?>">
                                    <?= $d['tag'] ?>
                                </span>
                                <span class="px-2.5 py-0.5 bg-[#0A1628] text-[#C5A880] font-mono text-[11px] font-light rounded-md border border-[#C5A880]/30 tracking-wider">
                                    <?= $d['iata'] ?>
                                </span>
                            </div>
                        </div>

                        <!-- NOMBRE DE CIUDAD Y PAÍS CON TIPOGRAFÍA REFINADA (MONTSERRAT 300) -->
                        <h3 class="text-xl font-light text-white tracking-wide group-hover:text-[#C5A880] transition-colors mb-1">
                            <?= htmlspecialchars($d['ciudad']) ?>
                        </h3>
                        <p class="text-xs font-light tracking-[0.15em] text-[#C5A880]/70 uppercase mb-3"><?= htmlspecialchars($d['pais']) ?></p>
                        
                        <p class="text-xs text-slate-300/80 font-light leading-relaxed line-clamp-2 mb-6 tracking-wide">
                            <?= htmlspecialchars($d['desc']) ?>
                        </p>
                    </div>

                    <!-- BOTÓN DE BÚSQUEDA ELEGANTE -->
                    <a href="index.php?action=buscar&destino=<?= urlencode($d['ciudad']) ?>" 
                       class="w-full py-2.5 bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] font-light tracking-[0.15em] uppercase text-[11px] rounded-xl transition-all duration-300 text-center flex items-center justify-center space-x-2 shadow-sm group/btn">
                        <svg class="w-3.5 h-3.5 text-[#C5A880] group-hover/btn:text-[#0A1628] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Volar a <?= htmlspecialchars($d['ciudad']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ALERTA DE NO RESULTADOS -->
        <div id="noResultados" class="hidden text-center py-20 space-y-4">
            <div class="text-3xl text-[#C5A880]">✦</div>
            <h3 class="text-lg font-light text-white tracking-wider">No se encontraron destinos</h3>
            <p class="text-xs text-slate-400 font-light tracking-wide">Intente buscar por ciudad o código IATA como "Cusco", "Lima" o "MAD".</p>
        </div>

    </section>

</main>

<!-- SCRIPT DE FILTRADO Y BÚSQUEDA INTERACTIVO -->
<script>
let categoriaActual = 'todos';

function filtrarCategoria(cat) {
    categoriaActual = cat;

    // Estilos dinámicos para los botones de filtro
    document.querySelectorAll('.btn-filtro').forEach(btn => {
        btn.classList.remove('bg-[#C5A880]', 'text-[#0A1628]', 'shadow-lg', 'shadow-[#C5A880]/20');
        btn.classList.add('bg-[#132238]/60', 'text-slate-300', 'border', 'border-[#C5A880]/20');
    });

    const activeBtn = document.getElementById('btn-cat-' + cat);
    if (activeBtn) {
        activeBtn.classList.remove('bg-[#132238]/60', 'text-slate-300', 'border', 'border-[#C5A880]/20');
        activeBtn.classList.add('bg-[#C5A880]', 'text-[#0A1628]', 'shadow-lg', 'shadow-[#C5A880]/20');
    }

    filtrarDestinos();
}

function filtrarDestinos() {
    const query = document.getElementById('inputBuscarDestino').value.toLowerCase().trim();
    const tarjetas = document.querySelectorAll('.card-destino');
    let visibles = 0;

    tarjetas.forEach(card => {
        const catCard = card.getAttribute('data-cat');
        const searchData = card.getAttribute('data-search');

        const coincideCategoria = (categoriaActual === 'todos' || catCard === categoriaActual);
        const coincideBusqueda = (query === '' || searchData.includes(query));

        if (coincideCategoria && coincideBusqueda) {
            card.classList.remove('hidden');
            visibles++;
        } else {
            card.classList.add('hidden');
        }
    });

    const noRes = document.getElementById('noResultados');
    if (visibles === 0) {
        noRes.classList.remove('hidden');
    } else {
        noRes.classList.add('hidden');
    }
}
</script>
