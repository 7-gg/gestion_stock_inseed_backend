<?php

/**
 * Configuration pour l'authentification en deux étapes
 * 
 * Variables d'environnement :
 * - LOGIN_CODE_EXPIRATION : Durée du code en minutes (défaut: 10)
 * - LOGIN_CODE_MAX_ATTEMPTS : Nombre maximum de tentatives (défaut: 3)
 */

return [
    /*
     * Durée du code de connexion en minutes
     */
    'login_code_expiration' => env('LOGIN_CODE_EXPIRATION', 10),

    /*
     * Nombre maximum de tentatives échouées
     */
    'login_code_max_attempts' => env('LOGIN_CODE_MAX_ATTEMPTS', 3),

    /*
     * Configuration du rate limiting (optionnel)
     * Utilisez des middleware de throttling sur les routes
     */
    'login_code_rate_limit' => env('LOGIN_CODE_RATE_LIMIT', '5,1'), // 5 requêtes par minute
];
