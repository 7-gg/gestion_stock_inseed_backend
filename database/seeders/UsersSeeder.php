<?php

namespace Database\Seeders;

use App\Models\User;
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
            [
                'name' => 'Monsieur TEKESSI',
                'email' => 'tekessi@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Monsieur KOKOUDA',
                'email' => 'kokouda@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'TETE kossi',
                'email' => 'tete@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Kayi DJIBOM',
                'email' => 'kayi@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Monsieur FANKEBA',
                'email' => 'fankeba@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Monsieur AMANA',
                'email' => 'amana@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Monsieur BANAPASSE',
                'email' => 'banapasse@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Madame Isabelle',
                'email' => 'isabelle@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => true, // Manager selon l'image
            ],
            [
                'name' => 'BEDEMA Prisca',
                'email' => 'prisca@inseed.com',
                'phone' => null,
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],

            [
                'name' => 'Madame ADISSA',
                'email' => 'ameke7gg@gmail.com', //'manager@adissa.com',
                'phone' => '+22890010203', // Ajusté en format local si besoin
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => true,
            ],
            [
                'name' => 'Monsieur PANACO',
                'email' => 'manager@panaco.com',
                'phone' => '+22891122334',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => true,
            ],
            [
                'name' => 'Monsieur BENESSI',
                'email' => 'user@benessi.com',
                'phone' => '+33622334455',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Monsieur LAO-TETA',
                'email' => 'user@lao-teta.com',
                'phone' => '+33633445566',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Monsieur GENTRY',
                'email' => 'user@gentry.com',
                'phone' => '+33644556677',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
            [
                'name' => 'Madame FERNANDA',
                'email' => 'user@fernanda.com',
                'phone' => '+33655667788',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'is_manager' => false,
            ],
        ];

        foreach ($users as $userData) {
            // updateOrCreate évite de créer des doublons si tu relances le seeder
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
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
