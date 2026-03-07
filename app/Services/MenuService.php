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
                    'label' => 'PRINCIPAL',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'pi pi-home', 'url' => '/admin/dashboard'],
                        ['label' => 'Gestión de Usuarios', 'icon' => 'pi pi-users', 'url' => '/admin/usuariosg'],
                    ],
                ],
                [
                    'label' => 'REPORTES',
                    'items' => [
                        ['label' => 'Gestión de Reportes', 'icon' => 'pi pi-clipboard', 'url' => '/admin/gestion-incidencias'],
                        ['label' => 'Reporte Ciudadanos', 'icon' => 'pi pi-file-plus', 'url' => '/reportes/gestion-reportes'],
                        ['label' => 'Monitoreo', 'icon' => 'pi pi-eye', 'url' => '/admin/monitoreo'],
                        ['label' => 'Mapa de Calor', 'icon' => 'pi pi-map', 'url' => '/admin/mapa'],
                    ],
                ],
            ],

            'trabajador', 'contratista' => [
                [
                    'label' => 'PRINCIPAL',
                    'items' => [
                        ['label' => 'Dashboard',    'icon' => 'pi pi-home',      'url' => '/trabajador/dashboard'],
                        ['label' => 'Mis Órdenes',  'icon' => 'pi pi-briefcase', 'url' => '/trabajador/tareas'],
                        ['label' => 'Historial',    'icon' => 'pi pi-history',   'url' => '/trabajador/reportes'],
                    ],
                ],
            ],

            default => [],
        };
    }
}
