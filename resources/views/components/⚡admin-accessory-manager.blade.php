<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;
use App\Models\CatalogAccessory;

new #[Layout('components.layouts.admin')] class extends Component {
    public $groups = [];
    public $activeGroup = null;

    // Group Form State
    #[Validate('required|string|max:255', as: 'group title')]
    public $groupTitle = '';

    // Term Form State
    #[Validate('required|string|max:255', as: 'term title')]
    public $termTitle = '';
    public $termDescription = '';

    public function mount()
    {
        $this->loadGroups();
        if (count($this->groups) > 0) {
            $this->selectGroup($this->groups[0]->id);
        }
    }

    public function loadGroups()
    {
        $this->groups = CatalogAccessory::where('parent_id', 0)->orderBy('title')->get();
    }

    public function selectGroup($id)
    {
        $this->activeGroup = CatalogAccessory::with('children')->find($id);
    }

    public function createGroup()
    {
        $this->validateOnly('groupTitle');

        $group = CatalogAccessory::create([
            'title' => $this->groupTitle,
            'parent_id' => 0,
            'url_slug' => Str::slug($this->groupTitle),
        ]);

        $this->groupTitle = '';
        $this->loadGroups();
        $this->selectGroup($group->id);
    }

    public function createTerm()
    {
        $this->validateOnly('termTitle');

        if (!$this->activeGroup) {
            return;
        }

        CatalogAccessory::create([
            'title' => $this->termTitle,
            'parent_id' => $this->activeGroup->id,
            // Append short ID to prevent slug collisions for common terms across groups
            'url_slug' => Str::slug($this->termTitle) . '-' . substr(uniqid(), -4),
            'description' => $this->termDescription,
        ]);

        $this->termTitle = '';
        $this->termDescription = '';
        $this->selectGroup($this->activeGroup->id);
    }

    public function deleteAccessory($id)
    {
        // Deleting a parent automatically cascades to children due to logic or DB setup.
        // We will manually delete children here for safety if cascade isn't on the DB layer.
        CatalogAccessory::where('parent_id', $id)->delete();
        CatalogAccessory::destroy($id);

        $this->loadGroups();

        if ($this->activeGroup && $this->activeGroup->id == $id) {
            $this->activeGroup = null;
        } elseif ($this->activeGroup) {
            $this->selectGroup($this->activeGroup->id);
        }
    }
}; ?>

<div class="h-[calc(100vh-8rem)] flex flex-col">

    <div class="mb-6 flex-shrink-0">
        <h1 class="text-2xl font-light text-gray-900">Taxonomy & Accessories</h1>
        <p class="mt-2 text-sm text-gray-600">Manage classification groups (e.g., Artists, Mediums) and their specific
            terms.</p>
    </div>

    <div class="flex-1 flex flex-col md:flex-row gap-8 min-h-0">

        <!-- Left Column: Groups -->
        <div class="w-full md:w-1/3 flex flex-col bg-white border border-gray-200 shadow-sm rounded-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                <h2 class="text-xs font-medium tracking-[0.1em] uppercase text-gray-900">Accessory Groups</h2>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-1">
                @foreach ($groups as $group)
                    <div class="flex items-center justify-between group cursor-pointer {{ $activeGroup && $activeGroup->id === $group->id ? 'bg-gray-100' : 'hover:bg-gray-50' }} p-2 rounded-sm transition-colors"
                        wire:click="selectGroup({{ $group->id }})">
                        <span class="text-sm font-medium text-gray-900">{{ $group->title }}</span>
                        <button wire:click.stop="deleteAccessory({{ $group->id }})"
                            class="text-gray-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                <form wire:submit.prevent="createGroup" class="flex gap-2">
                    <input wire:model="groupTitle" type="text" placeholder="New Group Name"
                        class="flex-1 text-sm border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900 py-2">
                    <button type="submit"
                        class="bg-gray-900 text-white px-3 py-2 text-xs font-medium tracking-[0.1em] uppercase hover:bg-black transition-colors">Add</button>
                </form>
                @error('groupTitle')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Right Column: Terms for Active Group -->
        <div class="w-full md:w-2/3 flex flex-col bg-white border border-gray-200 shadow-sm rounded-sm overflow-hidden">
            @if ($activeGroup)
                <div class="p-4 border-b border-gray-200 bg-gray-50 flex-shrink-0 flex justify-between items-center">
                    <h2 class="text-xs font-medium tracking-[0.1em] uppercase text-gray-900">Terms in:
                        {{ $activeGroup->title }}</h2>
                    <span class="text-xs text-gray-500">{{ $activeGroup->children->count() }} items</span>
                </div>

                <div class="flex-1 overflow-y-auto p-4">
                    <ul class="divide-y divide-gray-100">
                        @forelse($activeGroup->children as $term)
                            <li class="py-4 flex justify-between items-start group">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $term->title }}</p>
                                    @if ($term->description)
                                        <p class="text-xs text-gray-500 mt-1 max-w-lg">{{ $term->description }}</p>
                                    @endif
                                </div>
                                <button wire:click="deleteAccessory({{ $term->id }})"
                                    class="text-xs text-red-600 hover:text-red-900 opacity-0 group-hover:opacity-100 transition-opacity font-medium tracking-wide uppercase">Delete</button>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm text-gray-500">No terms have been added to this group
                                yet.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="p-6 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    <h3 class="text-xs font-medium tracking-[0.1em] uppercase text-gray-900 mb-4">Add New Term</h3>
                    <form wire:submit.prevent="createTerm" class="space-y-4">
                        <div>
                            <input wire:model="termTitle" type="text"
                                placeholder="Term Title (e.g., Oil, Marcus Chen)"
                                class="w-full text-sm border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900 py-2">
                            @error('termTitle')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <textarea wire:model="termDescription" rows="2" placeholder="Optional description or bio..."
                                class="w-full text-sm border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900 py-2"></textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit"
                                class="bg-gray-900 text-white px-4 py-2 text-xs font-medium tracking-[0.1em] uppercase hover:bg-black transition-colors">Save
                                Term</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-sm text-gray-500 p-8">
                    Select a group from the sidebar to view or manage its terms.
                </div>
            @endif
        </div>

    </div>
</div>
