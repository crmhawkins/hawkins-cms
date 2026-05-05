// Hawkins CMS — Inline Editor Overlay
// Activates when body has [data-editor] attribute

(function () {
    'use strict';

    if (!document.body.dataset.editor) return;

    // Mobile guard
    if (window.innerWidth < 1024) {
        const banner = document.createElement('div');
        banner.className = 'hawkins-editor-mobile-warning';
        banner.textContent = 'El editor requiere pantalla de escritorio (≥1024px).';
        Object.assign(banner.style, {
            position: 'fixed', top: '0', left: '0', right: '0',
            background: '#f59e0b', color: '#fff', textAlign: 'center',
            padding: '12px', zIndex: '99999', fontFamily: 'sans-serif'
        });
        document.body.prepend(banner);
        return;
    }

    let saveTimeout = null;

    // ── TEXT EDITING ──────────────────────────────────────────
    document.querySelectorAll('[data-edit-field]').forEach(function (el) {
        el.style.outline = '2px dashed rgba(245,158,11,0.5)';
        el.style.cursor = 'text';
        el.title = 'Click para editar';

        el.addEventListener('click', function (e) {
            e.stopPropagation();
            if (el.contentEditable === 'true') return;
            el.contentEditable = 'true';
            el.style.outline = '2px solid #f59e0b';
            el.focus();
        });

        el.addEventListener('blur', function () {
            el.contentEditable = 'false';
            el.style.outline = '2px dashed rgba(245,158,11,0.5)';
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(function () {
                saveField(el);
            }, 600);
        });

        el.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                el.blur();
            }
        });
    });

    function saveField(el) {
        const blockId = el.dataset.blockId;
        const path = el.dataset.editField;
        const value = el.innerText || el.textContent;

        fetch('/edit/api/field', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ block_id: blockId, path: path, value: value }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.ok) showToast('Guardado ✓', 'success');
            else showToast('Error al guardar', 'error');
        })
        .catch(function () { showToast('Error de red', 'error'); });
    }

    // ── IMAGE EDITING ─────────────────────────────────────────
    document.querySelectorAll('[data-edit-image]').forEach(function (el) {
        const overlay = document.createElement('button');
        overlay.className = 'hawkins-image-edit-btn';
        overlay.innerHTML = '📷 Cambiar imagen';
        Object.assign(overlay.style, {
            position: 'absolute', top: '50%', left: '50%',
            transform: 'translate(-50%,-50%)',
            background: 'rgba(0,0,0,0.7)', color: '#fff',
            border: 'none', borderRadius: '6px',
            padding: '8px 16px', cursor: 'pointer',
            fontFamily: 'sans-serif', fontSize: '14px',
            opacity: '0', transition: 'opacity 0.2s', zIndex: '10',
        });

        const wrapper = el.closest('[data-edit-image-wrap]') || el.parentElement;
        if (getComputedStyle(wrapper).position === 'static') {
            wrapper.style.position = 'relative';
        }
        wrapper.appendChild(overlay);

        wrapper.addEventListener('mouseenter', function () { overlay.style.opacity = '1'; });
        wrapper.addEventListener('mouseleave', function () { overlay.style.opacity = '0'; });

        overlay.addEventListener('click', function (e) {
            e.preventDefault();
            openImageModal(el);
        });
    });

    function openImageModal(el) {
        const modal = document.createElement('div');
        modal.className = 'hawkins-image-modal';
        Object.assign(modal.style, {
            position: 'fixed', inset: '0', background: 'rgba(0,0,0,0.8)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            zIndex: '99999',
        });

        modal.innerHTML = `
            <div style="background:#fff;border-radius:12px;padding:32px;max-width:480px;width:100%;text-align:center;font-family:sans-serif;">
                <h3 style="margin:0 0 16px;font-size:18px;">Cambiar imagen</h3>
                <div id="hcms-drop" style="border:2px dashed #d1d5db;border-radius:8px;padding:40px;cursor:pointer;color:#6b7280;margin-bottom:16px;">
                    Arrastra una imagen aquí o <strong>haz click para seleccionar</strong>
                </div>
                <input type="file" id="hcms-file" accept="image/*" style="display:none;">
                <div id="hcms-preview" style="display:none;margin-bottom:16px;">
                    <img id="hcms-preview-img" style="max-width:100%;border-radius:8px;max-height:200px;">
                </div>
                <div style="display:flex;gap:8px;justify-content:center;">
                    <button id="hcms-upload-btn" style="background:#f59e0b;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:14px;" disabled>Guardar</button>
                    <button id="hcms-cancel-btn" style="background:#6b7280;color:#fff;border:none;padding:10px 24px;border-radius:6px;cursor:pointer;font-size:14px;">Cancelar</button>
                </div>
                <div id="hcms-progress" style="display:none;margin-top:12px;color:#6b7280;font-size:14px;">Subiendo...</div>
            </div>
        `;

        document.body.appendChild(modal);

        let selectedFile = null;
        const dropZone = document.getElementById('hcms-drop');
        const fileInput = document.getElementById('hcms-file');
        const uploadBtn = document.getElementById('hcms-upload-btn');
        const cancelBtn = document.getElementById('hcms-cancel-btn');
        const preview = document.getElementById('hcms-preview');
        const previewImg = document.getElementById('hcms-preview-img');
        const progress = document.getElementById('hcms-progress');

        dropZone.addEventListener('click', function () { fileInput.click(); });
        dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.style.borderColor = '#f59e0b'; });
        dropZone.addEventListener('dragleave', function () { dropZone.style.borderColor = '#d1d5db'; });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            handleFile(e.dataTransfer.files[0]);
        });

        fileInput.addEventListener('change', function () { handleFile(fileInput.files[0]); });

        function handleFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            selectedFile = file;
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
                dropZone.style.display = 'none';
            };
            reader.readAsDataURL(file);
            uploadBtn.disabled = false;
        }

        uploadBtn.addEventListener('click', function () {
            if (!selectedFile) return;
            const formData = new FormData();
            formData.append('image', selectedFile);
            formData.append('block_id', el.dataset.blockId);
            formData.append('path', el.dataset.editImage);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            uploadBtn.disabled = true;
            progress.style.display = 'block';

            fetch('/edit/api/image', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: formData,
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.ok) {
                    // Swap image on page
                    if (el.tagName === 'IMG') {
                        el.src = data.url;
                    } else {
                        el.style.backgroundImage = "url('" + data.url + "')";
                    }
                    showToast('Imagen actualizada ✓', 'success');
                    document.body.removeChild(modal);
                } else {
                    showToast('Error al subir imagen', 'error');
                    uploadBtn.disabled = false;
                    progress.style.display = 'none';
                }
            })
            .catch(function () {
                showToast('Error de red', 'error');
                uploadBtn.disabled = false;
                progress.style.display = 'none';
            });
        });

        cancelBtn.addEventListener('click', function () { document.body.removeChild(modal); });
        modal.addEventListener('click', function (e) { if (e.target === modal) document.body.removeChild(modal); });
    }

    // ── TOAST ─────────────────────────────────────────────────
    function showToast(message, type) {
        const toast = document.createElement('div');
        Object.assign(toast.style, {
            position: 'fixed', bottom: '24px', right: '24px',
            background: type === 'success' ? '#10b981' : '#ef4444',
            color: '#fff', padding: '12px 20px', borderRadius: '8px',
            fontFamily: 'sans-serif', fontSize: '14px', zIndex: '99999',
            boxShadow: '0 4px 12px rgba(0,0,0,0.2)', transition: 'opacity 0.3s',
        });
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(function () {
            toast.style.opacity = '0';
            setTimeout(function () { document.body.removeChild(toast); }, 300);
        }, 3000);
    }

})();
