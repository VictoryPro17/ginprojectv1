<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\DailyLog;
use App\Models\Client;

new 
#[Layout('components.layouts.app', ['title' => 'Neuer Eintrag'])]
class extends Component {
    public $client_id = '';
    public $date = '';
    public $mood = '';
    public $activity = '';
    public $special_incidents = '';
    public $hydration_ml = null;
    public $stool_passed = false;
    public $urine_desc = '';
    
    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'mood' => 'nullable|string',
            'activity' => 'nullable|string',
            'special_incidents' => 'nullable|string',
            'hydration_ml' => 'nullable|integer|min:0',
            'stool_passed' => 'boolean',
            'urine_desc' => 'nullable|string',
        ]);

        DailyLog::create([
            'client_id' => $this->client_id,
            'user_id' => auth()->id(),
            'date' => $this->date,
            'mood' => $this->mood,
            'activity' => $this->activity,
            'special_incidents' => $this->special_incidents,
            'hydration_ml' => $this->hydration_ml,
            'stool_passed' => $this->stool_passed,
            'urine_desc' => $this->urine_desc,
        ]);

        return redirect()->route('daily-logs.index');
    }

    public function with()
    {
        return [
            'clients' => Client::all(),
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto p-6">
    <div class="flex items-center gap-4 mb-8">
        <flux:button :href="route('daily-logs.index')" icon="arrow-left" variant="ghost" />
        <flux:heading size="xl">Neuer Tageseintrag</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:select wire:model="client_id" label="Klient" placeholder="Wähle einen Klienten" required>
                @foreach ($clients as $client)
                    <flux:select.option value="{{ $client->id }}">{{ $client->name }}</flux:select.option>
                @endforeach
            </flux:select>
            
            <flux:input wire:model="date" type="date" label="Datum" required />
        </div>

        <flux:radio.group wire:model="mood" label="Aktuelle Stimmung / Laune" variant="segmented">
            <flux:radio value="Sehr gut" label="😊" />
            <flux:radio value="Gut" label="🙂" />
            <flux:radio value="Neutral" label="😐" />
            <flux:radio value="Schlecht" label="🙁" />
            <flux:radio value="Kritisch" label="😫" />
        </flux:radio.group>

        <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg space-y-4">
            <flux:heading size="lg">Vitalwerte & Pflege</flux:heading>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <flux:input wire:model="hydration_ml" type="number" label="Trinkmenge (ml)" placeholder="z.B. 1500" />
                
                <flux:select wire:model="urine_desc" label="Urin / KV">
                    <flux:select.option value="">Keine Angabe</flux:select.option>
                    <flux:select.option value="unauffällig">Unauffällig / Normal</flux:select.option>
                    <flux:select.option value="konzentriert">Konzentriert / Dunkel</flux:select.option>
                    <flux:select.option value="blutig">Blutig</flux:select.option>
                    <flux:select.option value="wenig">Wenig Menge</flux:select.option>
                    <flux:select.option value="viel">Viel Menge</flux:select.option>
                </flux:select>
                
                <flux:checkbox wire:model="stool_passed" label="Stuhlgang erfolgt?" />
            </div>
        </div>

        <flux:textarea wire:model="activity" label="Aktivitäten / Verlauf" rows="4" placeholder="Was wurde heute gemacht? Wie war der Tag?" />
        
        <flux:textarea wire:model="special_incidents" label="Besondere Vorkommnisse" placeholder="Medizinische Vorfälle, Aggressionen, etc." />

        <div class="flex justify-end gap-2">
            <flux:button :href="route('daily-logs.index')" variant="ghost">Abbrechen</flux:button>
            <flux:button type="submit" variant="primary">Speichern</flux:button>
        </div>
    </form>
</div>
