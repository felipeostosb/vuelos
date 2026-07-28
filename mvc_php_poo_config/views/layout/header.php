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

    <!-- mapa de calor -->
    <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "xaoq8ifwa3");
</script>
</head>
<body class="bg-brand-blue text-white font-sans min-h-screen">

    <header class="site-header">
        <div class="header__container">
            
            <a href="?action=home" class="header__logo-link">
                <!-- <i class="fa-solid fa-paper-plane header__logo-icon"></i>
                <span class="header__logo-text">NOVA <span class="header__logo-highlight">AI</span>RLINES</span>
                <span class="header__logo-text">NOVA AIRLINES</span> -->
                <img src="assets/img/logonovairlines.png" alt="NOVAIRLINES" class="w-40 md:w-[250px] h-auto object-contain px-3 md:px-3"> 
                
            </a> 

            <nav class="header__nav">
                <a href="?action=home" class="nav__link">Inicio</a>
                <a href="?action=destinos" class="nav__link">Destinos</a>
                <a href="?action=ofertas" class="nav__link">Ofertas</a>
                <a href="?action=checkin" class="nav__link">Check-in</a>
                <a href="?action=ayuda" class="nav__link">Ayuda</a>
                <?php if (($_SESSION['usuario']['rol'] ?? '') === 'admin'): ?>
                    <a href="?action=admin" class="nav__link text-amber-400 font-bold hover:text-amber-300">
                        <i class="fa-solid fa-shield-halved text-amber-400 mr-1"></i> Admin
                    </a>
                <?php endif; ?>
            </nav>

            <div class="header__actions">
                <a href="tel:+5112345678" class="header__phone">
                    <i class="fa-solid fa-phone text-[#0070F3]"></i> +51 1 234 5678
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <div class="user-menu__trigger">
                            <div class="user-menu__avatar">
                                <?php echo substr($_SESSION['user_name'], 0, 1); ?>
                            </div>
                            <span class="user-menu__name">Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                        
                        <!-- Menú Desplegable Oculto -->
                        <div class="user-menu__dropdown">
                            <div class="dropdown__content">
                                <?php if (($_SESSION['usuario']['rol'] ?? '') === 'admin'): ?>
                                    <a href="?action=admin" class="dropdown__item text-amber-400 font-bold"><i class="fa-solid fa-shield-halved w-5"></i> Panel Administrador</a>
                                <?php endif; ?>
                                <a href="?action=panel" class="dropdown__item"><i class="fa-solid fa-suitcase-rolling w-5"></i> Mis Viajes</a>
                                <a href="#" class="dropdown__item"><i class="fa-solid fa-user w-5"></i> Mi Perfil</a>
                                <div class="dropdown__divider"></div>
                                <a href="?action=logout" class="dropdown__item dropdown__item--danger"><i class="fa-solid fa-sign-out-alt w-5"></i> Cerrar sesión</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <button onclick="abrirLogin()" class="btn btn--primary">
                        Iniciar sesión
                    </button>
                <?php endif; ?>
            </div>

        </div>
    </header>

    <!-- Notificaciones / Alertas Globales -->
    <div id="toast-container" class="fixed top-24 right-5 z-[110] flex flex-col gap-2">
        <?php if(isset($_GET['login']) && $_GET['login'] == 'error'): ?>
            <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg font-bold flex items-center gap-3 animate-[slideInRight_0.3s_ease-out]">
                <i class="fa-solid fa-circle-exclamation text-xl"></i>
                Credenciales incorrectas
            </div>
            <script>setTimeout(() => abrirLogin(), 500);</script>
        <?php endif; ?>
        
        <?php if(isset($_GET['registro']) && $_GET['registro'] == 'success'): ?>
            <div class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg font-bold flex items-center gap-3 animate-[slideInRight_0.3s_ease-out]">
                <i class="fa-solid fa-check-circle text-xl"></i>
                ¡Cuenta creada y sesión iniciada!
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['login']) && $_GET['login'] == 'success'): ?>
            <div class="bg-blue-500 text-white px-6 py-4 rounded-xl shadow-lg font-bold flex items-center gap-3 animate-[slideInRight_0.3s_ease-out]">
                <i class="fa-solid fa-check-circle text-xl"></i>
                ¡Bienvenido de nuevo!
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['login']) && $_GET['login'] == 'required'): ?>
            <div class="bg-yellow-500 text-white px-6 py-4 rounded-xl shadow-lg font-bold flex items-center gap-3 animate-[slideInRight_0.3s_ease-out]">
                <i class="fa-solid fa-lock text-xl"></i>
                Debes iniciar sesión para continuar
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
            // 1. Obtenemos la acción de la URL (ej: ?action=destinos)
            const urlParams = new URLSearchParams(window.location.search);
            // Si no hay acción en la URL, asumimos que estamos en 'home'
            const currentAction = urlParams.get('action') || 'home';
            
            // 2. Buscamos el enlace que coincida con esa acción
            const activeLink = document.querySelector(`.nav-link[href="?action=${currentAction}"]`);
            
            // 3. Si encontramos el enlace, le aplicamos los estilos de "pestaña activa"
            if (activeLink) {
                activeLink.classList.remove('text-gray-300', 'border-transparent');
                activeLink.classList.add('text-white', 'border-white', 'scale-110', 'font-bold');
            }
        });
    </script>
