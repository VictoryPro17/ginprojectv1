<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Shift;
use Carbon\Carbon;

new 
#[Layout('components.layouts.app', ['title' => 'Dienstplan'])]
class extends Component {
    public $month;
    public $year;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function with()
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfWeek(); 
        $end = Carbon::create($this->year, $this->month, 1)->endOfMonth()->endOfWeek();
        
        $shifts = Shift::whereBetween('start_time', [$start, $end])
            ->with('user')
            ->get();
            
        // Map shifts to days
        $calendar = [];
        $current = $start->copy();
        
        while ($current <= $end) {
            $dayShifts = $shifts->filter(function ($shift) use ($current) {
                return $shift->start_time->isSameDay($current);
            });
            
            $calendar[] = [
                'date' => $current->copy(),
                'shifts' => $dayShifts,
                'isCurrentMonth' => $current->month == $this->month,
            ];
            
            $current->addDay();
        }

        return [
            'calendar' => $calendar,
            'currentDate' => Carbon::create($this->year, $this->month, 1),
        ];
    }
}; ?>

<div class="flex flex-col gap-6 p-6 h-full">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <flux:heading size="xl">Dienstplan</flux:heading>
            <div class="flex items-center gap-2">
                <flux:button wire:click="prevMonth" icon="chevron-left" variant="ghost" size="sm" />
                <span class="font-semibold text-lg w-32 text-center">{{ $currentDate->format('F Y') }}</span>
                <flux:button wire:click="nextMonth" icon="chevron-right" variant="ghost" size="sm" />
            </div>
        </div>
        @if(auth()->user()->role === 'admin')
            <flux:button :href="route('shifts.create')" icon="plus" variant="primary">Neuer Dienst</flux:button>
        @endif
    </div>

    <!-- Days Header -->
    <div class="grid grid-cols-7 gap-px bg-zinc-200 dark:bg-zinc-700 rounded-t-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @foreach(['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $day)
            <div class="bg-zinc-50 dark:bg-zinc-800 p-2 text-center text-sm font-semibold text-zinc-500">
                {{ $day }}
            </div>
        @endforeach
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-px bg-zinc-200 dark:bg-zinc-700 border-x border-b border-zinc-200 dark:border-zinc-700 rounded-b-xl overflow-hidden shadow-sm">
        @foreach ($calendar as $day)
            <div class="bg-white dark:bg-zinc-900 min-h-[120px] p-2 flex flex-col gap-1 {{ !$day['isCurrentMonth'] ? 'bg-zinc-50/50 dark:bg-zinc-900/50 text-zinc-400' : '' }}">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-semibold {{ $day['date']->isToday() ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center' : '' }}">
                        {{ $day['date']->day }}
                    </span>
                </div>
                
                <div class="space-y-1">
                    @foreach ($day['shifts'] as $shift)
                        @php
                            $colors = [
                                'work' => 'blue',
                                'vacation' => 'green',
                                'sick' => 'red',
                                'training' => 'yellow',
                            ];
                            $color = $colors[$shift->type] ?? 'zinc';
                            $bg = match($color) {
                                'blue' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-800',
                                'green' => 'bg-green-50 dark:bg-green-900/20 border-green-100 dark:border-green-800',
                                'red' => 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800',
                                'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-100 dark:border-yellow-800',
                                default => 'bg-zinc-50 dark:bg-zinc-800 border-zinc-100 dark:border-zinc-700',
                            };
                            $type_label = match($shift->type) {
                                'work' => 'Arbeit',
                                'vacation' => 'Urlaub',
                                'sick' => 'Krank',
                                'training' => 'Fortbildung',
                                default => $shift->type
                            };
                        @endphp
                        <div class="text-xs p-1.5 rounded {{ $bg }} border truncate cursor-pointer hover:opacity-80 transition" title="{{ $shift->user->name }}: {{ $type_label }}">
                            <div class="font-medium truncate">{{ $shift->user->initials() }}</div>
                            <div class="text-[0.6rem] opacity-75">{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="text-right">
            <flux:button icon="printer" variant="ghost" disabled>PDF Export (Demnächst)</flux:button>
    </div>
</div>
