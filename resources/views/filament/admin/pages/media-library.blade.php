<x-filament-panels::page>
    <x-filament::section>
        {{-- Toolbar --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <x-filament::input.wrapper class="w-full sm:max-w-xs">
                <x-filament::input
                    type="text"
                    wire:model.live="search"
                    placeholder="Buscar por nombre..."
                />
            </x-filament::input.wrapper>

            <button
                type="button"
                x-data
                x-on:click="
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/*';
                    input.onchange = async (e) => {
                        const file = e.target.files[0];
                        if (!file) return;
                        const fd = new FormData();
                        fd.append('image', file);
                        fd.append('block_id', 0);
                        fd.append('path', 'temp');
                        fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                        const res = await fetch('/edit/api/image', { method: 'POST', body: fd });
                        if (res.ok) {
                            $wire.dispatch('refresh');
                            $wire.$refresh();
                        }
                    };
                    input.click();
                "
                class="fi-btn fi-btn-color-primary fi-btn-size-md fi-btn-outlined inline-flex items-center gap-1.5 rounded-lg border border-primary-600 px-3 py-2 text-sm font-semibold text-primary-600 shadow-sm hover:bg-primary-50 focus:outline-none"
            >
                <x-heroicon-o-arrow-up-tray class="h-4 w-4" />
                Subir archivo
            </button>
        </div>

        {{-- Grid --}}
        @if ($this->media->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <x-heroicon-o-photo class="h-12 w-12 mb-3" />
                <p class="text-sm">Sin archivos. Sube uno para empezar.</p>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($this->media as $item)
                    <div
                        class="group relative flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
                        x-data="{ copied: false }"
                    >
                        {{-- Thumbnail --}}
                        <div class="flex h-28 items-center justify-center bg-gray-50 dark:bg-gray-800">
                            @if ($item->isImage())
                                <img
                                    src="{{ $item->url }}"
                                    alt="{{ $item->alt ?? $item->original_name }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                            @else
                                <x-heroicon-o-document class="h-12 w-12 text-gray-400" />
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-col gap-1 p-2">
                            <p class="truncate text-xs font-medium text-gray-700 dark:text-gray-200" title="{{ $item->original_name }}">
                                {{ $item->original_name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $item->human_size }}</p>

                            {{-- Actions --}}
                            <div class="mt-1 flex items-center gap-1">
                                {{-- Copy URL --}}
                                <button
                                    type="button"
                                    x-on:click="
                                        navigator.clipboard.writeText('{{ $item->url }}');
                                        copied = true;
                                        setTimeout(() => copied = false, 1500);
                                    "
                                    class="flex-1 rounded-md border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                    title="Copiar URL"
                                >
                                    <span x-show="!copied">Copiar URL</span>
                                    <span x-show="copied" x-cloak class="text-green-600">Copiado!</span>
                                </button>

                                {{-- Delete --}}
                                <button
                                    type="button"
                                    wire:click="deleteMedia({{ $item->id }})"
                                    wire:confirm="¿Eliminar este archivo? Esta acción no se puede deshacer."
                                    class="rounded-md border border-red-200 p-1 text-red-500 hover:bg-red-50 dark:border-red-800 dark:hover:bg-red-950"
                                    title="Eliminar"
                                >
                                    <x-heroicon-o-trash class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $this->media->links() }}
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
