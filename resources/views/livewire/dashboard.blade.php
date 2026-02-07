<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Client;
use App\Models\Shift;
use App\Models\DailyLog;
use App\Models\Workshop;
use App\Models\Announcement;

new 
#[Layout('components.layouts.app', ['title' => 'Dashboard'])]
class extends Component {
    public $announcement_title = '';
    public $announcement_content = '';

    public function with()
    {
        return [
            'totalClients' => Client::count(),
            'activeStaff' => User::where('status', 'active')->count(),
            'todaysLogs' => DailyLog::whereDate('date', now())->count(),
            'nextShift' => Shift::where('user_id', auth()->id())
                ->where('start_time', '>', now())
                ->orderby('start_time')
                ->first(),
            'upcomingWorkshops' => Workshop::where('date', '>', now())
                ->orderBy('date')
                ->take(3)
                ->get(),
            'announcements' => Announcement::with('user')->latest()->take(5)->get(),
            'upcomingBirthdays' => Client::get()
                ->filter(fn($c) => $c->dob && (
                    $c->dob->copy()->year(now()->year)->between(now()->startOfDay(), now()->addDays(30)->endOfDay()) ||
                    $c->dob->copy()->year(now()->year + 1)->between(now()->startOfDay(), now()->addDays(30)->endOfDay())
                ))
                ->sortBy(fn($c) => $c->dob->copy()->year(now()->year)->isPast() ? $c->dob->copy()->year(now()->year + 1) : $c->dob->copy()->year(now()->year))
                ->take(5),
            'todaysShifts' => Shift::with('user')
                ->whereDate('start_time', now())
                ->orderBy('start_time')
                ->get(),
        ];
    }

    public function createAnnouncement()
    {
        if (auth()->user()->role !== 'admin') {
            return;
        }

        $this->validate([
            'announcement_title' => 'required|string|max:255',
            'announcement_content' => 'required|string',
        ]);

        Announcement::create([
            'title' => $this->announcement_title,
            'content' => $this->announcement_content,
            'user_id' => auth()->id(),
        ]);

        $this->reset(['announcement_title', 'announcement_content']);
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        if (auth()->user()->role !== 'admin') {
            return;
        }
        $announcement->delete();
    }
}; ?>

