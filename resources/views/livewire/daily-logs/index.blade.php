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

    <div class="space-y-4">
        @foreach ($logs as $log)
            <div class="p-4 border rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm transition hover:shadow-md">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="font-bold text-lg">{{ $log->client->name }}</span>
                        <span class="text-zinc-500 text-sm ml-2">{{ $log->date->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-zinc-500">von {{ $log->user->name }}</span>
                            @if($log->mood)
                            <flux:badge>{{ $log->mood }}</flux:badge>
                        @endif
                    </div>
                </div>
                
                @if($log->activity)
                <div class="mb-2">
                    <span class="font-semibold text-sm text-zinc-600 dark:text-zinc-400">Aktivität:</span>
                    <p class="text-zinc-800 dark:text-zinc-200">{{ $log->activity }}</p>
                </div>
                @endif
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-3 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg text-sm border border-zinc-100 dark:border-zinc-800">
                    <div>
                        <span class="text-zinc-500">Trinken:</span>
                        <span class="font-medium ml-1">{{ $log->hydration_ml ? $log->hydration_ml . ' ml' : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Stuhlgang:</span>
                        <span class="font-medium ml-1">{{ $log->stool_passed ? 'Ja' : 'Nein' }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Urin/KV:</span>
                        <span class="font-medium ml-1">{{ $log->urine_desc ?: '-' }}</span>
                    </div>
                </div>

                @if($log->special_incidents)
                <div class="mt-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900/30">
                    <div class="flex items-center gap-2 mb-1">
                        <flux:icon name="exclamation-triangle" class="size-4" />
                        <span class="font-bold text-xs uppercase tracking-wide">Besonderheit</span>
                    </div>
                    <p class="text-sm">{{ $log->special_incidents }}</p>
                </div>
                @endif
            </div>
        @endforeach
        
        @if($logs->isEmpty())
            <div class="text-center py-10 text-zinc-500">Keine Einträge gefunden.</div>
        @endif
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
