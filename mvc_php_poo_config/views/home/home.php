<main>
    <section id="hero-section" class="relative bg-cover bg-center h-[600px] flex flex-col justify-center items-center transition-all duration-1000" style="background-image: url('assets/img/hero_paris.png');">
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 text-center text-white px-4 mt-[-100px]">
            <div class="inline-flex items-center gap-2 bg-black/30 px-4 py-1.5 rounded-full text-sm font-medium mb-6 border border-white/20">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Powered by Google Gemini AI
            </div>
            <h1 class="text-5xl md:text-6xl font-extrabold mb-4 tracking-tight">Tu próximo viaje,<br>a una frase de distancia.</h1>
            <p class="text-lg md:text-xl font-light">Dile a nuestra IA dónde y cuándo quieres ir.</p>
        </div>

        <div class="absolute -bottom-16 w-full max-w-4xl px-4 z-20">
            <div class="bg-white rounded-2xl shadow-2xl p-6">
                <!-- Pestañas -->
                <div class="flex gap-6 border-b border-gray-200 mb-6 pb-2">
                    <button type="button" onclick="cambiarTab('ia')" id="tab-ia" class="text-[#0070F3] font-bold border-b-2 border-[#0070F3] pb-2 flex items-center gap-2 transition-colors">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Búsqueda Inteligente
                    </button>
                    <button type="button" onclick="cambiarTab('clasica')" id="tab-clasica" class="text-gray-400 font-medium pb-2 flex items-center gap-2 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-magnifying-glass"></i> Búsqueda Clásica
                    </button>
                </div>
                
                <!-- Formulario IA -->
                <form id="form-ia" method="GET" action="index.php" class="flex flex-col md:flex-row gap-4">
                    <input type="hidden" name="action" value="buscar">
                    <div class="relative flex-1">
                        <input type="text" name="query" required placeholder="Ej: Deseo viajar a París desde Lima, con mi esposa el 25 de julio" class="w-full pl-6 pr-12 py-4 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3] transition-all">
                        <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#0070F3] hover:text-[#0051CC]">
                            <i class="fa-solid fa-microphone text-xl"></i>
                        </button>
                    </div>
                    <button type="submit" class="bg-[#0070F3] hover:bg-[#0051CC] text-white px-10 py-4 rounded-xl font-bold text-lg transition-all shadow-md">
                        Buscar
                    </button>
                </form>

                <!-- Formulario Clásico -->
                <form id="form-clasica" method="GET" action="index.php" class="hidden flex-col gap-4">
                    <input type="hidden" name="action" value="buscar">
                    
                    <!-- Toggle Tipo de Viaje -->
                    <div class="flex items-center gap-4 mb-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="tipo_viaje" value="ida_vuelta" checked class="w-4 h-4 accent-[#0070F3]" onchange="toggleRetorno(this.value)">
                            <span class="text-sm font-bold text-[#0A192F] group-hover:text-[#0070F3] transition-colors">Ida y vuelta</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="tipo_viaje" value="solo_ida" class="w-4 h-4 accent-[#0070F3]" onchange="toggleRetorno(this.value)">
                            <span class="text-sm font-bold text-gray-500 group-hover:text-[#0070F3] transition-colors">Solo ida</span>
                        </label>
                    </div>
                    
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="relative md:col-span-1">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-plane-departure"></i></span>
                                <select name="origen" required class="w-full pl-10 pr-4 py-4 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3] transition-all appearance-none bg-white">
                                    <option value="" disabled selected>Origen</option>
                                    <option value="Lima">Lima (LIM)</option>
                                    <option value="Cusco">Cusco (CUZ)</option>
                                    <option value="Arequipa">Arequipa (AQP)</option>
                                    <option value="Bogotá">Bogotá (BOG)</option>
                                    <option value="Madrid">Madrid (MAD)</option>
                                    <option value="París">París (CDG)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                            </div>
                            <div class="relative md:col-span-1">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-plane-arrival"></i></span>
                                <select name="destino" required class="w-full pl-10 pr-4 py-4 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3] transition-all appearance-none bg-white">
                                    <option value="" disabled selected>Destino</option>
                                    <option value="Lima">Lima (LIM)</option>
                                    <option value="Cusco">Cusco (CUZ)</option>
                                    <option value="Arequipa">Arequipa (AQP)</option>
                                    <option value="Bogotá">Bogotá (BOG)</option>
                                    <option value="Madrid">Madrid (MAD)</option>
                                    <option value="París">París (CDG)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                            </div>
                            <div class="relative md:col-span-1">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-calendar"></i></span>
                                <input type="text" name="rango_fechas" id="rango_fechas" required placeholder="Fechas de viaje" class="w-full pl-10 pr-4 py-4 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3] transition-all bg-white text-gray-700 bg-white">
                            </div>
                            <div class="relative md:col-span-1">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-users"></i></span>
                                <input type="number" name="pasajeros" min="1" value="1" required class="w-full pl-10 pr-4 py-4 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3] transition-all">
                            </div>
                        </div>

                        <button type="submit" class="bg-[#0070F3] hover:bg-[#0051CC] text-white px-10 py-4 rounded-xl font-bold text-lg transition-all shadow-md shrink-0">
                            Buscar
                        </button>
                    </div>
                </form>

                <p id="tip-text" class="text-center text-xs text-gray-400 mt-4"><i class="fa-regular fa-lightbulb text-yellow-500"></i> Tip: dile a la IA tu destino, fechas y número de personas</p>

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

    <section class="max-w-[1560px] mx-auto px-12 py-16">
        <h2 class="text-3xl font-bold text-[#0A2540] mb-8">Tipos de vuelos</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 group">
                <div class="h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1587595431973-160d0d94add1?auto=format&fit=crop&w=600&q=80" alt="Vuelos nacionales" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-[#0A2540] mb-2">Vuelos nacionales</h3>
                    <p class="text-gray-500 text-sm mb-4">Lima · Cusco · Arequipa · Iquitos</p>
                    <a href="?action=destinos" class="text-[#0070F3] font-semibold hover:underline flex items-center gap-2">Ver vuelos <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 group">
                <div class="h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=600&q=80" alt="Vuelos internacionales" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-[#0A2540] mb-2">Vuelos internacionales</h3>
                    <p class="text-gray-500 text-sm mb-4">Madrid · Miami · Bogotá · Buenos Aires</p>
                    <a href="?action=destinos" class="text-[#0070F3] font-semibold hover:underline flex items-center gap-2">Ver vuelos <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 group">
                <div class="h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1494515843206-f3117d3f51b7?auto=format&fit=crop&w=600&q=80" alt="Vuelos multidestino" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-[#0A2540] mb-2">Vuelos multidestino</h3>
                    <p class="text-gray-500 text-sm mb-4">Varias ciudades en un solo viaje</p>
                    <a href="?action=destinos" class="text-[#0070F3] font-semibold hover:underline flex items-center gap-2">Ver vuelos <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

        </div>
    </section>

    <section class="max-w-[1560px] mx-auto px-12 py-10 bg-white">
        <h2 class="text-3xl font-bold text-[#0A2540] mb-2">Ofertas y promociones</h2>
        <p class="text-gray-500 mb-8">Precios actualizados por IA cada hora</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-50 rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 flex flex-col group">
                <a href="?action=ofertas" class="relative block h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1552074284-5e88ef1aef18?auto=format&fit=crop&w=600&q=80" alt="Cancún" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <span class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10"><i class="fa-solid fa-fire"></i> HOT</span>
                </a>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-[#0A2540] mb-1">Lima → Cancún</h3>
                        <p class="text-gray-500 text-sm mb-4">Mañana · 1 escala</p>
                    </div>
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-gray-400">desde</p>
                            <p class="text-2xl font-bold text-[#0070F3]">S/. 890</p>
                        </div>
                        <a href="?action=buscar&destino=Miami" class="bg-[#0070F3] hover:bg-[#0051CC] text-white px-6 py-2.5 rounded-lg font-semibold transition-colors text-center">Reservar</a>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 flex flex-col group">
                <a href="?action=ofertas" class="relative block h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1543783207-ec64e4d95325?auto=format&fit=crop&w=600&q=80" alt="Madrid" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <span class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10"><i class="fa-regular fa-calendar-days"></i> Temporada</span>
                </a>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-[#0A2540] mb-1">Lima → Madrid</h3>
                        <p class="text-gray-500 text-sm mb-4">Jul–Sep</p>
                    </div>
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-gray-400">desde</p>
                            <p class="text-2xl font-bold text-[#0070F3]">S/. 2,100</p>
                        </div>
                        <a href="?action=buscar&destino=Madrid" class="bg-[#0070F3] hover:bg-[#0051CC] text-white px-6 py-2.5 rounded-lg font-semibold transition-colors text-center">Reservar</a>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 flex flex-col group">
                <a href="?action=ofertas" class="relative block h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=600&q=80" alt="París" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <span class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10"><i class="fa-solid fa-globe"></i> Especial</span>
                </a>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-[#0A2540] mb-1">Especial Europa</h3>
                        <p class="text-gray-500 text-sm mb-4">Madrid · París · Roma</p>
                    </div>
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-gray-400">desde</p>
                            <p class="text-2xl font-bold text-[#0070F3]">S/. 3,450</p>
                        </div>
                        <a href="?action=buscar&destino=París" class="bg-[#0070F3] hover:bg-[#0051CC] text-white px-6 py-2.5 rounded-lg font-semibold transition-colors text-center">Reservar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-[1560px] mx-auto px-12 py-16">
        <h2 class="text-3xl font-bold text-[#0A2540] mb-8">Destinos populares</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="relative rounded-2xl overflow-hidden h-64 tarjeta-animada shadow-sm group">
                <img src="https://images.unsplash.com/photo-1552074284-5e88ef1aef18?auto=format&fit=crop&w=600&q=80" alt="Cancún" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-5 left-5">
                    <h3 class="text-2xl font-bold text-white mb-2">Cancún</h3>
                    <span class="bg-[#0070F3] text-white px-3 py-1 rounded-md font-bold text-sm">S/. 890</span>
                </div>
            </div>

            <div class="relative rounded-2xl overflow-hidden h-64 tarjeta-animada shadow-sm group">
                <img src="https://images.unsplash.com/photo-1543783207-ec64e4d95325?auto=format&fit=crop&w=600&q=80" alt="Madrid" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-5 left-5">
                    <h3 class="text-2xl font-bold text-white mb-2">Madrid</h3>
                    <span class="bg-[#0070F3] text-white px-3 py-1 rounded-md font-bold text-sm">S/. 2,100</span>
                </div>
            </div>

            <div class="relative rounded-2xl overflow-hidden h-64 tarjeta-animada shadow-sm group">
                <img src="https://images.unsplash.com/photo-1514214246283-d427a95c5d2f?auto=format&fit=crop&w=600&q=80" alt="Miami" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-5 left-5">
                    <h3 class="text-2xl font-bold text-white mb-2">Miami</h3>
                    <span class="bg-[#0070F3] text-white px-3 py-1 rounded-md font-bold text-sm">S/. 1,250</span>
                </div>
            </div>

            <div class="relative rounded-2xl overflow-hidden h-64 tarjeta-animada shadow-sm group">
                <img src="https://images.unsplash.com/photo-1589909202802-8f4aadce1849?auto=format&fit=crop&w=600&q=80" alt="Buenos Aires" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-5 left-5">
                    <h3 class="text-2xl font-bold text-white mb-2">Buenos Aires</h3>
                    <span class="bg-[#0070F3] text-white px-3 py-1 rounded-md font-bold text-sm">S/. 650</span>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-[1560px] mx-auto px-12 py-10 pb-20">
        <h2 class="text-3xl font-bold text-[#0A2540] mb-8">Check-in y gestión</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 group">
                <div class="h-40 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=600&q=80" alt="Estado del vuelo" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-[#0A2540] mb-1">Estado del vuelo</h3>
                    <p class="text-gray-500 text-sm mb-4">Consulta en tiempo real</p>
                    <a href="?action=checkin" class="text-[#0070F3] font-semibold hover:underline flex items-center gap-2">Ver más <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 group">
                <div class="h-40 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80" alt="Cambios" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-[#0A2540] mb-1">Cambios y cancelaciones</h3>
                    <p class="text-gray-500 text-sm mb-4">Gestiona cambios o cancela</p>
                    <a href="?action=checkin" class="text-[#0070F3] font-semibold hover:underline flex items-center gap-2">Ver más <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden tarjeta-animada border border-gray-100 group">
                <div class="h-40 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1556012018-50c5c0da73bf?auto=format&fit=crop&w=600&q=80" alt="Equipaje" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-[#0A2540] mb-1">Políticas de equipaje</h3>
                    <p class="text-gray-500 text-sm mb-4">Límites por aerolínea</p>
                    <a href="?action=checkin" class="text-[#0070F3] font-semibold hover:underline flex items-center gap-2">Ver más <i class="fa-solid fa-arrow-right text-sm"></i></a>
                </div>
            </div>
        </div>
    </section>
</main>
