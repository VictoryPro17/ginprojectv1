<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\DailyLog;
use App\Models\Client;
use Livewire\WithPagination;

new 
#[Layout('components.layouts.app', ['title' => 'Tagesdokumentation'])]
class extends Component {
    use WithPagination;

    public $client_id = '';
    public $date = '';

    public function with()
    {
        return [
            'logs' => DailyLog::query()
                ->when($this->client_id, fn($q) => $q->where('client_id', $this->client_id))
                ->when($this->date, fn($q) => $q->whereDate('date', $this->date))
                ->with(['client', 'user'])
                ->latest('date')
                ->paginate(10),
            'clients' => Client::all(),
        ];
    }
}; ?>

<div class="flex flex-col gap-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Tagesdokumentation</flux:heading>
            <flux:subheading>Verlauf und Berichte</flux:subheading>
        </div>
        <flux:button :href="route('daily-logs.create')" icon="plus" variant="primary">Neuer Eintrag</flux:button>
    </div>

    <div class="flex flex-col sm:flex-row gap-4">
            <flux:select wire:model.live="client_id" placeholder="Klient filtern" class="sm:max-w-xs">
            <flux:select.option value="">Alle Klienten</flux:select.option>
            @foreach ($clients as $client)
                <flux:select.option value="{{ $client->id }}">{{ $client->name }}</flux:select.option>
            @endforeach
        </flux:select>
        
        <flux:input wire:model.live="date" type="date" class="sm:max-w-xs" />
    </div>

    <div class="space-y-1">
        @php $lastDate = null; @endphp
        @forelse ($logs as $log)
            @php $d = $log->date->copy()->startOfDay(); @endphp
            @if ($lastDate === null || ! $d->equalTo($lastDate))
                @php
                    $lastDate = $d;
                    $dateLabel = $d->isToday() ? 'Heute' : ($d->isYesterday() ? 'Gestern' : $d->format('d.m.Y'));
                @endphp
                <div class="flex items-center gap-3 pt-6 pb-2 first:pt-0">
                    <flux:heading size="sm" class="whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $dateLabel }}</flux:heading>
                    <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-800"></div>
                </div>
            @endif

            <div class="relative ml-1 border-l-2 border-zinc-100 dark:border-zinc-800 pl-6 pb-4">
                <span class="absolute -left-[7px] top-4 size-3 rounded-full ring-4 ring-white dark:ring-zinc-800 {{ $log->special_incidents ? 'bg-red-500' : 'bg-blue-500' }}"></span>

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="flex size-9 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-sm font-semibold text-zinc-600 dark:text-zinc-300">
                                {{ strtoupper(mb_substr($log->client->name, 0, 1)) }}
                            </div>
                            <div class="leading-tight">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $log->client->name }}</div>
                                <div class="text-xs text-zinc-500">von {{ $log->user->name }}</div>
                            </div>
                        </div>
                        @if ($log->mood)
                            <flux:badge size="sm" color="zinc">{{ $log->mood }}</flux:badge>
                        @endif
                    </div>

                    @if ($log->activity)
                        <div class="mt-3">
                            <span class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Aktivität</span>
                            <p class="text-sm text-zinc-700 dark:text-zinc-200">{{ $log->activity }}</p>
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/60 px-2.5 py-1 text-xs text-zinc-600 dark:text-zinc-300">
                            <span class="text-zinc-400">Trinken:</span>
                            <span class="font-medium">{{ $log->hydration_ml ? $log->hydration_ml . ' ml' : '–' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/60 px-2.5 py-1 text-xs text-zinc-600 dark:text-zinc-300">
                            <span class="text-zinc-400">Stuhlgang:</span>
                            <span class="font-medium">{{ $log->stool_passed ? 'Ja' : 'Nein' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/60 px-2.5 py-1 text-xs text-zinc-600 dark:text-zinc-300">
                            <span class="text-zinc-400">Urin/KV:</span>
                            <span class="font-medium">{{ $log->urine_desc ?: '–' }}</span>
                        </span>
                    </div>

                    @if ($log->special_incidents)
                        <div class="mt-3 flex gap-2 rounded-lg border border-red-100 dark:border-red-900/30 bg-red-50 dark:bg-red-900/20 p-3 text-red-700 dark:text-red-400">
                            <flux:icon name="exclamation-triangle" class="mt-0.5 size-4 shrink-0" />
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wide">Besonderheit</div>
                                <p class="text-sm">{{ $log->special_incidents }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-zinc-500">Keine Einträge gefunden.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
