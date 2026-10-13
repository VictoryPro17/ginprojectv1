<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Group;

new 
#[Layout('components.layouts.app', ['title' => 'Gruppen'])]
class extends Component {
    public function with()
    {
        return [
            'groups' => Group::with('teamLead')->withCount('users')->get(),
        ];
    }
    
    public function delete(Group $group)
    {
        $group->delete();
    }
}; ?>

<div class="flex flex-col gap-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Gruppenverwaltung</flux:heading>
            <flux:subheading>Übersicht aller Wohngruppen</flux:subheading>
        </div>
        <flux:modal.trigger name="create-group">
            <flux:button icon="plus" variant="primary">Neue Gruppe</flux:button>
        </flux:modal.trigger>
    </div>

    <div class="overflow-hidden border border-zinc-200 rounded-xl shadow-sm dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Name</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Gruppenleitung</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Mitarbeiter</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-900 dark:divide-zinc-700">
                @foreach ($groups as $group)
                    <tr wire:key="{{ $group->id }}" class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-zinc-900 dark:text-zinc-100">{{ $group->name }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $group->teamLead->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $group->users_count }} Mitarbeiter</td>
                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <flux:button wire:click="delete({{ $group->id }})" wire:confirm="Wirklich löschen?" icon="trash" variant="danger" size="sm" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <flux:modal name="create-group" class="md:w-96">
        <livewire:groups.create /> 
    </flux:modal>
</div>