<div class="h-full w-full flex-1 flex flex-col gap-6 p-6">
    <!-- Welcome Section -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Willkommen zurück, {{ auth()->user()->name }}!</flux:heading>
            <flux:subheading>Hier ist der aktuelle Überblick für heute.</flux:subheading>
        </div>
        <div class="hidden md:block">
            <span class="text-sm text-zinc-500">{{ now()->translatedFormat('l, d. F Y') }}</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stats cards remain same -->
        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg">
                    <flux:icon name="users" class="size-6" />
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 uppercase">Klienten</p>
                    <p class="text-2xl font-bold">{{ $totalClients }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-lg">
                    <flux:icon name="identification" class="size-6" />
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 uppercase">Aktive Mitarbeiter</p>
                    <p class="text-2xl font-bold">{{ $activeStaff }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-lg">
                    <flux:icon name="document-text" class="size-6" />
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 uppercase">Heutige Einträge</p>
                    <p class="text-2xl font-bold">{{ $todaysLogs }}</p>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-lg">
                    <flux:icon name="calendar" class="size-6" />
                </div>
                <div>
                    <p class="text-xs font-medium text-zinc-500 uppercase">Nächster Dienst</p>
                     @if($nextShift)
                        <p class="text-lg font-bold truncate">{{ $nextShift->start_time->format('d.m. H:i') }}</p>
                    @else
                        <p class="text-sm font-medium text-zinc-500">Keine Dienste</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Announcements & Workshops -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Announcements Section (New) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:heading size="lg">Neuigkeiten & Infos</flux:heading>
                        <flux:badge color="blue" size="sm">News Board</flux:badge>
                    </div>
                     @if(auth()->user()->role === 'admin')
                        <flux:modal.trigger name="create-announcement">
                            <flux:button size="sm" icon="plus" variant="primary">Neu</flux:button>
                        </flux:modal.trigger>
                    @endif
                </div>

                <div class="space-y-4">
                    @forelse($announcements as $announcement)
                        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm relative group">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold text-zinc-600 dark:text-zinc-400">
                                        {{ $announcement->user->initials() }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $announcement->user->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $announcement->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                @if(auth()->user()->role === 'admin')
                                    <flux:button wire:click="deleteAnnouncement({{ $announcement->id }})" wire:confirm="Post löschen?" icon="trash" variant="ghost" size="sm" class="opacity-0 group-hover:opacity-100 transition-opacity" />
                                @endif
                            </div>
                            
                            <h3 class="text-lg font-bold mb-1 text-zinc-900 dark:text-zinc-100">{{ $announcement->title }}</h3>
                            <div class="prose prose-sm dark:prose-invert text-zinc-600 dark:text-zinc-400">
                                {{ $announcement->content }}
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 text-zinc-500">
                            Keine Neuigkeiten verfügbar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Events Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Upcoming Workshops -->
                <div class="space-y-4">
                     <div class="flex items-center justify-between">
                        <flux:heading size="lg">Anstehende Workshops</flux:heading>
                        <flux:button :href="route('workshops.index')" variant="ghost" size="sm" icon="arrow-right">Alle</flux:button>
                    </div>
        
                    <div class="space-y-3">
                        @forelse($upcomingWorkshops as $workshop)
                            <div class="flex items-center p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                                 <div class="flex-shrink-0 w-12 text-center mr-4 p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                                    <div class="text-xs text-zinc-500 uppercase">{{ $workshop->date->translatedFormat('M') }}</div>
                                    <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $workshop->date->format('d') }}</div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 truncate w-32">{{ $workshop->title }}</h4>
                                    <p class="text-xs text-zinc-500">{{ $workshop->date->format('H:i') }} Uhr</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 text-zinc-500">
                                Keine anstehenden Workshops.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Birthdays -->
                <div class="space-y-4">
                     <div class="flex items-center justify-between">
                        <flux:heading size="lg">Geburtstage</flux:heading>
                        <div class="h-8"></div> <!-- Spacer -->
                    </div>
        
                    <div class="space-y-3">
                        @forelse($upcomingBirthdays as $client)
                            <div class="flex items-center p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
                                 <div class="flex-shrink-0 w-12 text-center mr-4 p-2 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-lg">
                                    <flux:icon name="cake" class="size-6 mx-auto" />
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $client->name }}</h4>
                                    <p class="text-xs text-zinc-500">
                                        wird {{ $client->dob->age + 1 }} • {{ $client->dob->format('d.m.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 text-zinc-500">
                                Keine Geburtstage in Kürze.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Sidebar -->
        <div class="space-y-6">
            <!-- Today's Staff -->
            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <flux:heading size="md" class="mb-4">Wer ist heute da?</flux:heading>
                <div class="space-y-3">
                    @forelse($todaysShifts as $shift)
                        <div class="flex items-center gap-3">
                            <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold text-zinc-600 dark:text-zinc-400">
                                {{ $shift->user->initials() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $shift->user->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}</p>
                            </div>
                             <div class="size-2 rounded-full {{ $shift->start_time->isPast() && $shift->end_time->isFuture() ? 'bg-green-500 animate-pulse' : 'bg-zinc-300 dark:bg-zinc-600' }}"></div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 italic">Keine Dienste heute eingetragen.</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-4">
                <flux:heading size="lg">Schnellzugriff</flux:heading>
                
                <div class="grid gap-3">
                    <flux:button :href="route('tasks.index')" icon="check-circle" variant="filled" class="w-full justify-start py-4 h-auto bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800">
                        <div class="text-left ml-2">
                            <div class="font-semibold">Aufgaben prüfen</div>
                            <div class="text-xs opacity-75 font-normal">Offene To-Dos erledigen</div>
                        </div>
                    </flux:button>
                    
                    <flux:button :href="route('daily-logs.create')" icon="pencil-square" variant="filled" class="w-full justify-start py-4 h-auto">
                        <div class="text-left ml-2">
                            <div class="font-semibold">Tagesdoku schreiben</div>
                            <div class="text-xs opacity-75 font-normal">Erfasse Vorkommnisse & Stimmung</div>
                        </div>
                    </flux:button>

                     @if(auth()->user()->role === 'admin')
                    <flux:button :href="route('clients.create')" icon="user-plus" variant="outline" class="w-full justify-start py-4 h-auto bg-white dark:bg-zinc-900">
                       <div class="text-left ml-2">
                            <div class="font-semibold">Neuer Klient</div>
                            <div class="text-xs text-zinc-500 font-normal">Stammdaten anlegen</div>
                        </div>
                    </flux:button>
                    @endif
    
                    <flux:button :href="route('shifts.index')" icon="calendar-days" variant="outline" class="w-full justify-start py-4 h-auto bg-white dark:bg-zinc-900">
                        <div class="text-left ml-2">
                            <div class="font-semibold">Mein Dienstplan</div>
                            <div class="text-xs text-zinc-500 font-normal">Kommende Schichten prüfen</div>
                        </div>
                    </flux:button>
                </div>
                
                <div class="mt-6 p-4 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md">
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest opacity-75">Tipp des Tages</span>
                        <p class="text-sm font-medium">"Dokumentation ist der Schlüssel zu einer qualitativ hochwertigen Betreuung. Vergiss nicht, besondere Vorkommnisse sofort zu notieren!"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Create Announcement Modal -->
    <flux:modal name="create-announcement" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Neue Ankündigung</flux:heading>
                <flux:subheading>Teile Neuigkeiten mit dem gesamten Team.</flux:subheading>
            </div>
            
            <form wire:submit="createAnnouncement" class="space-y-4">
                <flux:input wire:model="announcement_title" label="Titel" placeholder="Wichtige Info..." required />
                <flux:textarea wire:model="announcement_content" label="Inhalt" rows="4" placeholder="Deine Nachricht an das Team..." required />
                
                <div class="flex justify-end gap-2">
                    <flux:button type="submit" variant="primary">Veröffentlichen</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
