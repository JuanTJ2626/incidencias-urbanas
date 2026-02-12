<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share authenticated user and menu per role via Inertia
        Inertia::share([
            'auth.user' => function () {
                $user = Auth::user();
                return $user ? $user->only(['id','name','rol']) : null;
            },
            'menu' => function () {
                $user = Auth::user();
                if (! $user) {
                    return [];
                }

                // Basic menu builder by role (extend as needed)
                switch ($user->rol) {
                    case 'admin':
                        return [
                            [
                                'label' => 'GENERAL',
                                'items' => [
                                    ['label' => 'Dashboard','icon'=>'pi pi-home','url'=>'/admin/dashboard'],
                                    ['label' => 'Usuarios','icon'=>'pi pi-users','url'=>'/admin/users'],
                                    ['label' => 'Reportes','icon'=>'pi pi-file','url'=>'/admin/reportes'],
                                    ['label' => 'PRUEBA wvcevrever','icon'=>'pi pi-exclamation-triangle','url'=>'/admin/incidencias'],
                                ],
                            ],
                            [
                                'label' => 'GESTIÓN',
                                'items' => [
                                    ['label' => 'Configuración','icon'=>'pi pi-cog','url'=>'/admin/config'],
                                ],
                            ],
                        ];
                    case 'trabajador':
                        return [
                            [
                                'label' => 'PRINCIPAL',
                                'items' => [
                                    ['label' => 'Tareas','icon'=>'pi pi-briefcase','url'=>'/trabajador/tareas'],
                                    ['label' => 'Reportes','icon'=>'pi pi-file','url'=>'/trabajador/reportes'],
                                ],
                            ],
                        ];
                    default:
                        return [];
                }
            }
        ]);
    }
}
