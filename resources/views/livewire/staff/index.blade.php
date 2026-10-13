<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithPagination;

new 
#[Layout('components.layouts.app', ['title' => 'Mitarbeiter'])]
class extends Component {
    use WithPagination;

    public $search = '';

    public function with()
    {
        return [
            'users' => User::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->with('group')
                ->paginate(10),
        ];
    }
    
    public function delete(User $user)
    {
        if($user->id === auth()->id()) {
            return;
        }
        $user->delete();
    }
}; ?>

<div class="flex flex-col gap-6 p-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Mitarbeiterverwaltung</flux:heading>
            <flux:subheading>Übersicht aller Benutzer & Rollen</flux:subheading>
        </div>
    </div>

    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Suche..." class="max-w-sm" />

    <div class="overflow-hidden border border-zinc-200 rounded-xl shadow-sm dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Name</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Email</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Rolle</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Gruppe</th>
                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-zinc-500 dark:text-zinc-400">Status</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-900 dark:divide-zinc-700">
                @foreach ($users as $user)
                    <tr wire:key="{{ $user->id }}" class="transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="size-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                    {{ $user->initials() }}
                                </div>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <flux:badge color="{{ $user->role === 'admin' ? 'red' : 'zinc' }}">{{ match($user->role) { 'admin' => 'Admin', default => 'Mitarbeiter' } }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">{{ $user->group->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <flux:badge color="{{ $user->status === 'active' ? 'green' : 'red' }}">{{ match($user->status) { 'active' => 'Aktiv', 'inactive' => 'Inaktiv', default => $user->status } }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                            <flux:dropdown>
                                <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />

                                <flux:menu>
                                    <flux:menu.item :href="route('staff.edit', $user)" icon="pencil-square">Bearbeiten</flux:menu.item> 
                                    @if($user->id !== auth()->id())
                                        <flux:menu.item wire:click="delete({{ $user->id }})" wire:confirm="Wirklich löschen?" icon="trash" variant="danger">Löschen</flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
