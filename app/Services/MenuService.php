<?php

namespace App\Services;

class MenuService
{
    public function getMenu($user)
    {
        if (! $user) {
            return [];
        }

        return match ($user->rol) {
            'admin' => [
                [
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'pi pi-home', 'url' => '/admin/dashboard'],
                        ['label' => 'Gestion de Usuarios', 'icon' => 'pi pi-users', 'url' => '/admin/usuariosg'],
                        ['label' => 'Reportes', 'icon' => 'pi pi-file', 'url' => '/admin/reportes'],
                        ['label' => 'PRUEBA', 'icon' => 'pi pi-exclamation-triangle', 'url' => '/admin/prueba'],
                        ['label' => 'Reportes - Ciudadanos', 'icon' => 'pi pi-file-plus', 'url' => '/reportes/gestion-reportes'],
                    ],
                ],
              
            ],

            'trabajador' => [
                [
                    'label' => 'PRINCIPAL',
                    'items' => [
                        ['label' => 'Tareas', 'icon' => 'pi pi-briefcase', 'url' => '/trabajador/tareas'],
                        ['label' => 'Reportes', 'icon' => 'pi pi-file', 'url' => '/trabajador/reportes'],
                    ],
                ],
            ],

            default => [],
        };
    }
}
