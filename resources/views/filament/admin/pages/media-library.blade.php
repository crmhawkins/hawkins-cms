<x-filament-panels::page>
    {{-- Upload zone --}}
    <div class="mb-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-6 text-center"
         x-data="{ dragging: false }"
         x-on:dragover.prevent="dragging = true"
         x-on:dragleave="dragging = false"
         x-on:drop.prevent="dragging = false"
         :class="dragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10' : ''">
        <form wire:submit.prevent="uploadFiles">
            <div class="flex flex-col items-center gap-3">
                <x-heroicon-o-arrow-up-tray class="h-10 w-10 text-gray-400" />
                <p class="text-sm text-gray-600 dark:text-gray-400">Arrastra archivos aquí o</p>
                <label class="cursor-pointer rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                    Seleccionar archivos
                    <input type="file" wire:model="uploads" multiple accept="image/*,video/*,application/pdf" class="sr-only">
                </label>
                @error('uploads.*') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
            @if(count($uploads ?? []) > 0)
                <div class="mt-4 flex items-center justify-center gap-3">
                    <span class="text-sm text-gray-600">{{ count($uploads) }} archivo(s) seleccionado(s)</span>
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                        Subir
                    </button>
                </div>
            @endif
        </form>
    </div>

    {{-- Filters --}}
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Buscar por nombre..."
               class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white flex-1 min-w-48">
        <select wire:model.live="filterType" class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            <option value="">Todos los archivos</option>
            <option value="image">Imágenes</option>
            <option value="video">Vídeos</option>
            <option value="document">Documentos</option>
        </select>
        <span class="text-sm text-gray-500">{{ $this->media->total() }} archivo(s)</span>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
        @foreach($this->media as $file)
            <div class="group relative rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                {{-- Preview --}}
                @if(str_starts_with($file->mime_type, 'image/'))
                    <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img src="{{ asset('storage/'.$file->directory.'/'.$file->filename) }}"
                             alt="{{ $file->alt ?: $file->original_name }}"
                             class="h-full w-full object-cover">
                    </div>
                @else
                    <div class="aspect-square flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                        <x-heroicon-o-document class="h-12 w-12 text-gray-400" />
                    </div>
                @endif

                {{-- Overlay actions --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity p-2">
                    <button
                        onclick="navigator.clipboard.writeText('{{ asset('storage/'.$file->directory.'/'.$file->filename) }}').then(()=>{ let t=this; t.textContent='✓ Copiado'; setTimeout(()=>{t.textContent='Copiar URL'},1500); })"
                        class="w-full rounded bg-white/90 px-2 py-1 text-xs font-medium text-gray-800 hover:bg-white">
                        Copiar URL
                    </button>
                    <button wire:click="startEditAlt({{ $file->id }})"
                            class="w-full rounded bg-blue-500/90 px-2 py-1 text-xs font-medium text-white hover:bg-blue-600">
                        Editar alt
                    </button>
                    <button wire:click="deleteMedia({{ $file->id }})"
                            wire:confirm="¿Eliminar este archivo? No se puede deshacer."
                            class="w-full rounded bg-red-500/90 px-2 py-1 text-xs font-medium text-white hover:bg-red-600">
                        Eliminar
                    </button>
                </div>

                {{-- Info --}}
                <div class="p-2">
                    <p class="truncate text-xs text-gray-600 dark:text-gray-400" title="{{ $file->original_name }}">{{ $file->original_name }}</p>
                    <p class="text-xs text-gray-400">{{ $file->human_size }}</p>
                    @if($file->alt)
                        <p class="truncate text-xs text-green-600" title="Alt: {{ $file->alt }}">Alt: {{ $file->alt }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Edit Alt Modal --}}
    @if($editingMediaId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Editar texto alternativo</h3>
                <input type="text" wire:model="editingAlt"
                       placeholder="Describe la imagen para accesibilidad y SEO"
                       class="mb-4 w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                <div class="flex gap-3">
                    <button wire:click="saveAlt" class="flex-1 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Guardar</button>
                    <button wire:click="$set('editingMediaId', null)" class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cancelar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $this->media->links() }}
    </div>
</x-filament-panels::page>
