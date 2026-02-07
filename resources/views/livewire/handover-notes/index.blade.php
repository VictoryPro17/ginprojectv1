<?php

use App\Models\HandoverNote;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

new #[Layout('components.layouts.app', ['title' => 'Übergabebuch'])] 
class extends Component {
    #[Url]
    public $filter = 'active';

    public $content = '';
    public $priority = 'normal';
    public $category = 'general';

    public function with()
    {
        return [
            'notes' => HandoverNote::with('user')
                ->when($this->filter === 'completed', fn($q) => $q->where('is_completed', true))
                ->when($this->filter !== 'completed', fn($q) => $q->where('is_completed', false))
                ->latest()
                ->get(),
        ];
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }

    public function createNote()
    {
        $this->validate([
            'content' => 'required|string|max:1000',
            'priority' => 'required|in:normal,high,critical',
            'category' => 'required|in:general,clients,facility,personal',
        ]);

        HandoverNote::create([
            'user_id' => auth()->id(),
            'content' => $this->content,
            'priority' => $this->priority,
            'category' => $this->category,
        ]);

        $this->reset(['content', 'priority', 'category']);
        session()->flash('status', 'Notiz erstellt.');
    }

    public function toggleStatus(HandoverNote $note)
    {
        $note->update(['is_completed' => !$note->is_completed]);
    }
    
    public function deleteNote(HandoverNote $note)
    {
        if ($note->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return;
        }
        $note->delete();
    }
}; ?>

<div class="h-full w-full flex-1 flex flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Übergabebuch</flux:heading>
            <flux:subheading>Wichtige Infos für das nächste Team.</flux:subheading>
        </div>
        <div>
            <flux:modal.trigger name="create-note">
                <flux:button variant="primary" icon="plus">Neuer Eintrag</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 border-b border-zinc-200 dark:border-zinc-700 pb-2">
        <button wire:click="setFilter('active')" class="px-4 py-2 text-sm font-medium {{ $filter !== 'completed' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-zinc-500 hover:text-zinc-700' }}">Offen</button>
        <button wire:click="setFilter('completed')" class="px-4 py-2 text-sm font-medium {{ $filter === 'completed' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-zinc-500 hover:text-zinc-700' }}">Erledigt / Archiv</button>
    </div>

    <div class="space-y-4">
        @forelse($notes as $note)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 p-4 rounded-xl shadow-sm flex gap-4 {{ $note->priority === 'critical' ? 'border-l-4 border-l-red-500' : ($note->priority === 'high' ? 'border-l-4 border-l-orange-500' : '') }}">
                <div class="flex flex-col items-center gap-1 w-12 text-center text-zinc-500 pt-1">
                    <span class="text-xs font-bold">{{ $note->created_at->format('d.m.') }}</span>
                    <span class="text-xs">{{ $note->created_at->format('H:i') }}</span>
                </div>
                
                <div class="flex-1 space-y-2">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-2">
                            <flux:badge size="sm" color="{{ match($note->category) { 'clients' => 'blue', 'facility' => 'orange', 'personal' => 'purple', default => 'zinc' } }}">
                                {{ match($note->category) { 'clients' => 'Klienten', 'facility' => 'Haushalt/Technik', 'personal' => 'Personal', default => 'Allgemein' } }}
                            </flux:badge>
                            @if($note->priority === 'critical')
                                <flux:badge size="sm" color="red">KRITISCH</flux:badge>
                            @elseif($note->priority === 'high')
                                <flux:badge size="sm" color="orange">Wichtig</flux:badge>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-2">
                             <span class="text-xs text-zinc-400">von {{ $note->user->name }}</span>
                            @if(auth()->user()->role === 'admin' || auth()->id() === $note->user_id)
                                <flux:button wire:click="deleteNote({{ $note->id }})" icon="trash" variant="ghost" size="xs" wire:confirm="Wirklich löschen?" />
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-zinc-900 dark:text-zinc-100 {{ $note->is_completed ? 'line-through opacity-60' : '' }}">
                        {{ $note->content }}
                    </p>

                    <div class="flex justify-end pt-2">
                         <flux:button wire:click="toggleStatus({{ $note->id }})" size="sm" variant="{{ $note->is_completed ? 'outline' : 'filled' }}" icon="{{ $note->is_completed ? 'arrow-uturn-left' : 'check' }}">
                            {{ $note->is_completed ? 'Als offen markieren' : 'Erledigt' }}
                        </flux:button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-12 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700">
                <flux:icon name="document-text" class="size-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                <p class="text-zinc-500 font-medium">Keine Einträge gefunden.</p>
            </div>
        @endforelse
    </div>

    <!-- Create Modal -->
    <flux:modal name="create-note" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Neue Notiz</flux:heading>
                <flux:subheading>Füge eine Information zum Übergabebuch hinzu.</flux:subheading>
            </div>
            
            <form wire:submit="createNote" class="space-y-4">
                <flux:select wire:model="category" label="Kategorie">
                    <option value="general">Allgemein</option>
                    <option value="clients">Klienten</option>
                    <option value="facility">Haushalt & Technik</option>
                    <option value="personal">Personal</option>
                </flux:select>

                <flux:select wire:model="priority" label="Dringlichkeit">
                    <option value="normal">Normal</option>
                    <option value="high">Hoch</option>
                    <option value="critical">Kritisch</option>
                </flux:select>

                <flux:textarea wire:model="content" label="Inhalt" rows="4" placeholder="Was ist passiert? Was muss erledigt werden?" required />
                
                <div class="flex justify-end gap-2">
                    <flux:button type="submit" variant="primary">Speichern</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
