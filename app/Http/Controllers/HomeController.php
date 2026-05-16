<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Society registration landing page.
     */
    public function register(): View
    {
        return view('register');
    }

    /**
     * Handle society registration form submission.
     */
    public function registerSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'mobile' => ['nullable', 'string', 'max:30', 'regex:/^[\d\s+().-]*$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'role' => ['required', 'string', Rule::in(['admin', 'member'])],
            'society_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:5000'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ], [
            'name.regex' => 'The full name may only contain letters, spaces, and basic punctuation.',
            'mobile.regex' => 'The mobile number format is invalid.',
        ]);

        $trimmedName = preg_replace('/\s+/u', ' ', trim($validated['name']));
        $nameParts = preg_split('/\s+/u', $trimmedName, 2);

        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? null;

        $role = strtoupper($validated['role']) === 'MEMBER' ? 'MEMBER' : 'ADMIN';

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'mobile' => $validated['mobile'] !== null && $validated['mobile'] !== ''
                ? trim($validated['mobile'])
                : null,
            'email' => $validated['email'],
            'society_name' => $validated['society_name'],
            'address' => $validated['address'],
            'role' => $role,
            'password' => Hash::make($validated['password']),
            'status' => 'ACTIVE',
        ]);

        $guard = config('auth.defaults.guard');
        $spatieRoleName = $role === 'MEMBER' ? 'MEMBER' : 'ADMIN';
        if (! Role::where('name', $spatieRoleName)->where('guard_name', $guard)->exists()) {
            $spatieRoleName = 'MEMBER';
        }
        $user->assignRole($spatieRoleName);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
