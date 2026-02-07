<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Contact;
use Illuminate\Validation\Rule;

new #[Layout('components.layouts.app', ['title' => 'Kontakte'])] 
class extends Component {
    public $search = '';
    public $name = '';
    public $category = 'misc';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $notes = '';

    public function with()
    {
        return [
            'contacts' => Contact::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('category', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                )
                ->orderBy('name')
                ->get(),
        ];
    }

    public function createContact()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Contact::create([
            'name' => $this->name,
            'category' => $this->category,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'notes' => $this->notes,
            'created_by_id' => auth()->id(),
        ]);

        $this->reset(['name', 'category', 'phone', 'email', 'address', 'notes']);
        session()->flash('status', 'Kontakt erstellt.');
        $this->dispatch('close-modal', name: 'create-contact');
    }

    public function delete(Contact $contact)
    {
        $contact->delete();
    }
}; ?>

<div class="flex flex-col gap-6 p-6 h-full">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Wichtige Kontakte</flux:heading>
            <flux:subheading>Telefonbuch & Adressen</flux:subheading>
        </div>
        <flux:modal.trigger name="create-contact">
            <flux:button icon="plus" variant="primary">Neuer Kontakt</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Suche..." class="max-w-sm" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($contacts as $contact)
            <div class="group relative flex flex-col p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm hover:border-zinc-300 dark:hover:border-zinc-600 transition">
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition">
                    <flux:button wire:click="delete({{ $contact->id }})" wire:confirm="Kontakt löschen?" icon="trash" variant="ghost" size="sm" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" />
                </div>

                <div class="flex items-center gap-3 mb-4">
                    <div class="size-10 rounded-full flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 text-zinc-500">
                        @php
                            $icon = match($contact->category) {
                                'doctor' => 'briefcase', // Using 'briefcase' as a generic medical/professional icon for now
                                'pharmacy' => 'beaker',
                                'taxi' => 'truck',
                                'emergency' => 'exclamation-triangle',
                                default => 'phone',
                            };
                            
                            // Map category to German label
                            $categoryLabel = match($contact->category) {
                                'doctor' => 'Arzt / Praxis',
                                'pharmacy' => 'Apotheke',
                                'taxi' => 'Fahrdienst / Taxi',
                                'emergency' => 'Notfall / Polizei / FW',
                                default => 'Sonstiges',
                            };
                        @endphp
                        <flux:icon :name="$icon" class="size-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-zinc-900 dark:text-zinc-100">{{ $contact->name }}</h3>
                        <span class="inline-flex items-center rounded-md bg-zinc-50 dark:bg-zinc-800 px-2 py-1 text-xs font-medium text-zinc-600 dark:text-zinc-400 ring-1 ring-inset ring-zinc-500/10">
                            {{ $categoryLabel }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    @if($contact->phone)
                        <div class="flex items-center gap-3 text-zinc-600 dark:text-zinc-400">
                            <flux:icon name="phone" class="size-4 opacity-70" />
                            <a href="tel:{{ $contact->phone }}" class="hover:underline hover:text-blue-600">{{ $contact->phone }}</a>
                        </div>
                    @endif

                    @if($contact->email)
                        <div class="flex items-center gap-3 text-zinc-600 dark:text-zinc-400">
                            <flux:icon name="envelope" class="size-4 opacity-70" />
                            <a href="mailto:{{ $contact->email }}" class="hover:underline hover:text-blue-600">{{ $contact->email }}</a>
                        </div>
                    @endif

                    @if($contact->address)
                        <div class="flex items-start gap-3 text-zinc-600 dark:text-zinc-400">
                            <flux:icon name="map-pin" class="size-4 opacity-70 mt-0.5" />
                            <span class="whitespace-pre-line">{{ $contact->address }}</span>
                        </div>
                    @endif

                    @if($contact->notes)
                        <div class="pt-3 mt-1 border-t border-zinc-100 dark:border-zinc-800 text-zinc-500 text-xs italic">
                            {{ $contact->notes }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center p-12 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700">
                <p class="text-zinc-500 font-medium">Keine Kontakte gefunden.</p>
            </div>
        @endforelse
    </div>

    <!-- Create Modal -->
    <flux:modal name="create-contact" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Neuer Kontakt</flux:heading>
            </div>
            
            <form wire:submit="createContact" class="space-y-4">
                <flux:input wire:model="name" label="Name" placeholder="Dr. Müller / Adler Apotheke" required />
                
                <flux:select wire:model="category" label="Kategorie">
                    <flux:select.option value="doctor">Arzt / Praxis</flux:select.option>
                    <flux:select.option value="pharmacy">Apotheke</flux:select.option>
                    <flux:select.option value="taxi">Fahrdienst / Taxi</flux:select.option>
                    <flux:select.option value="emergency">Notfall / Behörde</flux:select.option>
                    <flux:select.option value="misc">Sonstiges</flux:select.option>
                </flux:select>

                <flux:input wire:model="phone" label="Telefon" type="tel" />
                <flux:input wire:model="email" label="Email" type="email" />
                
                <flux:textarea wire:model="address" label="Adresse" rows="2" placeholder="Straße, PLZ Ort" />
                <flux:textarea wire:model="notes" label="Notizen" rows="2" placeholder="Öffnungszeiten, Ansprechpartner..." />
                
                <div class="flex justify-end gap-2 pt-2">
                    <flux:button type="submit" variant="primary">Speichern</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
