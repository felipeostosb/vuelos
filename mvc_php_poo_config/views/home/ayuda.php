<main class="bg-[#0A1628] pb-24 text-white font-sans min-h-screen">
    
    <section class="relative bg-gradient-to-b from-[#0A1628] via-[#132238] to-[#0A1628] border-b border-[#C5A880]/15 text-white py-20 px-6 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#C5A880]/15 via-transparent to-transparent pointer-events-none"></div>
        <div class="max-w-6xl mx-auto text-center relative z-10 space-y-4">
            <span class="px-5 py-2 bg-[#C5A880]/10 text-[#C5A880] border border-[#C5A880]/30 text-sm font-light tracking-[0.25em] uppercase rounded-full inline-block">
                Centro de Atención & Soporte
            </span>
            <h1 class="text-3xl md:text-5xl font-light tracking-[0.06em] text-white">Ayuda & Atención al Pasajero</h1>
            <p class="text-[#C5A880]/80 text-sm md:text-base max-w-2xl mx-auto font-light tracking-wide">Estamos a su servicio para hacer de su experiencia aeronáutica un viaje impecable</p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <section class="lg:col-span-2 space-y-4">
            <h2 class="text-xl md:text-2xl font-light text-[#C5A880] tracking-wider mb-6 uppercase flex items-center gap-3">
                <span>✦</span> Preguntas Frecuentes
            </h2>
            
            <div class="bg-[#132238]/60 backdrop-blur-md rounded-2xl shadow-lg border border-[#C5A880]/20 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-1')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-light text-white text-base tracking-wide">¿Cómo funciona la búsqueda con Inteligencia Artificial?</span>
                    <i id="icon-faq-1" class="fa-solid fa-chevron-down text-[#C5A880] transition-transform duration-300"></i>
                </button>
                <div id="faq-1" class="hidden px-6 pb-6 text-slate-300 font-light text-sm leading-relaxed border-t border-[#C5A880]/15 pt-4 bg-[#0A1628]/40">
                    Nuestra tecnología de IA analiza miles de combinaciones aeronáuticas en tiempo real para brindarle las tarifas más convenientes y personalizadas según sus preferencias.
                </div>
            </div>

            <div class="bg-[#132238]/60 backdrop-blur-md rounded-2xl shadow-lg border border-[#C5A880]/20 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-2')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-light text-white text-base tracking-wide">¿Puedo gestionar o modificar mi reserva de vuelo?</span>
                    <i id="icon-faq-2" class="fa-solid fa-chevron-down text-[#C5A880] transition-transform duration-300"></i>
                </button>
                <div id="faq-2" class="hidden px-6 pb-6 text-slate-300 font-light text-sm leading-relaxed border-t border-[#C5A880]/15 pt-4 bg-[#0A1628]/40">
                    Sí, los cambios se pueden realizar directamente desde su panel "Mis Viajes" o contactando a nuestro centro de atención técnica con su código PNR.
                </div>
            </div>

            <div class="bg-[#132238]/60 backdrop-blur-md rounded-2xl shadow-lg border border-[#C5A880]/20 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-3')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-light text-white text-base tracking-wide">¿Cómo realizo mi Check-in digital?</span>
                    <i id="icon-faq-3" class="fa-solid fa-chevron-down text-[#C5A880] transition-transform duration-300"></i>
                </button>
                <div id="faq-3" class="hidden px-6 pb-6 text-slate-300 font-light text-sm leading-relaxed border-t border-[#C5A880]/15 pt-4 bg-[#0A1628]/40">
                    Acceda a la sección Check-in en la barra de navegación e ingrese su código de reserva PNR y su apellido desde 24 horas antes del despegue.
                </div>
            </div>

            <div class="bg-[#132238]/60 backdrop-blur-md rounded-2xl shadow-lg border border-[#C5A880]/20 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-4')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-light text-white text-base tracking-wide">¿Qué métodos de pago son aceptados?</span>
                    <i id="icon-faq-4" class="fa-solid fa-chevron-down text-[#C5A880] transition-transform duration-300"></i>
                </button>
                <div id="faq-4" class="hidden px-6 pb-6 text-slate-300 font-light text-sm leading-relaxed border-t border-[#C5A880]/15 pt-4 bg-[#0A1628]/40">
                    Aceptamos Visa, Mastercard, American Express y soluciones bancarias digitales locales con cifrado de seguridad bancario.
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <div class="bg-[#132238]/60 backdrop-blur-md p-6 rounded-2xl shadow-lg border border-[#C5A880]/20 text-center flex flex-col items-center">
                <h3 class="text-lg font-light text-white mb-1 tracking-wide">Atención Vía WhatsApp</h3>
                <p class="text-sm text-[#C5A880]/80 font-light mb-6">Respuesta personalizada en minutos</p>
                <a href="https://wa.me/5112345678" target="_blank" 
                   class="w-full bg-[#15A850] hover:bg-[#118F43] text-white py-3 rounded-xl font-light text-sm uppercase tracking-widest transition-colors text-center block">
                    Chatear en Directo
                </a>
            </div>

            <div class="bg-[#132238]/60 backdrop-blur-md p-6 rounded-2xl shadow-lg border border-[#C5A880]/20">
                <h3 class="text-lg font-light text-[#C5A880] text-center mb-6 tracking-wide uppercase">Asistencia Técnica</h3>
                <form action="index.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="procesar_soporte">
                    <div>
                        <input type="text" name="nombre" placeholder="Nombre completo" required 
                               class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 rounded-xl px-4 py-3 text-sm font-light text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880] transition-colors">
                    </div>
                    <div>
                        <input type="email" name="email" placeholder="Correo electrónico" required 
                               class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 rounded-xl px-4 py-3 text-sm font-light text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880] transition-colors">
                    </div>
                    <div>
                        <textarea name="mensaje" placeholder="Describa su consulta o requerimiento..." rows="4" required 
                                  class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 rounded-xl px-4 py-3 text-sm font-light text-white placeholder-slate-500 focus:outline-none focus:border-[#C5A880] transition-colors resize-none"></textarea>
                    </div>
                    <button type="submit" 
                            class="w-full bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] py-3 rounded-xl font-light text-sm uppercase tracking-widest transition-all duration-300 text-center">
                        Enviar Consulta
                    </button>
                </form>
            </div>
        </aside>

    </div>
</main>

<script>
function toggleAccordion(id) {
    const content = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
