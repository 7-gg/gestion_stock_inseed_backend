1- Modifier l'utilisateur

Le changement d’administrateur est volontairement exclu de l’interface utilisateur et traité via un seeder contrôlé, garantissant sécurité, traçabilité et conformité institutionnelle.

# Variables d’environnement – Administrateur - fichier .env

    ADMIN_EMAIL=admin@gmail.com
    ADMIN_PHONE=+228XXXXXXXX

# lancer le seeder

    php artisan db:seed --class=AdminSeeder
