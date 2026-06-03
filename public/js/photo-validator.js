/**
 * EduCard — Validación de foto de carnet
 * Valida: formato, tamaño y dimensiones mínimas antes del envío
 */
(function () {
    'use strict';

    const CONFIG = {
        maxSizeBytes:  2 * 1024 * 1024,   // 2 MB
        maxSizeMb:     2,
        allowedTypes:  ['image/jpeg', 'image/jpg', 'image/png'],
        minWidth:      500,                // px mínimo ancho (foto 4x4)
        minHeight:     500,                // px mínimo alto  (foto 4x4)
        maxWidth:      6000,
        maxHeight:     6000,
    };

    const ICONS = {
        check: '<svg width="18" height="18" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>',
        cross: '<svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>',
        warn:  '<svg width="18" height="18" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
        spin:  '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>'
    };

    /**
     * Inicializa la validación en un input[type=file].
     * @param {string} inputId      – ID del input file
     * @param {string} thumbId      – ID del <img> de preview
     * @param {string} placeholderId – ID del div placeholder
     * @param {string} filenameId   – ID del span/div de nombre de archivo
     * @param {string} errorId      – ID del div de errores
     * @param {string} wrapId       – ID de la zona de drop (borde cambia)
     * @param {string} formId       – ID del <form> para bloquear submit
     */
    window.initPhotoValidator = function (inputId, thumbId, placeholderId, filenameId, errorId, wrapId, formId) {
        const input       = document.getElementById(inputId);
        const thumb       = document.getElementById(thumbId);
        const placeholder = document.getElementById(placeholderId);
        const filename    = document.getElementById(filenameId);
        const errorBox    = document.getElementById(errorId);
        const wrap        = document.getElementById(wrapId);
        const form        = document.getElementById(formId);

        if (!input) return;

        // Estado de validez de la foto
        let photoValid = false;

        // Bloquear submit si foto inválida
        if (form) {
            form.addEventListener('submit', function (e) {
                if (input.files.length > 0 && !photoValid) {
                    e.preventDefault();
                    errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }

        input.addEventListener('change', function () {
            const file = this.files[0];
            clearError();
            photoValid = false;

            if (!file) {
                resetPreview();
                return;
            }

            // Muestra la checklist
            const checklist = document.getElementById('photo-checklist');
            if (checklist) checklist.style.display = 'block';

            // Resetear iconos de validaciones JS
            const iconSize = document.querySelector('#check-size .icon');
            const iconRatio = document.querySelector('#check-ratio .icon');
            if(iconSize) iconSize.innerHTML = ICONS.spin;
            if(iconRatio) iconRatio.innerHTML = ICONS.spin;

            // ── 1. Validar formato ──────────────────────────────────────────
            if (!CONFIG.allowedTypes.includes(file.type)) {
                showError('⚠ Formato no válido. Solo se aceptan imágenes JPG o PNG.');
                resetPreview();
                return;
            }

            // ── 2. Validar peso ─────────────────────────────────────────────
            if (file.size > CONFIG.maxSizeBytes) {
                if(iconSize) iconSize.innerHTML = ICONS.cross;
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                showError('⚠ La imagen pesa ' + sizeMb + ' MB y supera el límite de ' + CONFIG.maxSizeMb + ' MB. Reduce el tamaño de la imagen e intenta de nuevo.');
                resetPreview(false); // don't hide checklist
                return;
            }
            if(iconSize) iconSize.innerHTML = ICONS.check;

            // ── 3. Validar dimensiones con FileReader + Image ───────────────
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = new Image();
                img.onload = function () {
                    const w = img.naturalWidth;
                    const h = img.naturalHeight;

                    if (w < CONFIG.minWidth || h < CONFIG.minHeight) {
                        if(iconRatio) iconRatio.innerHTML = ICONS.cross;
                        showError(
                            '⚠ Resolución insuficiente (' + w + '×' + h + ' px). ' +
                            'La foto 4×4 debe tener al menos ' + CONFIG.minWidth + '×' + CONFIG.minHeight + ' px. ' +
                            'Usa una foto con mayor resolución.'
                        );
                        resetPreview(false);
                        return;
                    }

                    if (w > CONFIG.maxWidth || h > CONFIG.maxHeight) {
                        if(iconRatio) iconRatio.innerHTML = ICONS.cross;
                        showError(
                            '⚠ La imagen es demasiado grande (' + w + '×' + h + ' px). ' +
                            'El máximo permitido es ' + CONFIG.maxWidth + '×' + CONFIG.maxHeight + ' px.'
                        );
                        resetPreview(false);
                        return;
                    }
                    
                    // Check strict 1:1 ratio with 5% tolerance
                    const ratio = w / h;
                    if (ratio < 0.95 || ratio > 1.05) {
                        if(iconRatio) iconRatio.innerHTML = ICONS.cross;
                        showError('⚠ La foto debe ser cuadrada (Relación 1:1). Tu imagen tiene dimensiones de ' + w + '×' + h + ' px.');
                        resetPreview(false);
                        return;
                    }
                    if(iconRatio) iconRatio.innerHTML = ICONS.check;

                    // ✅ Todo válido (local) — mostrar preview y arrancar Validación Avanzada
                    photoValid = true;
                    clearError();
                    showPreview(e.target.result, file.name, w, h, file.size);

                    // --- INICIO DE VALIDACIÓN AVANZADA (API) ---
                    const iconBg = document.querySelector('#check-bg .icon');
                    const iconSuit = document.querySelector('#check-suit .icon');
                    const iconHair = document.querySelector('#check-hair .icon');
                    
                    if(iconBg) iconBg.innerHTML = ICONS.spin;
                    if(iconSuit) iconSuit.innerHTML = ICONS.spin;
                    if(iconHair) iconHair.innerHTML = ICONS.spin;

                    const formData = new FormData();
                    formData.append('photo', file);

                    fetch('/api/validate-photo-advanced', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.details) {
                            if(iconBg) iconBg.innerHTML = data.details.red_background ? ICONS.check : ICONS.cross;
                            if(iconSuit) iconSuit.innerHTML = data.details.formal_attire_heuristic ? ICONS.check : ICONS.cross;
                            if(iconHair) iconHair.innerHTML = data.details.hair_heuristic ? ICONS.check : ICONS.cross;
                        } else {
                            if(iconBg) iconBg.innerHTML = ICONS.warn;
                            if(iconSuit) iconSuit.innerHTML = ICONS.warn;
                            if(iconHair) iconHair.innerHTML = ICONS.warn;
                            console.error('Error en validación avanzada:', data.message);
                        }
                    })
                    .catch(error => {
                        if(iconBg) iconBg.innerHTML = ICONS.warn;
                        if(iconSuit) iconSuit.innerHTML = ICONS.warn;
                        if(iconHair) iconHair.innerHTML = ICONS.warn;
                        console.error('Error de red al validar:', error);
                    });
                    // --- FIN DE VALIDACIÓN AVANZADA ---
                    
                };
                img.onerror = function () {
                    showError('⚠ No se pudo leer la imagen. Asegúrate de que el archivo no esté dañado.');
                    resetPreview();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        // ── Funciones auxiliares ────────────────────────────────────────────
        function showError(msg) {
            if (!errorBox) return;
            errorBox.textContent = msg;
            errorBox.style.display = 'flex';
            if (wrap) {
                wrap.style.borderColor = 'var(--danger)';
                wrap.style.borderStyle = 'solid';
            }
        }

        function clearError() {
            if (!errorBox) return;
            errorBox.textContent = '';
            errorBox.style.display = 'none';
            if (wrap) {
                wrap.style.borderColor = 'var(--border)';
                wrap.style.borderStyle = 'dashed';
            }
        }

        function showPreview(src, name, w, h, size) {
            if (thumb) {
                thumb.src = src;
                thumb.style.display = 'block';
            }
            if (placeholder) placeholder.style.display = 'none';
            if (filename) {
                const sizeMb = (size / (1024 * 1024)).toFixed(2);
                filename.innerHTML =
                    '<strong>' + escHtml(name) + '</strong>' +
                    '<span style="margin-left:8px;color:var(--text-dim);font-weight:400">' + w + '×' + h + ' px · ' + sizeMb + ' MB</span>';
                filename.style.color = 'var(--success)';
            }
            if (wrap) {
                wrap.style.borderColor = 'var(--success)';
                wrap.style.borderStyle = 'solid';
            }
        }

        function resetPreview(hideChecklist = true) {
            if (thumb) { thumb.src = ''; thumb.style.display = 'none'; }
            if (placeholder) placeholder.style.display = 'flex';
            if (filename) { filename.textContent = 'Ningún archivo seleccionado'; filename.style.color = 'var(--text-muted)'; }
            if (wrap) { wrap.style.borderColor = 'var(--border)'; wrap.style.borderStyle = 'dashed'; }
            if (hideChecklist) {
                const checklist = document.getElementById('photo-checklist');
                if (checklist) checklist.style.display = 'none';
            }
        }

        function escHtml(str) {
            return str.replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }
    };
})();
