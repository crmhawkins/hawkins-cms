<x-filament-panels::page>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" defer></script>

    <script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('mediaUploader', () => ({
            dragging: false,
            uploading: false,
            handleFiles(files) {
                if (!files || !files.length) return;
                this.uploading = true;
                const wire = this.$wire;
                wire.uploadMultiple('uploads', Array.from(files),
                    () => { this.uploading = false; this.$refs.fileInput.value = ''; wire.saveUploads(); },
                    () => { this.uploading = false; },
                    () => {}
                );
            }
        }));

        Alpine.data('mediaGrid', () => ({
            selected: [],
            toggle(id) {
                const idx = this.selected.indexOf(id);
                if (idx === -1) this.selected.push(id);
                else this.selected.splice(idx, 1);
            },
            isSelected(id) { return this.selected.includes(id); },
            selectAll(ids) { this.selected = [...ids]; },
            clearAll() { this.selected = []; },
            async deleteSelected() {
                if (!this.selected.length) return;
                if (!confirm(`¿Eliminar ${this.selected.length} archivo(s)? No se puede deshacer.`)) return;
                await this.$wire.deleteSelected(this.selected);
                this.selected = [];
            }
        }));

        Alpine.data('mediaModal', () => ({
            cropper: null,
            cropping: false,
            urlCopied: false,

            initCropper() {
                if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
                this.$nextTick(() => {
                    const img = this.$refs.cropImg;
                    if (!img) return;
                    this.cropper = new Cropper(img, { viewMode: 1, autoCropArea: 1 });
                    this.cropping = true;
                });
            },

            cancelCrop() {
                if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
                this.cropping = false;
            },

            async applyCrop() {
                if (!this.cropper) return;
                const d = this.cropper.getData(true);
                this.cancelCrop();
                await this.$wire.saveCrop(d.x, d.y, d.width, d.height);
            },

            copyUrl(url) {
                navigator.clipboard.writeText(url).then(() => {
                    this.urlCopied = true;
                    setTimeout(() => { this.urlCopied = false; }, 2000);
                });
            },

            close() {
                this.cancelCrop();
                this.$wire.set('editingMediaId', null);
            }
        }));

    });
    </script>

    {{-- Upload zone --}}
    <div class="mb-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-6 text-center"
         x-data="mediaUploader()"
         x-on:dragover.prevent="dragging = true"
         x-on:dragleave.prevent="dragging = false"
         x-on:drop.prevent="dragging = false; handleFiles($event.dataTransfer.files)"
         :class="dragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10' : ''">
        <div class="flex flex-col items-center gap-3">
            <x-heroicon-o-arrow-up-tray class="h-10 w-10 text-gray-400" />
            <p class="text-sm text-gray-600 dark:text-gray-400">Arrastra archivos aquí o</p>
            <input type="file" x-ref="fileInput" multiple accept="image/*,video/*,application/pdf" style="display:none" x-on:change="handleFiles($event.target.files)">
            <button type="button" x-on:click="$refs.fileInput.click()" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors cursor-pointer">Seleccionar archivos</button>
        </div>
        <div x-show="uploading" x-cloak class="mt-4"><p class="text-xs text-gray-500">Subiendo archivos...</p></div>
    </div>

    {{-- Toolbar --}}
    <div x-data="mediaGrid()" class="space-y-3">

        <div class="flex flex-wrap items-center gap-3">
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar..."
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white flex-1 min-w-40">
            <select wire:model.live="filterType" class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <option value="">Todos</option>
                <option value="image">Imágenes</option>
                <option value="video">Vídeos</option>
                <option value="document">Documentos</option>
            </select>
            <span class="text-sm text-gray-500">{{ $this->media->total() }} archivo(s)</span>
        </div>

        {{-- Bulk action bar --}}
        <div x-show="selected.length > 0" x-cloak
             class="flex items-center gap-3 rounded-lg bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 px-4 py-2">
            <span class="text-sm font-medium text-primary-700 dark:text-primary-300" x-text="selected.length + ' seleccionado(s)'"></span>
            <button type="button" x-on:click="clearAll()" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">Deseleccionar todo</button>
            <button type="button" x-on:click="selectAll([{{ $this->media->pluck('id')->join(',') }}])"
                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">Seleccionar página</button>
            <div class="flex-1"></div>
            <button type="button" x-on:click="deleteSelected()"
                    class="rounded-lg bg-red-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-600 transition-colors">
                Eliminar seleccionados
            </button>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-6 gap-2">
            @foreach($this->media as $file)
                @php $fileId = $file->id; @endphp
                <div class="relative group rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 aspect-square cursor-pointer"
                     :class="isSelected({{ $fileId }}) ? 'ring-2 ring-primary-500' : 'hover:ring-2 hover:ring-gray-300 dark:hover:ring-gray-600'">

                    {{-- Thumbnail --}}
                    <div class="w-full h-full" x-on:click="$wire.startEdit({{ $fileId }})">
                        @if(str_starts_with($file->mime_type, 'image/'))
                            <img src="{{ asset('storage/'.$file->directory.'/'.$file->filename) }}"
                                 alt="{{ $file->alt ?: $file->original_name }}"
                                 class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex flex-col items-center justify-center gap-1 p-2">
                                <x-heroicon-o-document class="h-8 w-8 text-gray-400" />
                                <p class="text-xs text-gray-400 truncate w-full text-center">{{ pathinfo($file->original_name, PATHINFO_EXTENSION) }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Checkbox (top-left, always visible when selected, on hover otherwise) --}}
                    <div class="absolute top-1 left-1"
                         :class="isSelected({{ $fileId }}) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
                         x-on:click.stop="toggle({{ $fileId }})">
                        <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
                             :class="isSelected({{ $fileId }}) ? 'bg-primary-600 border-primary-600' : 'bg-white/80 border-gray-400'">
                            <svg x-show="isSelected({{ $fileId }})" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Alt indicator --}}
                    @if($file->alt)
                        <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 rounded-full opacity-80" title="Tiene alt text"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($editingMediaId && $this->editingFile)
        @php $file = $this->editingFile; $fileUrl = asset('storage/'.$file->directory.'/'.$file->filename); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" x-data="mediaModal()">
            <div class="absolute inset-0" x-on:click="close()"></div>
            <div class="relative w-full max-w-3xl rounded-2xl bg-white dark:bg-gray-800 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate pr-4">{{ $file->original_name }}</h3>
                    <button x-on:click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex flex-1 overflow-hidden min-h-0">

                    {{-- Left preview panel --}}
                    <div class="w-64 shrink-0 flex flex-col bg-gray-50 dark:bg-gray-900 p-4 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
                        @if($file->isImage())
                            <div class="w-full rounded-lg overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 mb-3" x-show="!cropping">
                                <img src="{{ $fileUrl }}" alt="{{ $file->alt }}" class="w-full object-contain max-h-48">
                            </div>
                            <div class="w-full mb-3" x-show="cropping" x-cloak>
                                <img x-ref="cropImg" src="{{ $fileUrl }}" alt="" class="max-w-full block">
                            </div>
                        @else
                            <div class="w-full h-32 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-lg mb-3">
                                <x-heroicon-o-document class="h-16 w-16 text-gray-400" />
                            </div>
                        @endif

                        <div class="w-full text-xs text-gray-500 space-y-1.5" x-show="!cropping">
                            <p><span class="font-medium text-gray-700 dark:text-gray-300">Tipo:</span> {{ $file->mime_type }}</p>
                            <p><span class="font-medium text-gray-700 dark:text-gray-300">Peso:</span> {{ $file->human_size }}</p>
                            <p><span class="font-medium text-gray-700 dark:text-gray-300">Subido:</span> {{ $file->created_at->format('d/m/Y H:i') }}</p>
                            @if($file->isImage())
                                @php
                                    try {
                                        $imgPath = Storage::disk($file->disk)->path($file->directory.'/'.$file->filename);
                                        [$imgW, $imgH] = @getimagesize($imgPath) ?: [null, null];
                                    } catch(\Throwable $e) { $imgW = $imgH = null; }
                                @endphp
                                @if($imgW && $imgH)
                                    <p><span class="font-medium text-gray-700 dark:text-gray-300">Dimensiones:</span> {{ $imgW }}×{{ $imgH }} px</p>
                                @endif
                            @endif
                        </div>

                        @if($file->isImage())
                            <div class="mt-4" x-show="!cropping">
                                <button type="button" x-on:click="initCropper()"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 justify-center">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0H3m4 0h10m4 0h-4m0 0v12m0 4h4m-4 0H7m0 0v4"/></svg>
                                    Recortar y duplicar
                                </button>
                            </div>
                            <div class="mt-4 space-y-2" x-show="cropping" x-cloak>
                                <button type="button" x-on:click="applyCrop()" class="w-full rounded-lg bg-primary-600 px-3 py-2 text-xs font-medium text-white hover:bg-primary-700">✓ Aplicar recorte</button>
                                <button type="button" x-on:click="cancelCrop()" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Cancelar recorte</button>
                            </div>
                        @endif
                    </div>

                    {{-- Right edit panel --}}
                    <div class="flex-1 flex flex-col overflow-y-auto p-6 space-y-5">

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">URL del archivo</label>
                            <div class="flex gap-2">
                                <input type="text" readonly value="{{ $fileUrl }}"
                                       onclick="this.select()"
                                       class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-3 py-2 text-xs text-gray-600 dark:text-gray-400 cursor-text">
                                <button type="button" x-on:click="copyUrl('{{ $fileUrl }}')"
                                        class="shrink-0 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-w-16 text-center">
                                    <span x-show="!urlCopied">Copiar</span>
                                    <span x-show="urlCopied" x-cloak class="text-green-600 font-semibold">✓</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nombre del archivo</label>
                            <input type="text" wire:model="editingName"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                            <p class="mt-1 text-xs text-gray-400">Solo el nombre de visualización. El archivo físico (UUID) no cambia.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Texto alternativo (Alt)</label>
                            <input type="text" wire:model="editingAlt"
                                   placeholder="Describe la imagen para accesibilidad y SEO"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                            <p class="mt-1 text-xs text-gray-400">Importante para SEO y accesibilidad (WCAG).</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Ruta en disco</label>
                            <input type="text" readonly value="{{ $file->directory }}/{{ $file->filename }}"
                                   class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2 text-xs text-gray-400 cursor-default">
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <button wire:click="deleteMedia({{ $file->id }})"
                            wire:confirm="¿Eliminar '{{ addslashes($file->original_name) }}'? No se puede deshacer."
                            class="rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600">
                        Eliminar
                    </button>
                    <div class="flex gap-3">
                        <button type="button" x-on:click="close()"
                                class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Cancelar
                        </button>
                        <button wire:click="saveEdit()"
                                class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                            Guardar cambios
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-6">{{ $this->media->links() }}</div>
</x-filament-panels::page>
