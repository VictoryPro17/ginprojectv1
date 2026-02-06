<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}

        <div class="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-700 text-center">
            <p class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Test Version - For Training Purposes Only</p>
            <p class="text-[10px] text-zinc-400 mt-1">© {{ date('Y') }} Victory. All rights reserved.</p>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
