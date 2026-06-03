<footer class="site-footer">
    <div class="footer__container footer__grid">

        <div class="footer__brand">
            <h4 class="footer__title font-['Open_Sauce_One']">NOVAIRLINES</h4>
            <ul class="footer__links">
                <li><a href="?action=home" class="footer__link">Acerca de nosotros</a></li>
                <li><a href="?action=home" class="footer__link">Cómo funciona la IA</a></li>
                <li><a href="#" class="footer__link">Términos y condiciones</a></li>
                <li><a href="#" class="footer__link">Política de privacidad</a></li>
            </ul>
        </div>

        <div class="footer__brand">
            <h4 class="footer__title">Gestión de viajes</h4>
            <ul class="footer__links">
                <li><a href="?action=checkin" class="footer__link">Check-in online</a></li>
                <li><a href="#" class="footer__link">Estado de vuelo</a></li>
                <li><a href="#" class="footer__link">Cambios y cancelaciones</a></li>
                <li><a href="#" class="footer__link">Equipaje</a></li>
            </ul>
        </div>

        <div class="footer__brand">
            <h4 class="footer__title">Ayuda y soporte</h4>
            <a href="https://wa.me/5112345678" target="_blank" class="btn btn--large" style="background-color: #25D366; color: white; width: 100%; margin-bottom: 1rem; padding: 0.75rem;">
                <i class="fa-brands fa-whatsapp text-lg"></i> WhatsApp
            </a>
            <ul class="footer__links">
                <li><a href="?action=ayuda" class="footer__link">Centro de ayuda</a></li>
                <li><a href="?action=ayuda" class="footer__link">Preguntas frecuentes</a></li>
                <li><a href="?action=ayuda" class="footer__link">Contacto</a></li>
            </ul>
        </div>

    </div>

    <div class="footer__bottom footer__container">
        <span class="footer__copyright">&copy; 2026 NOVAIRLINES. Todos los derechos reservados.</span>
    </div>
</footer>

<!-- ========================================== -->
<!-- MODAL DE INICIO DE SESIÓN -->
<!-- ========================================== -->
<div id="login-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    
    <!-- Contenedor del Modal -->
    <div class="bg-white rounded-3xl shadow-2xl w-[90%] max-w-[420px] p-8 relative transform scale-95 transition-transform duration-300" id="login-modal-content">
        
        <!-- Botón de Cerrar (X) -->
        <button onclick="cerrarLogin()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 transition-colors">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <!-- Cabecera del Modal -->
        <div class="text-center mb-6">
            <div class="flex items-center justify-center gap-2 mb-4">
                <i class="fa-solid fa-paper-plane text-[#0070F3] text-2xl rotate-[15deg]"></i>
                <span class="text-2xl font-bold text-[#0A192F] tracking-tight font-['Open_Sauce_One']">NOVA <span class="text-[#0070F3]">AI</span>RLINES</span>
            </div>
            <h2 class="text-2xl font-bold text-[#0A192F] mb-1">Bienvenido</h2>
            <p class="text-gray-500 text-sm">Inicia sesión para gestionar tus vuelos</p>
        </div>

        <!-- Formulario -->
        <form method="POST" action="index.php?action=login" class="space-y-4">
            
            <div class="text-center">
                <label class="block text-sm font-bold text-[#0A192F] mb-1">Email</label>
                <input type="email" name="email" required placeholder="tu@email.com" class="w-full text-center px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3] transition-all">
            </div>

            <div class="text-center">
                <label class="block text-sm font-bold text-[#0A192F] mb-1">Contraseña</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full text-center px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-[#0070F3] focus:ring-1 focus:ring-[#0070F3] transition-all">
            </div>

            <div class="text-center pt-2">
                <a href="#" class="text-[#0070F3] text-sm font-bold hover:underline">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="w-full bg-[#0070F3] hover:bg-[#0051CC] text-white font-bold py-3.5 rounded-xl transition-colors shadow-md mt-2">
                Iniciar sesión
            </button>
        </form>

        <!-- Separador -->
        <div class="flex items-center gap-4 my-6">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-gray-400 text-xs">o continúa con</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <!-- Botón de Google -->
        <button type="button" class="w-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-3.5 rounded-xl transition-colors flex items-center justify-center gap-3 mb-6">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            Continuar con Google
        </button>

        <!-- Registro -->
        <div class="text-center text-sm">
            <span class="text-gray-500">¿No tienes cuenta?</span> 
            <a href="?action=formulario" class="text-[#0070F3] font-bold hover:underline">Regístrate gratis</a>
        </div>

    </div>
</div>

<script>
    // Lógica para abrir y cerrar el modal con animaciones suaves
    const modal = document.getElementById('login-modal');
    const modalContent = document.getElementById('login-modal-content');

    function abrirLogin() {
        modal.classList.remove('hidden');
        // Pequeño delay para que la transición CSS funcione después de quitar 'hidden'
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function cerrarLogin() {
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        // Esperamos a que termine la animación para ocultarlo completamente
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Cerrar al hacer clic en el fondo oscuro
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            cerrarLogin();
        }
    });
</script>

</body>
</html>
