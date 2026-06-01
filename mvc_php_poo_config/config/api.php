<?php
/**
 * =========================================================================
 * ARCHIVO DE CONFIGURACIÓN: api.php (El "Llavero")
 * =========================================================================
 * Este archivo es como un llavero maestro de alta seguridad. 
 * Guarda las "contraseñas" o "API Keys" necesarias para comunicarnos 
 * con otros edificios (servicios externos) como Google o Duffel.
 * 
 * Es importante NUNCA mostrar este archivo al público en internet.
 */

return [
    // La llave para hablar con DUFFEL (El proveedor mundial de vuelos)
    'duffel' => [
        'api_key' => 'TU_DUFFEL_API_KEY_AQUI',
        // 'base_url' => 'https://api.duffel.com/',
    ],
    
    // La llave para hablar con GEMINI (El cerebro de Inteligencia Artificial)
    'gemini' => [
        'api_key' => 'TU_GEMINI_API_KEY_AQUI',
        'model' => 'gemini-1.5-flash', // El modelo inteligente específico que usaremos
    ],
    
    // Las llaves para permitir que el usuario inicie sesión con su cuenta de Google
    'oauth' => [
        'google_client_id' => 'TU_CLIENT_ID_AQUI',
        'google_client_secret' => 'TU_CLIENT_SECRET_AQUI',
        'redirect_uri' => 'https://novairlines.appp/callback', // A dónde lo enviamos después de loguearse
    ]
];
