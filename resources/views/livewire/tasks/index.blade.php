<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Task;
use App\Models\User;

new #[Layout('components.layouts.app', ['title' => 'Aufgaben'])] 
class extends Component {
    public $title = '';
    public $description = '';
    public $due_date = '';
    public $assigned_to_id = '';
    public $importance = 'normal';

    public function with()
    {
        return [
            'tasks' => Task::with(['assignedTo', 'createdBy'])
                ->orderBy('is_completed', 'asc') // Pendings first
                ->orderBy('importance', 'desc') // High importance first
                ->orderBy('due_date', 'asc') // Earliest due date first
                ->get(),
            'users' => User::where('status', 'active')->orderBy('name')->get(),
        ];
    }

    public function createTask()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'assigned_to_id' => 'nullable|exists:users,id',
            'importance' => 'required|in:normal,high',
        ]);

        Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date ?: null,
            'assigned_to_id' => $this->assigned_to_id ?: null,
            'importance' => $this->importance,
            'created_by_id' => auth()->id(),
        ]);

        $this->reset(['title', 'description', 'due_date', 'assigned_to_id', 'importance']);
        session()->flash('status', 'Aufgabe erstellt.');
        $this->dispatch('close-modal', name: 'create-task');
    }

    public function toggleStatus(Task $task)
    {
        $task->update(['is_completed' => !$task->is_completed]);
    }

    public function delete(Task $task)
    {
        $task->delete();
    }
}; ?>

<div class="flex flex-col gap-6 p-6 h-full">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Aufgaben</flux:heading>
            <flux:subheading>Team-Tasks & To-Dos</flux:subheading>
        </div>
        <flux:modal.trigger name="create-task">
            <flux:button icon="plus" variant="primary">Neue Aufgabe</flux:button>
        </flux:modal.trigger>
    </div>

    <!-- Tasks Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($tasks as $task)
            <div class="flex flex-col p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm {{ $task->is_completed ? 'opacity-60' : '' }}">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex gap-2">
                         @if($task->importance === 'high')
                            <flux:badge color="red" size="sm">Wichtig</flux:badge>
                        @endif
                         @if($task->due_date)
                            <flux:badge color="{{ $task->due_date->isPast() && !$task->is_completed ? 'red' : 'zinc' }}" size="sm" icon="calendar">
                                {{ $task->due_date->format('d.m.') }}
                            </flux:badge>
                        @endif
                    </div>
                    
                    <flux:dropdown>
                        <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />
                        <flux:menu>
                            <flux:menu.item wire:click="toggleStatus({{ $task->id }})" icon="{{ $task->is_completed ? 'arrow-uturn-left' : 'check' }}">
                                {{ $task->is_completed ? 'Als offen markieren' : 'Erledigen' }}
                            </flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item wire:click="delete({{ $task->id }})" wire:confirm="Löschen?" icon="trash" variant="danger">Löschen</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <div class="flex-1 mb-4">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 {{ $task->is_completed ? 'line-through' : '' }}">{{ $task->title }}</h3>
                    @if($task->description)
                        <p class="text-sm text-zinc-500 mt-1 line-clamp-2">{{ $task->description }}</p>
                    @endif
                </div>

                <div class="flex justify-between items-center pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        @if($task->assignedTo)
                            <flux:tooltip content="Zugewiesen an {{ $task->assignedTo->name }}">
                                <div class="size-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-[10px] font-bold text-indigo-700 dark:text-indigo-300">
                                    {{ $task->assignedTo->initials() }}
                                </div>
                            </flux:tooltip>
                        @else
                            <flux:tooltip content="Nicht zugewiesen">
                                <div class="size-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                                    <flux:icon name="user" class="size-3" />
                                </div>
                            </flux:tooltip>
                        @endif
                        <span class="text-xs text-zinc-400">Erstellt von {{ $task->createdBy->initials() }}</span>
                    </div>

                    <flux:button wire:click="toggleStatus({{ $task->id }})" size="sm" variant="{{ $task->is_completed ? 'outline' : 'filled' }}" class="!h-8 !px-3">
                         {{ $task->is_completed ? 'Erledigt' : 'Abschließen' }}
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center p-12 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700">
                <p class="text-zinc-500 font-medium">Keine Aufgaben gefunden.</p>
            </div>
        @endforelse
    </div>

    <!-- Create Modal -->
    <flux:modal name="create-task" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Neue Aufgabe</flux:heading>
            </div>
            
            <form wire:submit="createTask" class="space-y-4">
                <flux:input wire:model="title" label="Titel" placeholder="Was ist zu tun?" required />
                
                <flux:textarea wire:model="description" label="Beschreibung" rows="3" placeholder="Details..." />

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="due_date" type="date" label="Fällig am" />
                    
                    <flux:select wire:model="importance" label="Wichtigkeit">
                        <flux:select.option value="normal">Normal</flux:select.option>
                        <flux:select.option value="high">Hoch</flux:select.option>
                    </flux:select>
                </div>

                <flux:select wire:model="assigned_to_id" label="Zuweisen an" placeholder="Niemand (Offen)">
                    <flux:select.option value="">Offen für alle</flux:select.option>
                    @foreach($users as $user)
                        <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                
                <div class="flex justify-end gap-2 pt-2">
                    <flux:button type="submit" variant="primary">Erstellen</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
