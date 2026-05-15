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

        Alpine.data('mediaModal', (ids = [], currentId = null) => ({
            cropper: null,
            cropping: false,
            urlCopied: false,
            ids: ids,
            currentId: currentId,

            get currentIndex() { return this.ids.indexOf(this.currentId); },
            get hasPrev() { return this.currentIndex > 0; },
            get hasNext() { return this.currentIndex < this.ids.length - 1; },

            async prev() {
                if (!this.hasPrev) return;
                this.cancelCrop();
                const id = this.ids[this.currentIndex - 1];
                this.currentId = id;
                await this.$wire.startEdit(id);
            },

            async next() {
                if (!this.hasNext) return;
                this.cancelCrop();
                const id = this.ids[this.currentIndex + 1];
                this.currentId = id;
                await this.$wire.startEdit(id);
            },

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
        <div class="grid gap-2" style="grid-template-columns: repeat(6, minmax(0, 1fr));">
            @foreach($this->media as $file)
                @php $fileId = $file->id; @endphp
                <div class="relative group rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 cursor-pointer" style="aspect-ratio:1/1;"
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

    {{-- Detail Modal — WordPress style --}}
    @if($editingMediaId && $this->editingFile)
        @php
            $file      = $this->editingFile;
            $fileUrl   = asset('storage/'.$file->directory.'/'.$file->filename);
            $mediaIds  = $this->media->pluck('id')->values()->toArray();
            try {
                $imgPath = Storage::disk($file->disk)->path($file->directory.'/'.$file->filename);
                [$imgW, $imgH] = $file->isImage() ? (@getimagesize($imgPath) ?: [null, null]) : [null, null];
            } catch(\Throwable $e) { $imgW = $imgH = null; }
        @endphp
        <div class="fixed inset-0 flex items-center justify-center p-6"
             style="z-index:99999; background:rgba(0,0,0,0.85);"
             x-data="mediaModal({{ json_encode($mediaIds) }}, {{ $file->id }})">
            <div class="absolute inset-0" x-on:click="close()"></div>

            {{-- Prev arrow --}}
            <button type="button" x-show="hasPrev" x-on:click.stop="prev()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/40 text-white transition-colors"
                    style="z-index:100001; width:48px; height:48px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Next arrow --}}
            <button type="button" x-show="hasNext" x-on:click.stop="next()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/40 text-white transition-colors"
                    style="z-index:100001; width:48px; height:48px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Modal container --}}
            <div class="relative w-full rounded-2xl bg-white dark:bg-gray-900 shadow-2xl overflow-hidden flex flex-col"
                 style="max-width:920px; max-height:88vh; z-index:100000;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detalles del archivo</span>
                    <button x-on:click="close()" class="rounded-full p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition-colors">
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>
                </div>

                {{-- Body: left image + right details --}}
                <div class="flex flex-1 overflow-hidden min-h-0">

                    {{-- LEFT — image preview --}}
                    <div class="shrink-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-hidden"
                         style="width:55%; min-height:360px;">
                        @if($file->isImage())
                            <div x-show="!cropping" class="w-full h-full flex items-center justify-center p-4">
                                <img src="{{ $fileUrl }}" alt="{{ $file->alt }}"
                                     style="max-width:100%; max-height:360px; object-fit:contain; display:block;">
                            </div>
                            <div x-show="cropping" x-cloak class="w-full p-3 overflow-auto">
                                <img x-ref="cropImg" src="{{ $fileUrl }}" alt="" style="max-width:100%; display:block;">
                            </div>
                        @else
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <x-heroicon-o-document class="h-20 w-20" />
                                <span class="text-sm font-medium uppercase">{{ pathinfo($file->original_name, PATHINFO_EXTENSION) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- RIGHT — metadata + fields --}}
                    <div class="flex-1 overflow-y-auto flex flex-col">

                        {{-- File metadata (static) --}}
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 space-y-1.5 shrink-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $file->original_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $file->created_at->format('d/m/Y') }} &middot; {{ $file->human_size }}
                                @if($imgW && $imgH) &middot; {{ $imgW }}&times;{{ $imgH }} px @endif
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $file->mime_type }}</p>
                        </div>

                        {{-- Editable fields --}}
                        <div class="px-6 py-5 space-y-6 flex-1">

                            {{-- Nombre --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Nombre</label>
                                <input type="text" wire:model="editingName"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>

                            {{-- Alt --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Texto alternativo</label>
                                <input type="text" wire:model="editingAlt"
                                       placeholder="Describe la imagen (SEO y accesibilidad)"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <p class="mt-1 text-xs text-gray-400">Cómo describir la imagen para lectores de pantalla.</p>
                            </div>

                            {{-- Caption --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Pie de foto</label>
                                <textarea wire:model="editingCaption" rows="2"
                                          placeholder="Pie de foto opcional"
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">Descripción</label>
                                <textarea wire:model="editingDescription" rows="3"
                                          placeholder="Descripción larga del archivo"
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                            </div>

                            {{-- URL --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">URL del archivo</label>
                                <div class="flex gap-2">
                                    <input type="text" readonly value="{{ $fileUrl }}" onclick="this.select()"
                                           class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2 text-xs text-gray-500 dark:text-gray-400 cursor-text focus:outline-none">
                                    <button type="button" x-on:click="copyUrl('{{ $fileUrl }}')"
                                            class="shrink-0 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            style="min-width:70px; text-align:center;">
                                        <span x-show="!urlCopied">Copiar</span>
                                        <span x-show="urlCopied" x-cloak style="color:#16a34a; font-weight:700;">✓ Copiado</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Crop --}}
                            @if($file->isImage())
                                <div x-show="!cropping">
                                    <button type="button" x-on:click="initCropper()"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 justify-center transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0H3m4 0h10m4 0h-4m0 0v12m0 4h4m-4 0H7m0 0v4"/></svg>
                                        Recortar y duplicar
                                    </button>
                                </div>
                                <div class="space-y-2" x-show="cropping" x-cloak>
                                    <button type="button" x-on:click="applyCrop()" class="w-full rounded-lg bg-primary-600 px-3 py-2 text-xs font-medium text-white hover:bg-primary-700">✓ Aplicar recorte</button>
                                    <button type="button" x-on:click="cancelCrop()" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50">Cancelar</button>
                                </div>
                            @endif

                        </div>{{-- /editable fields --}}
                    </div>{{-- /right --}}
                </div>{{-- /body --}}

                {{-- Footer --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shrink-0">
                    <button wire:click="deleteMedia({{ $file->id }})"
                            wire:confirm="¿Eliminar '{{ addslashes($file->original_name) }}'? No se puede deshacer."
                            class="rounded-lg bg-red-500 px-4 py-2 text-xs font-semibold text-white hover:bg-red-600 transition-colors">
                        Eliminar archivo
                    </button>
                    <div class="flex gap-2">
                        <button type="button" x-on:click="close()"
                                class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </button>
                        <button wire:click="saveEdit()"
                                class="rounded-lg bg-primary-600 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-700 transition-colors">
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
