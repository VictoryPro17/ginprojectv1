{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>GIN Documentation – Willkommen</title>

  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

  {{-- Vite / Tailwind --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="description" content="GIN Documentation – Die Plattform für Klientenverwaltung, Dienstpläne und Dokumentation in der Betreuung.">
</head>

<body class="min-h-screen antialiased bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
  <div class="relative flex min-h-screen flex-col">

    {{-- Top bar --}}
    <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
      <div class="flex items-center gap-3">
        <div class="flex size-10 items-center justify-center rounded-xl bg-accent text-white shadow-sm">
          <x-app-logo-icon class="size-6 fill-current text-white" />
        </div>
        <div class="leading-tight">
          <div class="font-semibold">GIN Documentation</div>
          <div class="text-xs text-zinc-500 dark:text-zinc-400">Betreuung &amp; Doku</div>
        </div>
      </div>

      <nav class="flex items-center gap-2">
        <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 transition hover:bg-zinc-200/60 dark:text-zinc-300 dark:hover:bg-zinc-800">
          Login
        </a>
        <a href="{{ route('register') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:opacity-90">
          Registrieren
        </a>
      </nav>
    </header>

    {{-- Hero --}}
    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col items-center px-6 py-16 text-center">
      <span class="mb-6 inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-4 py-1.5 text-xs font-medium text-zinc-600 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
        <span class="size-1.5 rounded-full bg-green-500"></span>
        Speziell für die Arbeit im Dienst entwickelt
      </span>

      <h1 class="max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
        Dokumentation, die den Überblick behält
      </h1>

      <p class="mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
        GIN Documentation vereint Klientenprofile, Dienstpläne und die tägliche Dokumentation
        in einer klaren, zuverlässigen Plattform – schnell erfasst, immer griffbereit.
      </p>

      <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
        <a href="{{ route('login') }}" class="rounded-xl bg-accent px-6 py-3 font-medium text-white shadow-sm transition hover:opacity-90">
          Anmelden
        </a>
        <a href="{{ route('register') }}" class="rounded-xl border border-zinc-200 bg-white px-6 py-3 font-medium text-zinc-800 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800">
          Konto erstellen
        </a>
      </div>

      {{-- Features --}}
      <div class="mt-20 grid w-full grid-cols-1 gap-6 text-left sm:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
          <div class="mb-4 flex size-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
          </div>
          <h3 class="font-semibold">Tagesdokumentation</h3>
          <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Berichte schnell und mobil erfassen.</p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
          <div class="mb-4 flex size-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
          </div>
          <h3 class="font-semibold">Klientenverwaltung</h3>
          <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Profile, Notizen und Stammdaten an einem Ort.</p>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
          <div class="mb-4 flex size-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
          </div>
          <h3 class="font-semibold">Dienstpläne</h3>
          <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Struktur und Verlauf immer im Blick.</p>
        </div>
      </div>
    </main>

    {{-- Footer --}}
    <footer class="mx-auto w-full max-w-6xl px-6 py-8 text-center text-xs text-zinc-400">
      © {{ date('Y') }} GIN Documentation — Erstellt von Victory
    </footer>
  </div>
</body>
</html>
