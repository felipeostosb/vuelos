<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOVAIRLINES - Tu viaje con Inteligencia Artificial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            gold: '#C5A880',
                            purple: '#48324F',
                            rose: '#9C694C',
                            blue: '#0A1628',
                        }
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link class="refe" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Core & Components CSS -->
    <link rel="stylesheet" href="assets/css/core/variables.css">
    <link rel="stylesheet" href="assets/css/core/base.css">
    <link rel="stylesheet" href="assets/css/components/buttons.css">
    <link rel="stylesheet" href="assets/css/components/forms.css">
    <link rel="stylesheet" href="assets/css/components/cards.css">
    
    <!-- Layout CSS -->
    <link rel="stylesheet" href="assets/css/layout/header.css">
    <link rel="stylesheet" href="assets/css/layout/footer.css">
    
    <!-- Views CSS -->
    <link rel="stylesheet" href="assets/css/views/home.css">
    <link rel="stylesheet" href="assets/css/views/reserva.css">
    <link rel="stylesheet" href="assets/css/views/checkout.css">
    <link rel="stylesheet" href="assets/css/views/panel.css">
    <link rel="stylesheet" href="assets/css/views/checkin.css">
    
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    
    <!-- Google Fonts Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Mapa de Calor -->
    <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "xaoq8ifwa3");
