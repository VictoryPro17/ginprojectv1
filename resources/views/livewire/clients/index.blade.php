<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Client;
use Livewire\WithPagination;

new 
#[Layout('components.layouts.app', ['title' => 'Klienten'])]
class extends Component {
    use WithPagination;

    public $search = '';

    public function with()
    {
        return [
            'clients' => Client::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('group')
                ->latest()
                ->paginate(10),
        ];
    }

    public function delete(Client $client)
    {
        $client->delete();
    }
}; ?>

<div class="flex flex-col gap-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Klientenverwaltung</flux:heading>
            <flux:subheading>Übersicht aller Klienten</flux:subheading>
        </div>
        @if(auth()->user()->role === 'admin')
            <flux:button :href="route('clients.create')" icon="plus" variant="primary">Neuer Klient</flux:button>
        @endif
    </div>

    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Suche..." class="max-w-sm" />

    <div class="overflow-hidden border border-zinc-200 rounded-xl shadow-sm dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Name</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Gruppe</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Geburtsdatum</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Betreuer</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-900 dark:divide-zinc-700">
                @foreach ($clients as $client)
                    <tr wire:key="{{ $client->id }}" class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-zinc-900 dark:text-zinc-100">{{ $client->name }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                            @if($client->group)
                                <flux:badge color="zinc">{{ $client->group->name }}</flux:badge>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $client->dob->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $client->caretaker->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            @if(auth()->user()->role === 'admin')
                            <flux:dropdown>
                                <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />

                                <flux:menu>
                                    <flux:menu.item :href="route('clients.edit', $client)" icon="pencil-square">Bearbeiten</flux:menu.item>
                                    <flux:menu.item wire:click="delete({{ $client->id }})" wire:confirm="Wirklich löschen?" icon="trash" variant="danger">Löschen</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>
</div>
