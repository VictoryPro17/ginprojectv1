<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Volt::route('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // ZiviCare Routes
    Volt::route('clients', 'clients.index')->name('clients.index');
    Volt::route('clients/create', 'clients.create')->name('clients.create');
    Volt::route('clients/{client}/edit', 'clients.edit')->name('clients.edit');

    Volt::route('staff', 'staff.index')->name('staff.index');
    Volt::route('staff/{user}/edit', 'staff.edit')->name('staff.edit');

    Volt::route('groups', 'groups.index')->name('groups.index');
    Volt::route('daily-logs', 'daily-logs.index')->name('daily-logs.index');
    Volt::route('daily-logs/create', 'daily-logs.create')->name('daily-logs.create');
    
    Volt::route('shifts', 'shifts.index')->name('shifts.index');
    Volt::route('shifts/create', 'shifts.create')->name('shifts.create');
    
    Volt::route('workshops', 'workshops.index')->name('workshops.index');
    Volt::route('workshops/create', 'workshops.create')->name('workshops.create');
});
