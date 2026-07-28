<main class="min-h-[80vh] bg-[#0A1628] py-16 px-4 flex items-center justify-center font-sans text-white">
    <div class="w-full max-w-md bg-[#132238]/90 border border-[#C5A880]/30 rounded-3xl p-8 shadow-2xl backdrop-blur-md">
        <div class="text-center mb-8 space-y-2">
            <div class="w-14 h-14 bg-[#C5A880]/15 border border-[#C5A880]/30 rounded-full flex items-center justify-center mx-auto text-[#C5A880] text-2xl">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-light tracking-wide text-white">Crear Cuenta Boutique</h2>
            <p class="text-xs text-[#C5A880]/80 font-light tracking-wide">Únase a NovAirlines y disfrute de una experiencia aeronáutica sin igual</p>
        </div>

        <?php if (isset($_GET['registro']) && $_GET['registro'] == 'error'): ?>
            <div class="bg-red-500/15 border border-red-500/30 text-red-400 text-xs font-light p-3 rounded-xl mb-6 text-center">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i> El correo ya se encuentra registrado o hubo un error al crear la cuenta.
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST" class="space-y-5">
            <input type="hidden" name="action" value="procesarRegistro">
            
            <div>
                <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-1.5">Nombre Completo</label>
                <input type="text" name="nombre" class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 focus:border-[#C5A880] rounded-xl px-4 py-3 text-xs font-light text-white placeholder-slate-500 focus:outline-none transition-all" placeholder="Ej. Carlos Martínez" required>
            </div>

            <div>
                <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-1.5">Correo Electrónico</label>
                <input type="email" name="email" class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 focus:border-[#C5A880] rounded-xl px-4 py-3 text-xs font-light text-white placeholder-slate-500 focus:outline-none transition-all" placeholder="tucorreo@ejemplo.com" required>
            </div>

            <div>
                <label class="block text-xs font-light text-[#C5A880] uppercase tracking-widest mb-1.5">Contraseña</label>
                <input type="password" name="password" class="w-full bg-[#0A1628]/90 border border-[#C5A880]/30 focus:border-[#C5A880] rounded-xl px-4 py-3 text-xs font-light text-white placeholder-slate-500 focus:outline-none transition-all" placeholder="Cree una contraseña segura" required minlength="6">
            </div>

            <button type="submit" class="w-full bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] font-light text-xs uppercase tracking-widest py-3.5 rounded-xl transition-all duration-300 shadow-md">
                Crear Mi Cuenta
            </button>

            <div class="text-center text-xs font-light pt-2">
                <span class="text-slate-400">¿Ya posee una cuenta?</span> 
                <button type="button" onclick="abrirLogin()" class="text-[#C5A880] hover:underline font-light tracking-wide ml-1">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</main>
