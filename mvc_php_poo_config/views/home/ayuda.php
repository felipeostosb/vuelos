<main class="bg-brand-blue pb-20 text-white">
    
    <section class="bg-gradient-to-r from-brand-purple to-brand-rose border-b border-brand-gold/15 text-white py-20">
        <div class="max-w-[1280px] mx-auto px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 font-sans">Ayuda y soporte</h1>
            <p class="text-lg md:text-xl text-brand-gold/80">Estamos aquí para ayudarte</p>
        </div>
    </section>

    <div class="max-w-[1280px] mx-auto px-8 mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <section class="lg:col-span-2 space-y-4">
            <h2 class="text-2xl font-bold text-brand-gold mb-6 font-sans">Preguntas frecuentes</h2>
            
            <div class="bg-[#132238]/70 backdrop-blur-md rounded-2xl shadow-lg border border-brand-gold/15 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-1')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-bold text-white text-base">¿Cómo funciona la búsqueda con IA?</span>
                    <i id="icon-faq-1" class="fa-solid fa-chevron-down text-brand-gold transition-transform duration-300"></i>
                </button>
                <div id="faq-1" class="hidden px-6 pb-6 text-brand-gold/80 text-sm leading-relaxed border-t border-brand-gold/10 pt-4 bg-[#0a1628]/30">
                    Nuestra inteligencia artificial analiza miles de combinaciones de rutas, horarios y tarifas en tiempo real para ofrecerte las opciones más optimizadas, económicas y personalizadas según tus preferencias de viaje.
                </div>
            </div>

            <div class="bg-[#132238]/70 backdrop-blur-md rounded-2xl shadow-lg border border-brand-gold/15 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-2')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-bold text-white text-base">¿Puedo cancelar mi reserva?</span>
                    <i id="icon-faq-2" class="fa-solid fa-chevron-down text-brand-gold transition-transform duration-300"></i>
                </button>
                <div id="faq-2" class="hidden px-6 pb-6 text-brand-gold/80 text-sm leading-relaxed border-t border-brand-gold/10 pt-4 bg-[#0a1628]/30">
                    Sí, las cancelaciones se pueden gestionar directamente desde el panel de gestión. Los reembolsos o penalizaciones aplicables dependerán estrictamente de las condiciones y regulaciones de la tarifa que compraste.
                </div>
            </div>

            <div class="bg-[#132238]/70 backdrop-blur-md rounded-2xl shadow-lg border border-brand-gold/15 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-3')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-bold text-white text-base">¿Cómo hago el check-in?</span>
                    <i id="icon-faq-3" class="fa-solid fa-chevron-down text-brand-gold transition-transform duration-300"></i>
                </button>
                <div id="faq-3" class="hidden px-6 pb-6 text-brand-gold/80 text-sm leading-relaxed border-t border-brand-gold/10 pt-4 bg-[#0a1628]/30">
                    Puedes realizar tu Check-in digital de manera rápida accediendo a la pestaña dedicada en nuestro menú superior, introduciendo tu código de reserva y el apellido registrado 48 horas antes de tu vuelo.
                </div>
            </div>

            <div class="bg-[#132238]/70 backdrop-blur-md rounded-2xl shadow-lg border border-brand-gold/15 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-4')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-bold text-white text-base">¿Qué formas de pago aceptan?</span>
                    <i id="icon-faq-4" class="fa-solid fa-chevron-down text-brand-gold transition-transform duration-300"></i>
                </button>
                <div id="faq-4" class="hidden px-6 pb-6 text-brand-gold/80 text-sm leading-relaxed border-t border-brand-gold/10 pt-4 bg-[#0a1628]/30">
                    Aceptamos todas las principales tarjetas de crédito y débito (Visa, Mastercard, American Express), así como pagos digitales locales seguros y transferencias bancarias según tu país de residencia.
                </div>
            </div>

            <div class="bg-[#132238]/70 backdrop-blur-md rounded-2xl shadow-lg border border-brand-gold/15 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-5')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-bold text-white text-base">¿Hay cargos por equipaje?</span>
                    <i id="icon-faq-5" class="fa-solid fa-chevron-down text-brand-gold transition-transform duration-300"></i>
                </button>
                <div id="faq-5" class="hidden px-6 pb-6 text-brand-gold/80 text-sm leading-relaxed border-t border-brand-gold/10 pt-4 bg-[#0a1628]/30">
                    Cada tarifa incluye una franquicia de equipaje distinta (por ejemplo, bolso de mano o bodega). Puedes verificar el costo detallado por pieza adicional en nuestra tabla de políticas dentro de la sección Check-in.
                </div>
            </div>

            <div class="bg-[#132238]/70 backdrop-blur-md rounded-2xl shadow-lg border border-brand-gold/15 overflow-hidden">
                <button type="button" onclick="toggleAccordion('faq-6')" class="w-full p-6 text-left flex justify-between items-center hover:bg-white/5 transition-colors focus:outline-none">
                    <span class="font-bold text-white text-base">¿Cómo contacto al soporte?</span>
                    <i id="icon-faq-6" class="fa-solid fa-chevron-down text-brand-gold transition-transform duration-300"></i>
                </button>
                <div id="faq-6" class="hidden px-6 pb-6 text-brand-gold/80 text-sm leading-relaxed border-t border-brand-gold/10 pt-4 bg-[#0a1628]/30">
                    Si necesitas atención inmediata, puedes usar el botón de chat rápido vía WhatsApp a la derecha, o rellenar el formulario de asistencia técnica para que un asesor especializado responda a tu correo electrónico.
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            
            <div class="bg-[#132238]/70 backdrop-blur-md p-6 rounded-2xl shadow-lg border border-brand-gold/15 text-center flex flex-col items-center tarjeta-animada">
                <h3 class="text-xl font-bold text-white mb-1 font-sans">WhatsApp</h3>
                <p class="text-xs text-brand-gold/60 mb-6">Respuesta en 10 min</p>
                <a href="https://wa.me/5112345678" target="_blank" class="w-full bg-[#15A850] hover:bg-[#118F43] text-white py-3 rounded-xl font-semibold transition-colors text-center block">
                    Chatear ahora
                </a>
            </div>

            <div class="bg-[#132238]/70 backdrop-blur-md p-6 rounded-2xl shadow-lg border border-brand-gold/15 tarjeta-animada">
                <h3 class="text-xl font-bold text-brand-gold text-center mb-6 font-sans">Asistencia</h3>
                <form action="index.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="procesar_soporte">
                    <div>
                        <input type="text" name="nombre" placeholder="Nombre" required class="w-full bg-[#0A1628]/80 border border-brand-gold/30 rounded-xl px-4 py-3 text-sm text-white placeholder-[#6E82A1] focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold transition-colors">
                    </div>
                    <div>
                        <input type="email" name="email" placeholder="Email" required class="w-full bg-[#0A1628]/80 border border-brand-gold/30 rounded-xl px-4 py-3 text-sm text-white placeholder-[#6E82A1] focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold transition-colors">
                    </div>
                    <div>
                        <textarea name="mensaje" placeholder="Mensaje" rows="4" required class="w-full bg-[#0A1628]/80 border border-brand-gold/30 rounded-xl px-4 py-3 text-sm text-white placeholder-[#6E82A1] focus:outline-none focus:border-brand-gold focus:ring-1 focus:ring-brand-gold transition-colors resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-brand-gold to-brand-rose hover:from-brand-gold/90 hover:to-brand-rose/90 text-brand-blue py-3 rounded-xl font-bold transition-all shadow-[0_2px_10px_rgba(197,168,128,0.15)] text-center">
                        Enviar
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
