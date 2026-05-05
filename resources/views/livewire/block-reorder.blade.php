<div
    x-data="{
        blocks: @js($blocks),
        dragging: null,
        dragOver: null,
        startDrag(index) {
            this.dragging = index;
        },
        onDragOver(index) {
            if (this.dragging === null || this.dragging === index) return;
            const moved = this.blocks.splice(this.dragging, 1)[0];
            this.blocks.splice(index, 0, moved);
            this.dragging = index;
        },
        endDrag() {
            this.dragging = null;
            $wire.reorder(this.blocks.map(b => b.id));
        }
    }"
    style="max-width:600px;"
>
    <template x-for="(block, index) in blocks" :key="block.id">
        <div
            draggable="true"
            @dragstart="startDrag(index)"
            @dragover.prevent="onDragOver(index)"
            @dragend="endDrag()"
            :style="dragging === index ? 'opacity:.4;' : ''"
            style="display:flex;align-items:center;gap:1rem;padding:.75rem 1rem;margin-bottom:.5rem;background:#f9f9f9;border:1px solid #ddd;border-radius:6px;cursor:grab;"
        >
            <span style="color:#aaa;font-size:1.2rem;">&#9776;</span>
            <span x-text="block.type" style="font-weight:500;text-transform:capitalize;"></span>
            <span x-text="'orden: ' + block.sort" style="margin-left:auto;color:#999;font-size:.8rem;"></span>
        </div>
    </template>

    @if(empty($blocks))
        <p style="color:#999;">No hay bloques en esta página.</p>
    @endif
</div>
