<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VuelaIA - Tu viaje con Inteligencia Artificial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link class="refe" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <header class="bg-[#0A192F] text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-[1560px] mx-auto px-12 h-20 flex items-center justify-between">
            
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-[#0070F3] text-2xl rotate-[15deg]"></i>
                <span class="text-2xl font-bold tracking-tight">Vuela<span class="text-[#0070F3]">IA</span></span>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-300">
                <a href="?action=home" class="nav-link hover:text-white transition-all duration-300 pb-1 border-b-2 border-transparent hover:scale-105">Inicio</a>
                <a href="?action=destinos" class="nav-link hover:text-white transition-all duration-300 pb-1 border-b-2 border-transparent hover:scale-105">Destinos</a>
                <a href="?action=ofertas" class="nav-link hover:text-white transition-all duration-300 pb-1 border-b-2 border-transparent hover:scale-105">Ofertas</a>
                <a href="?action=checkin" class="nav-link hover:text-white transition-all duration-300 pb-1 border-b-2 border-transparent hover:scale-105">Check-in</a>
                <a href="?action=ayuda" class="nav-link hover:text-white transition-all duration-300 pb-1 border-b-2 border-transparent hover:scale-105">Ayuda</a>
            </nav>

            <div class="flex items-center gap-6">
                <a href="tel:+5112345678" class="hidden lg:flex items-center gap-2 text-sm text-gray-300 hover:text-white transition-colors">
                    <i class="fa-solid fa-phone text-[#0070F3]"></i> +51 1 234 5678
                </a>
                <button onclick="abrirLogin()" class="bg-[#0070F3] hover:bg-[#0051CC] text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-md">
                    Iniciar sesión
                </button>
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
