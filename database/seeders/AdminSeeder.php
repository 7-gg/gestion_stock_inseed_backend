<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPhone = env('ADMIN_PHONE');
        $admin_name = env('ADMIN_NAME', 'Administrateur');

        if (!$adminEmail) {
            throw new \Exception('ADMIN_EMAIL is not defined in .env');
        }

        DB::transaction(function () use ($adminEmail, $adminPhone, $admin_name) {

            $currentAdmin = DB::table('admin_histories')
                ->whereNull('ended_at')
                ->first();

            if ($currentAdmin) {
                $currentAdminUser = User::find($currentAdmin->user_id);

                if ($currentAdminUser && $currentAdminUser->email === $adminEmail) {
                    // Même admin → mise à jour si nécessaire
                    $updates = [];
                    if ($currentAdminUser->name !== $admin_name) {
                        $updates['name'] = $admin_name;
                    }
                    if ($currentAdminUser->phone !== $adminPhone) {
                        $updates['phone'] = $adminPhone;
                    }
                    if (!empty($updates)) {
                        $currentAdminUser->update($updates);
                    }
                } elseif ($currentAdminUser) {
                    // Ancien admin → retirer droits
                    $currentAdminUser->update(['is_admin' => false]);
                }
            }

            /** 1️⃣ Clôturer l’ancien mandat admin (s’il existe) */
            DB::table('admin_histories')
                ->whereNull('ended_at')
                ->update([
                    'ended_at' => Carbon::now(),
                ]);

            /** 2️⃣ Récupérer ou créer le nouvel admin */
            $admin = User::withTrashed()
                ->where('email', $adminEmail)
                ->first();

            if (!$admin) {
                $admin = User::create([
                    'name'       => 'Administrateur',
                    'email'      => $adminEmail,
                    'phone'      => $adminPhone,
                    'is_admin'   => true,
                    'is_manager' => false,
                    'password'   => Hash::make(str()->random(16)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // réactiver si supprimé logiquement
                if ($admin->deleted_at) {
                    $admin->restore();
                }

                $admin->update([
                    'is_admin' => true,
                ]);
            }

            /** 3️⃣ Enregistrer le nouveau mandat admin */
            DB::table('admin_histories')->insert([
                'user_id'    => $admin->id_user ?? $admin->id,
                'started_at' => Carbon::now(),
                'ended_at'   => null,
            ]);
        });
    }
}
