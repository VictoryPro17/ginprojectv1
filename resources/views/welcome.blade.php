{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ZiviCare – Willkommen</title>

  {{-- Vite / Tailwind --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="description" content="ZiviCare – Die einfache Plattform für Aufgaben, Bewohnerverwaltung und Dokumentation im Zivildienst.">
</head>

<body class="min-h-screen bg-gray-50 dark:bg-[#0a0a0a] text-gray-900 dark:text-gray-100 antialiased">
  <div class="flex flex-col items-center justify-center min-h-screen p-6">

    {{-- Logo --}}
    <div class="mb-6">
      <div class="w-24 h-24 rounded-2xl bg-black text-white flex items-center justify-center text-4xl shadow">
        ❤
      </div>
    </div>

    {{-- Titel --}}
    <h1 class="text-4xl sm:text-5xl font-extrabold mb-3 text-center">
      Willkommen bei <span class="text-blue-600">GIN Documentation</span>
    </h1>

    {{-- Kurzbeschreibung (1 Satz) --}}
    <p class="text-gray-600 dark:text-gray-300 text-center max-w-xl mb-6">
      Klientenverwaltung und Dokumentation, Dienstplanen
    </p>

    {{-- Mittlere Beschreibung (2–3 Zeilen) --}}
    <p class="text-gray-500 dark:text-gray-400 text-center max-w-2xl mb-6 px-4">
     <strong class="block mb-2">Beschreibung:</strong>
      GIN Documentation ist eine digitale Unterstützung für Gin Betreuer. Mit klaren Dienstplanen, Klientenprofilen und einfacher Dokumentation behältst du immer den Überblick
    </p>

    {{-- Längere Beschreibung (optional, versteckt auf kleinen Bildschirmen) --}}
    <div class="hidden sm:block max-w-3xl text-center text-sm text-gray-500 dark:text-gray-400 mb-8 px-6">
      <strong class="block mb-2">Kurz & schön:</strong> — schnell, zuverlässig und speziell für die Arbeit im Dienst entwickelt.
    </div>

    {{-- Buttons --}}
    <div class="flex gap-4">
      <a href="{{ route('login') ?? '/login' }}" class="px-6 py-3 rounded-xl bg-blue-600 text-white font-medium shadow hover:bg-blue-700 transition">
        Login
      </a>

      <a href="{{ route('register') ?? '/register' }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-800 hover:bg-gray-50 transition shadow-sm">
        Registrieren
      </a>
    </div>

    {{-- Features / kleine Liste --}}
    <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl w-full">
      <div class="p-4 bg-white dark:bg-[#161615] rounded-lg shadow-sm text-center">
        <div class="mb-2 text-2xl">📝</div>
        <div class="font-medium">Berichte</div>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Schnell und mobil erstellen</div>
      </div>

      <div class="p-4 bg-white dark:bg-[#161615] rounded-lg shadow-sm text-center">
        <div class="mb-2 text-2xl">👥</div>
        <div class="font-medium">Klienten</div>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Profile & Notizen</div>
      </div>

      <div class="p-4 bg-white dark:bg-[#161615] rounded-lg shadow-sm text-center">
        <div class="mb-2 text-2xl">✅</div>
        <div class="font-medium">Dienstplanen</div>
        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Struktur & Verlauf</div>
      </div>
    </div>

    {{-- Footer --}}
    <p class="text-xs text-gray-400 mt-10">
      © {{ date('Y') }} Gin — Erstellt von Victory 
    </p>
  </div>
</body>
</html>
