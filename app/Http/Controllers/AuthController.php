<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $roles = Role::all();
        return Inertia::render('Register', [
            'roles' => $roles
        ]);
    }

    public function showLoginForm()
    {
        return Inertia::render('Login');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'rol' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'rol' => $data['rol'],
        ]);

        $role = Role::firstOrCreate(['nombre' => $data['rol']]);
        $user->roles()->attach($role->id);

        Auth::login($user);

        return $this->redirectByRole($user);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Verificar si el usuario existe y si está activo
        $user = User::where('email', $credentials['email'])->first();

        if ($user && !$user->activo) {
            return back()->withErrors([
                'email' => 'Tu cuenta se encuentra desactivada actualmente. Por favor, contacta con soporte o con un administrador.',
            ]);
        }

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Redirigir según el rol del usuario
     */
    private function redirectByRole($user)
    {
        // Limpiamos el rol de espacios y lo pasamos a minúsculas
        $rol = strtolower(trim($user->rol ?? ''));

        // Permitimos 'administrador', 'admin', o variantes
        if ($rol === 'administrador' || $rol === 'admin') {
            return redirect('/admin/dashboard');
        }

        // Redirigir trabajadores al dashboard correspondiente
        if ($rol === 'trabajador' || $rol === 'worker' || $rol === 'contratista') {
            return redirect('/trabajador/dashboard');
        }

        return redirect('/home');
    }
}
