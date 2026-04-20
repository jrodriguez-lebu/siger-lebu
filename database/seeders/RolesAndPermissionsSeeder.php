<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permisos ─────────────────────────────────────────────
        $permissions = [
            // Emergencias
            'emergencies.view',
            'emergencies.create',
            'emergencies.edit',
            'emergencies.delete',
            'emergencies.change_status',
            'emergencies.assign_team',
            'emergencies.assign_priority',
            'emergencies.upload_photos',
            'emergencies.view_all',       // ver todas (coordinador/admin)
            'emergencies.view_assigned',  // ver solo asignadas (lider)

            // Equipos
            'teams.view',
            'teams.create',
            'teams.edit',
            'teams.delete',
            'teams.manage_members',

            // Vehículos
            'vehicles.view',
            'vehicles.create',
            'vehicles.edit',
            'vehicles.delete',
            'vehicles.assign',

            // Equipamiento
            'equipment.view',
            'equipment.create',
            'equipment.edit',
            'equipment.delete',

            // Insumos
            'supplies.view',
            'supplies.create',
            'supplies.edit',
            'supplies.delete',

            // Reportes
            'reports.view',
            'reports.export',

            // Usuarios (solo admin)
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.assign_roles',

            // Notificaciones
            'notifications.send',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Roles y asignación de permisos ────────────────────────

        // DIGITADOR: solo puede ingresar emergencias y ver las propias
        $digitador = Role::firstOrCreate(['name' => 'digitador']);
        $digitador->syncPermissions([
            'emergencies.view',
            'emergencies.create',
            'emergencies.upload_photos',
        ]);

        // LÍDER: ve emergencias asignadas, cambia estado, sube fotos
        $lider = Role::firstOrCreate(['name' => 'lider']);
        $lider->syncPermissions([
            'emergencies.view',
            'emergencies.view_assigned',
            'emergencies.change_status',
            'emergencies.upload_photos',
            'teams.view',
            'vehicles.view',
            'equipment.view',
            'supplies.view',
        ]);

        // COORDINADOR: gestión completa excepto usuarios
        $coordinador = Role::firstOrCreate(['name' => 'coordinador']);
        $coordinador->syncPermissions([
            'emergencies.view',
            'emergencies.view_all',
            'emergencies.create',
            'emergencies.edit',
            'emergencies.change_status',
            'emergencies.assign_team',
            'emergencies.assign_priority',
            'emergencies.upload_photos',
            'teams.view',
            'teams.create',
            'teams.edit',
            'teams.manage_members',
            'vehicles.view',
            'vehicles.create',
            'vehicles.edit',
            'vehicles.assign',
            'equipment.view',
            'equipment.create',
            'equipment.edit',
            'supplies.view',
            'supplies.create',
            'supplies.edit',
            'reports.view',
            'reports.export',
            'notifications.send',
        ]);

        // ADMIN: acceso total
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $this->command->info('✅ Roles y permisos creados correctamente.');
    }
}
