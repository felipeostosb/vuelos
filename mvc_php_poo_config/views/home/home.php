<main>
    <section id="hero-section" class="hero-section" style="background-image: url('assets/img/hero_paris.png');">
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <div class="hero-badge">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Powered by Google Gemini AI
            </div>
            <h1 class="hero-title">Tu próximo viaje,<br>a una frase de distancia.</h1>
            <p class="hero-subtitle">Dile a nuestra IA dónde y cuándo quieres ir.</p>
        </div>

        <div class="search-widget-wrapper">
            <div class="search-widget">
                <!-- Pestañas -->
                <div class="search-tabs">
                    <button type="button" onclick="cambiarTab('ia')" id="tab-ia" class="tab-btn tab-btn--active">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Búsqueda Inteligente
                    </button>
                    <button type="button" onclick="cambiarTab('clasica')" id="tab-clasica" class="tab-btn tab-btn--inactive">
                        <i class="fa-solid fa-magnifying-glass"></i> Búsqueda Clásica
                    </button>
                </div>
                
                <!-- Formulario IA -->
                <form id="form-ia" method="GET" action="index.php" class="form-ia">
                    <input type="hidden" name="action" value="buscar">
                    <div class="input-container">
                        <input type="text" name="query" required placeholder="Ej: Deseo viajar a París desde Lima, con mi esposa el 25 de julio" class="search-input search-input--ia">
                        <button type="button" class="input-icon-btn">
                            <i class="fa-solid fa-microphone text-xl"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn--primary btn--large">
                        Buscar
                    </button>
                </form>

                <!-- Formulario Clásico -->
                <form id="form-clasica" method="GET" action="index.php" class="form-clasica">
                    <input type="hidden" name="action" value="buscar">
                    
                    <!-- Toggle Tipo de Viaje -->
                    <div class="trip-type-toggles">
                        <label class="radio-label">
                            <input type="radio" name="tipo_viaje" value="ida_vuelta" checked onchange="toggleRetorno(this.value)">
                            <span class="radio-text radio-text--active">Ida y vuelta</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="tipo_viaje" value="solo_ida" onchange="toggleRetorno(this.value)">
                            <span class="radio-text radio-text--inactive">Solo ida</span>
                        </label>
                    </div>
                    
                    <div class="search-fields-container">
                        <div class="search-grid">
                            <div class="input-container">
                                <span class="input-icon-left"><i class="fa-solid fa-plane-departure"></i></span>
                                <select name="origen" required class="search-input">
                                    <option value="" disabled selected>Origen</option>
                                    <option value="Lima">Lima (LIM)</option>
                                    <option value="Cusco">Cusco (CUZ)</option>
                                    <option value="Arequipa">Arequipa (AQP)</option>
                                    <option value="Bogotá">Bogotá (BOG)</option>
                                    <option value="Madrid">Madrid (MAD)</option>
                                    <option value="París">París (CDG)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down input-icon-right"></i>
                            </div>
                            <div class="input-container">
                                <span class="input-icon-left"><i class="fa-solid fa-plane-arrival"></i></span>
                                <select name="destino" required class="search-input">
                                    <option value="" disabled selected>Destino</option>
                                    <option value="Lima">Lima (LIM)</option>
                                    <option value="Cusco">Cusco (CUZ)</option>
                                    <option value="Arequipa">Arequipa (AQP)</option>
                                    <option value="Bogotá">Bogotá (BOG)</option>
                                    <option value="Madrid">Madrid (MAD)</option>
                                    <option value="París">París (CDG)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down input-icon-right"></i>
                            </div>
                            <div class="input-container">
                                <span class="input-icon-left"><i class="fa-solid fa-calendar"></i></span>
                                <input type="text" name="rango_fechas" id="rango_fechas" required placeholder="Fechas de viaje" class="search-input">
                            </div>
                            <div class="input-container">
                                <span class="input-icon-left"><i class="fa-solid fa-users"></i></span>
                                <input type="number" name="pasajeros" min="1" value="1" required class="search-input">
                            </div>
                        </div>

                        <button type="submit" class="btn btn--primary btn--large">
                            Buscar
                        </button>
                    </div>
                </form>

                <p id="tip-text" class="tip-text"><i class="fa-regular fa-lightbulb text-yellow-500"></i> Tip: dile a la IA tu destino, fechas y número de personas</p>

                <script>
                    const backgrounds = [
                        'assets/img/hero_paris.png',
                        'assets/img/hero_peru.png',
                        'assets/img/hero_maldives.png'
                    ];
                    let currentBg = 0;
                    setInterval(() => {
                        currentBg = (currentBg + 1) % backgrounds.length;
                        document.getElementById('hero-section').style.backgroundImage = `url('${backgrounds[currentBg]}')`;
                    }, 5000);
                </script>

                <script>
                    let fpInstance = null;

                    function cambiarTab(tab) {
                        const formIa = document.getElementById('form-ia');
                        const formClasica = document.getElementById('form-clasica');
                        const tabIa = document.getElementById('tab-ia');
                        const tabClasica = document.getElementById('tab-clasica');
                        const tipText = document.getElementById('tip-text');

                        if (tab === 'ia') {
                            formIa.style.display = 'flex';
                            formClasica.style.display = 'none';
                            
                            tabIa.classList.add('text-[#0070F3]', 'border-b-2', 'border-[#0070F3]', 'font-bold');
                            tabIa.classList.remove('text-gray-400', 'font-medium');
                            
                            tabClasica.classList.remove('text-[#0070F3]', 'border-b-2', 'border-[#0070F3]', 'font-bold');
                            tabClasica.classList.add('text-gray-400', 'font-medium');
                            
                            tipText.innerHTML = '<i class="fa-regular fa-lightbulb text-yellow-500"></i> Tip: dile a la IA tu destino, fechas y número de personas';
                        } else {
                            formIa.style.display = 'none';
                            formClasica.style.display = 'flex';
                            
                            tabClasica.classList.add('text-[#0070F3]', 'border-b-2', 'border-[#0070F3]', 'font-bold');
                            tabClasica.classList.remove('text-gray-400', 'font-medium');
                            
                            tabIa.classList.remove('text-[#0070F3]', 'border-b-2', 'border-[#0070F3]', 'font-bold');
                            tabIa.classList.add('text-gray-400', 'font-medium');
                            
                            tipText.innerHTML = '<i class="fa-regular fa-lightbulb text-yellow-500"></i> Tip: Selecciona tus fechas y origen/destino manualmente';
                        }
                    }

                    function toggleRetorno(valor) {
                        const inputFechas = document.getElementById('rango_fechas');
                        
                        if (fpInstance) {
                            fpInstance.destroy(); // Destruimos la instancia vieja
                        }

                        // Recreamos Flatpickr dependiendo del modo
                        if (valor === 'ida_vuelta') {
                            fpInstance = flatpickr(inputFechas, {
                                mode: "range",
                                locale: "es",
                                minDate: "today",
                                dateFormat: "Y-m-d",
                                placeholder: "Ida y Vuelta"
                            });
                        } else {
                            fpInstance = flatpickr(inputFechas, {
                                mode: "single",
                                locale: "es",
                                minDate: "today",
                                dateFormat: "Y-m-d",
                                placeholder: "Fecha de Ida"
                            });
                        }
                    }
                    
                    // Inicializar
                    document.addEventListener('DOMContentLoaded', () => {
                        const selectedRadio = document.querySelector('input[name="tipo_viaje"]:checked');
                        if(selectedRadio) toggleRetorno(selectedRadio.value);
                    });
                </script>
            </div>
        </div>
    </section>

    <div class="h-24"></div>

    <section class="section-container">
        <h2 class="section-title">Tipos de vuelos</h2>
        <div class="cards-grid">
            
            <div class="card group">
                <a href="?action=destinos" class="card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1587595431973-160d0d94add1?auto=format&fit=crop&w=600&q=80" alt="Vuelos nacionales" class="card-image">
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title">Vuelos nacionales</h3>
                        <p class="card-subtitle">Lima · Cusco · Arequipa · Iquitos</p>
                    </div>
                    <a href="?action=destinos" class="card-link">Ver vuelos <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

            <div class="card group">
                <a href="?action=destinos" class="card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=600&q=80" alt="Vuelos internacionales" class="card-image">
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title">Vuelos internacionales</h3>
                        <p class="card-subtitle">Madrid · Miami · Bogotá · Buenos Aires</p>
                    </div>
                    <a href="?action=destinos" class="card-link">Ver vuelos <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

            <div class="card group">
                <a href="?action=destinos" class="card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1494515843206-f3117d3f51b7?auto=format&fit=crop&w=600&q=80" alt="Vuelos multidestino" class="card-image">
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title">Vuelos multidestino</h3>
                        <p class="card-subtitle">Varias ciudades en un solo viaje</p>
                    </div>
                    <a href="?action=destinos" class="card-link">Ver vuelos <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

        </div>
    </section>

    <section class="section-container" style="background-color: white;">
        <h2 class="section-title" style="margin-bottom: 0.5rem;">Ofertas y promociones</h2>
        <p class="section-subtitle">Precios actualizados por IA cada hora</p>
        
        <div class="cards-grid">
            <div class="card group">
                <a href="?action=buscar&destino=Miami" class="card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1552074284-5e88ef1aef18?auto=format&fit=crop&w=600&q=80" alt="Cancún" class="card-image">
                    <span class="card-badge"><i class="fa-solid fa-fire"></i> HOT</span>
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title">Lima → Cancún</h3>
                        <p class="card-subtitle">Mañana · 1 escala</p>
                    </div>
                    <div class="card-footer">
                        <div>
                            <p class="card-price-label">desde</p>
                            <p class="card-price-value">S/. 890</p>
                        </div>
                        <a href="?action=buscar&destino=Miami" class="btn btn--primary">Reservar</a>
                    </div>
                </div>
            </div>

            <div class="card group">
                <a href="?action=buscar&destino=Madrid" class="card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1543783207-ec64e4d95325?auto=format&fit=crop&w=600&q=80" alt="Madrid" class="card-image">
                    <span class="card-badge" style="background-color: #0070F3;"><i class="fa-regular fa-calendar-days"></i> Temporada</span>
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title">Lima → Madrid</h3>
                        <p class="card-subtitle">Jul–Sep</p>
                    </div>
                    <div class="card-footer">
                        <div>
                            <p class="card-price-label">desde</p>
                            <p class="card-price-value">S/. 2,100</p>
                        </div>
                        <a href="?action=buscar&destino=Madrid" class="btn btn--primary">Reservar</a>
                    </div>
                </div>
            </div>

            <div class="card group">
                <a href="?action=buscar&destino=París" class="card-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=600&q=80" alt="París" class="card-image">
                    <span class="card-badge" style="background-color: #0A192F;"><i class="fa-solid fa-globe"></i> Especial</span>
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title">Especial Europa</h3>
                        <p class="card-subtitle">Madrid · París · Roma</p>
                    </div>
                    <div class="card-footer">
                        <div>
                            <p class="card-price-label">desde</p>
                            <p class="card-price-value">S/. 3,450</p>
                        </div>
                        <a href="?action=buscar&destino=París" class="btn btn--primary">Reservar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-container">
        <h2 class="section-title">Destinos populares</h2>
        
        <div class="cards-grid cards-grid--4">
            <div class="popular-card group">
                <img src="https://images.unsplash.com/photo-1587595431973-160d0d94add1?auto=format&fit=crop&w=600&q=80" alt="Cusco" class="card-image">
                <div class="popular-card-overlay"></div>
                <div class="popular-card-content">
                    <h3 class="popular-card-title">Cusco</h3>
                    <p class="popular-card-subtitle">Vuelos desde S/. 120</p>
                </div>
            </div>
            
            <div class="popular-card group">
                <img src="assets/img/bogota.png" alt="Bogotá" class="card-image">
                <div class="popular-card-overlay"></div>
                <div class="popular-card-content">
                    <h3 class="popular-card-title">Bogotá</h3>
                    <p class="popular-card-subtitle">Vuelos desde S/. 450</p>
                </div>
            </div>

            <div class="popular-card group">
                <img src="https://images.unsplash.com/photo-1543783207-ec64e4d95325?auto=format&fit=crop&w=600&q=80" alt="Madrid" class="card-image">
                <div class="popular-card-overlay"></div>
                <div class="popular-card-content">
                    <h3 class="popular-card-title">Madrid</h3>
                    <p class="popular-card-subtitle">Vuelos directos diarios</p>
                </div>
            </div>

            <div class="popular-card group">
                <img src="assets/img/arequipa.png" alt="Arequipa" class="card-image">
                <div class="popular-card-overlay"></div>
                <div class="popular-card-content">
                    <h3 class="popular-card-title">Arequipa</h3>
                    <p class="popular-card-subtitle">Vuelos desde S/. 110</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-container" style="padding-bottom: 5rem;">
        <h2 class="section-title">Check-in y gestión</h2>
        
        <div class="cards-grid">
            <div class="card group">
                <a href="?action=checkin" class="card-image-wrapper" style="height: 10rem;">
                    <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=600&q=80" alt="Estado del vuelo" class="card-image">
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title" style="font-size: 1.125rem;">Estado del vuelo</h3>
                        <p class="card-subtitle">Consulta en tiempo real</p>
                    </div>
                    <a href="?action=checkin" class="card-link">Ver más <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

            <div class="card group">
                <a href="?action=checkin" class="card-image-wrapper" style="height: 10rem;">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80" alt="Cambios" class="card-image">
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title" style="font-size: 1.125rem;">Cambios y cancelaciones</h3>
                        <p class="card-subtitle">Gestiona cambios o cancela</p>
                    </div>
                    <a href="?action=checkin" class="card-link">Ver más <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

            <div class="card group">
                <a href="?action=checkin" class="card-image-wrapper" style="height: 10rem;">
                    <img src="https://images.unsplash.com/photo-1556012018-50c5c0da73bf?auto=format&fit=crop&w=600&q=80" alt="Equipaje" class="card-image">
                </a>
                <div class="card-content">
                    <div>
                        <h3 class="card-title" style="font-size: 1.125rem;">Políticas de equipaje</h3>
                        <p class="card-subtitle">Límites por aerolínea</p>
                    </div>
                    <a href="?action=checkin" class="card-link">Ver más <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>
        </div>
    </section>
</main>
