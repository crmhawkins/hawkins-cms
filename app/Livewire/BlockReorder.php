<?php

namespace App\Livewire;

use App\Models\Block;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class BlockReorder extends Component
{
    public int $pageId;
    public array $blocks = [];

    public function mount(int $pageId): void
    {
        $this->pageId = $pageId;
        $this->refreshBlocks();
    }

    public function reorder(array $order): void
    {
        Gate::authorize('edit-content');

        foreach ($order as $index => $blockId) {
            Block::where('id', $blockId)
                ->where('page_id', $this->pageId)
                ->update(['sort' => $index]);
        }

        $this->refreshBlocks();
    }

    private function refreshBlocks(): void
    {
        $this->blocks = Block::where('page_id', $this->pageId)
            ->orderBy('sort')
            ->get(['id', 'type', 'sort'])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.block-reorder');
    }
}