</script>
</head>
<body class="bg-[#0A1628] text-white font-sans font-light min-h-screen">

    <header class="site-header sticky top-0 z-50 bg-[#0A1628]/90 backdrop-blur-md border-b border-[#C5A880]/20 shadow-xl transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- LOGO BOUTIQUE DE NOVAIRLINES -->
            <a href="?action=home" class="flex items-center gap-3 group">
                <img src="assets/img/logonovairlines.png" alt="NOVAIRLINES" class="w-44 md:w-[220px] h-auto object-contain transition-transform duration-300 group-hover:scale-105"> 
            </a> 

            <!-- NAVEGACIÓN PRINCIPAL MONTSERRAT 300 -->
            <nav class="hidden md:flex items-center space-x-8 font-light text-xs uppercase tracking-[0.2em] text-slate-300">
                <a href="?action=home" class="nav-link hover:text-[#C5A880] transition-colors py-1 border-b-2 border-transparent">Inicio</a>
                <a href="?action=destinos" class="nav-link hover:text-[#C5A880] transition-colors py-1 border-b-2 border-transparent">Destinos</a>
                <a href="?action=ofertas" class="nav-link hover:text-[#C5A880] transition-colors py-1 border-b-2 border-transparent">Ofertas</a>
                <a href="?action=checkin" class="nav-link hover:text-[#C5A880] transition-colors py-1 border-b-2 border-transparent">Check-in</a>
                <a href="?action=ayuda" class="nav-link hover:text-[#C5A880] transition-colors py-1 border-b-2 border-transparent">Ayuda</a>
                
                <?php if (($_SESSION['usuario']['rol'] ?? '') === 'admin'): ?>
                    <a href="?action=admin" class="nav-link text-[#C5A880] hover:text-amber-300 transition-colors py-1 border-b-2 border-transparent flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-[#C5A880]"></i> Admin
                    </a>
                <?php endif; ?>
            </nav>

            <!-- ACCIONES / BOTÓN LOGIN / USUARIO -->
            <div class="flex items-center space-x-5">
                <a href="tel:+5112345678" class="hidden lg:flex items-center gap-2 text-xs font-light text-slate-400 hover:text-[#C5A880] transition-colors tracking-wider">
                    <i class="fa-solid fa-phone text-[#C5A880]"></i>
                    <span>+51 1 700-NOVA</span>
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="relative user-menu group">
                        <div class="flex items-center gap-2.5 bg-[#132238] border border-[#C5A880]/30 hover:border-[#C5A880] px-4 py-2 rounded-xl cursor-pointer transition-all duration-300">
                            <div class="w-7 h-7 rounded-full bg-[#C5A880]/20 border border-[#C5A880]/40 text-[#C5A880] flex items-center justify-center text-xs font-light">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </div>
                            <span class="text-xs font-light text-white tracking-wide">Hola, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-[#C5A880]"></i>
                        </div>
                        
                        <!-- MENÚ DESPLEGABLE BOUTIQUE -->
                        <div class="absolute right-0 top-full pt-2 w-52 hidden group-hover:block z-50">
                            <div class="bg-[#132238] border border-[#C5A880]/30 rounded-2xl shadow-2xl p-2 space-y-1 backdrop-blur-md text-xs font-light text-slate-300">
                                <?php if (($_SESSION['usuario']['rol'] ?? '') === 'admin'): ?>
                                    <a href="?action=admin" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[#C5A880] hover:bg-[#C5A880]/15 transition">
                                        <i class="fa-solid fa-shield-halved text-[#C5A880]"></i> Panel Admin
                                    </a>
                                <?php endif; ?>
                                <a href="?action=panel" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-white/5 hover:text-[#C5A880] transition">
                                    <i class="fa-solid fa-suitcase-rolling text-[#C5A880]"></i> Mis Viajes
                                </a>
                                <a href="?action=panel" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-white/5 hover:text-[#C5A880] transition">
                                    <i class="fa-solid fa-user text-[#C5A880]"></i> Mi Perfil
                                </a>
                                <div class="border-t border-[#C5A880]/15 my-1"></div>
                                <a href="?action=logout" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-400 hover:bg-rose-500/10 transition">
                                    <i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <button onclick="abrirLogin()" 
                            class="px-5 py-2.5 bg-transparent border border-[#C5A880]/40 hover:bg-[#C5A880] text-[#C5A880] hover:text-[#0A1628] font-light text-xs uppercase tracking-widest rounded-xl transition duration-300 shadow-md">
                        Iniciar sesión
                    </button>
                <?php endif; ?>
            </div>

        </div>
    </header>

    <!-- NOTIFICACIONES / ALERTAS GLOBALES -->
    <div id="toast-container" class="fixed top-24 right-5 z-[110] flex flex-col gap-2 font-light text-xs">
        <?php if(isset($_GET['login']) && $_GET['login'] == 'error'): ?>
            <div class="bg-rose-500/90 backdrop-blur text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-rose-400/30 animate-[slideInRight_0.3s_ease-out]">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span>Credenciales incorrectas. Intenta de nuevo.</span>
            </div>
            <script>setTimeout(() => abrirLogin(), 500);</script>
        <?php endif; ?>
        
        <?php if(isset($_GET['registro']) && $_GET['registro'] == 'success'): ?>
            <div class="bg-emerald-500/90 backdrop-blur text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-emerald-400/30 animate-[slideInRight_0.3s_ease-out]">
                <i class="fa-solid fa-check-circle text-base"></i>
                <span>¡Cuenta creada con éxito! Sesión iniciada.</span>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['login']) && $_GET['login'] == 'success'): ?>
            <div class="bg-[#132238] backdrop-blur text-[#C5A880] border border-[#C5A880]/40 px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 animate-[slideInRight_0.3s_ease-out]">
                <i class="fa-solid fa-check-circle text-base text-[#C5A880]"></i>
                <span>¡Bienvenido de nuevo a NovAirlines!</span>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['login']) && $_GET['login'] == 'required'): ?>
            <div class="bg-amber-500/90 backdrop-blur text-white px-5 py-3.5 rounded-2xl shadow-xl flex items-center gap-3 border border-amber-400/30 animate-[slideInRight_0.3s_ease-out]">
                <i class="fa-solid fa-lock text-base"></i>
                <span>Inicia sesión para poder completar tu compra</span>
            </div>
            <script>setTimeout(() => abrirLogin(), 800);</script>
        <?php endif; ?>
    </div>

    <style>
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const urlParams = new URLSearchParams(window.location.search);
            const currentAction = urlParams.get('action') || 'home';
            
            const links = document.querySelectorAll('.nav-link');
            links.forEach(link => {
                if (link.getAttribute('href') === `?action=${currentAction}`) {
                    link.classList.remove('text-slate-300', 'border-transparent');
                    link.classList.add('text-[#C5A880]', 'border-[#C5A880]');
                }
            });
        });
    </script>
