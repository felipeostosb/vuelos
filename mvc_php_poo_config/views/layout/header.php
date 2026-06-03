<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOVAIRLINES - Tu viaje con Inteligencia Artificial</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    
    <!-- Open Sauce Font -->
    <link href="https://fonts.cdnfonts.com/css/open-sauce-one" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <header class="site-header">
        <div class="header__container">
            
            <a href="?action=home" class="header__logo-link">
                <i class="fa-solid fa-paper-plane header__logo-icon"></i>
                <span class="header__logo-text">NOVA <span class="header__logo-highlight">AI</span>RLINES</span>
            </a>

            <nav class="header__nav">
                <a href="?action=home" class="nav__link">Inicio</a>
                <a href="?action=destinos" class="nav__link">Destinos</a>
                <a href="?action=ofertas" class="nav__link">Ofertas</a>
                <a href="?action=checkin" class="nav__link">Check-in</a>
                <a href="?action=ayuda" class="nav__link">Ayuda</a>
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
                                <a href="?action=panel" class="dropdown__item"><i class="fa-solid fa-suitcase-rolling w-5"></i> Mis Viajes</a>
                                <a href="?action=formulario" class="dropdown__item"><i class="fa-solid fa-user w-5"></i> Mi Perfil</a>
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
