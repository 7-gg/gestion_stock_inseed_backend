<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Administrateur
            [
                'name' => 'Addissa Dapaong',
                'email' => 'admin@stockapp.com',
                'phone' => '+33601020304',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Gestionnaire 1
            [
                'name' => 'Marie Dupont',
                'email' => 'marie.dupont@example.com',
                'phone' => '+33611223344',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Gestionnaire 2
            [
                'name' => 'Jean Martin',
                'email' => 'jean.martin@example.com',
                'phone' => '+33622334455',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Gestionnaire 3
            [
                'name' => 'Sophie Leroy',
                'email' => 'sophie.leroy@example.com',
                'phone' => '+33633445566',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Gestionnaire 4
            [
                'name' => 'Thomas Bernard',
                'email' => 'thomas.bernard@example.com',
                'phone' => '+33644556677',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Utilisateur standard 1 (non gestionnaire)
            [
                'name' => 'Alice Dubois',
                'email' => 'alice.dubois@example.com',
                'phone' => '+33655667788',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Utilisateur standard 2 (non gestionnaire)
            [
                'name' => 'Pierre Moreau',
                'email' => 'pierre.moreau@example.com',
                'phone' => '+33666778899',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insérer tous les utilisateurs
        foreach ($users as $userData) {
            User::create($userData);
        }

        // Message de confirmation
        // $this->command->info('7 utilisateurs créés avec succès :');
        // $this->command->info('- 1 administrateur (admin@stockapp.com)');
        // $this->command->info('- 4 gestionnaires (marie.dupont@example.com, etc.)');
        // $this->command->info('- 2 utilisateurs standards');
        // $this->command->info('');
        // $this->command->info('Tous les utilisateurs ont le mot de passe : password123');
    }
}
