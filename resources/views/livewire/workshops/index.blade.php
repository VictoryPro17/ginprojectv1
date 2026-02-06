<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Workshop;

new 
#[Layout('components.layouts.app', ['title' => 'Workshops & Seminare'])]
class extends Component {
    public function with()
    {
        return [
            'workshops' => Workshop::query()
                ->where('date', '>=', now())
                ->orderBy('date')
                ->with('participants') // Check if user is participant
                ->get(),
        ];
    }

    public function toggleRegistration(Workshop $workshop)
    {
        $user = auth()->user();
        if ($workshop->participants->contains($user)) {
            $workshop->participants()->detach($user);
        } else {
            $workshop->participants()->attach($user, ['status' => 'registered']);
        }
    }
    
    public function delete(Workshop $workshop)
    {
        // Add authorization check
        $workshop->delete();
    }
}; ?>

<div class="flex flex-col gap-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Workshops & Seminare</flux:heading>
            <flux:subheading>Anstehende Fortbildungen</flux:subheading>
        </div>
        @if(auth()->user()->role === 'admin')
            <flux:button :href="route('workshops.create')" icon="plus" variant="primary">Neuer Workshop</flux:button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($workshops as $workshop)
            <div class="flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="p-6 flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <flux:badge color="zinc">{{ $workshop->date->format('d.m.Y H:i') }}</flux:badge>
                        @if($workshop->participants->contains(auth()->user()))
                            <flux:badge color="green" icon="check">Angemeldet</flux:badge>
                        @endif
                    </div>
                    
                    <h3 class="text-lg font-bold mb-2">{{ $workshop->title }}</h3>
                    <p class="text-sm text-zinc-500 mb-4 line-clamp-3">{{ $workshop->description }}</p>
                    
                    <div class="flex items-center text-xs text-zinc-400 gap-4 mb-4">
                        @if($workshop->location)
                            <div class="flex items-center gap-1">
                                <flux:icon name="map-pin" class="size-4" />
                                <span>{{ $workshop->location }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-1">
                            <flux:icon name="users" class="size-4" />
                            <span>{{ $workshop->participants->count() }} Teilnehmer</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                    <flux:button wire:click="toggleRegistration({{ $workshop->id }})" 
                        variant="{{ $workshop->participants->contains(auth()->user()) ? 'danger' : 'primary' }}" 
                        size="sm"
                    >
                        {{ $workshop->participants->contains(auth()->user()) ? 'Abmelden' : 'Anmelden' }}
                    </flux:button>
                    
                    @if(auth()->user()->role === 'admin')
                    <flux:dropdown>
                        <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />

                        <flux:menu>
                            <flux:menu.item wire:click="delete({{ $workshop->id }})" wire:confirm="Wirklich löschen?" icon="trash" variant="danger">Löschen</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                    @endif
                </div>
            </div>
        @endforeach
        
        @if($workshops->isEmpty())
            <div class="col-span-full text-center py-10 text-zinc-500">Keine anstehenden Workshops in der Zukunft.</div>
        @endif
    </div>
</div>
