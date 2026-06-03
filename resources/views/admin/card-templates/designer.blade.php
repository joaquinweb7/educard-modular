<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diseñador — {{ $template->name }} · EduCard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        @foreach($fonts as $font)
        @font-face {
            font-family: '{{ $font->name }}';
            src: url('{{ asset('storage/' . $font->file_path) }}');
        }
        @endforeach

        /* ── Reset & Base ─────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:        #0f1117;
            --surface:   #16181f;
            --surface-2: #1e2028;
            --surface-3: #252830;
            --border:    #2e3040;
            --text:      #e8eaf0;
            --text-muted:#8b92a8;
            --text-dim:  #5a6070;
            --primary:   #6366f1;
            --primary-glow: rgba(99,102,241,.18);
            --success:   #22c55e;
            --danger:    #ef4444;
            --warning:   #f59e0b;
            --accent:    #06b6d4;
            --radius:    10px;
            --panel-w:   260px;
        }
        html, body { height: 100%; overflow: hidden; font-family: Inter, sans-serif; background: var(--bg); color: var(--text); font-size: 13px; }

        /* ── Layout ───────────────────────────────────────────── */
        #app { display: flex; flex-direction: column; height: 100vh; }

        /* Topbar */
        #topbar {
            display: flex; align-items: center; gap: 12px;
            padding: 0 16px; height: 52px; flex-shrink: 0;
            background: var(--surface); border-bottom: 1px solid var(--border);
            z-index: 100;
        }
        #topbar .logo { font-family: Outfit, sans-serif; font-weight: 900; font-size: 16px; color: var(--primary); margin-right: 6px; }
        #topbar .sep  { color: var(--border); margin: 0 4px; }
        #topbar .tpl-name { font-weight: 600; font-size: 14px; }
        #topbar .spacer { flex: 1; }
        #topbar .dim-badge {
            font-size: 11px; color: var(--text-dim);
            background: var(--surface-2); border: 1px solid var(--border);
            padding: 3px 10px; border-radius: 20px; font-family: monospace;
        }

        /* Main area */
        #workspace { display: flex; flex: 1; overflow: hidden; }

        /* Left toolbox */
        #toolbox {
            width: var(--panel-w); flex-shrink: 0;
            background: var(--surface); border-right: 1px solid var(--border);
            display: flex; flex-direction: column; overflow-y: auto;
        }
        .panel-label {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .8px; color: var(--text-dim);
            padding: 12px 14px 6px; user-select: none;
        }
        .tool-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; cursor: pointer; border: none;
            background: transparent; color: var(--text);
            font-family: Inter, sans-serif; font-size: 12.5px;
            font-weight: 500; border-radius: 0; width: 100%;
            text-align: left; transition: background .15s;
        }
        .tool-btn:hover { background: var(--surface-2); }
        .tool-btn .icon { font-size: 16px; width: 22px; text-align: center; }
        .tool-divider { height: 1px; background: var(--border); margin: 6px 0; }

        /* Variables quick-pick */
        .var-grid { display: flex; flex-wrap: wrap; gap: 5px; padding: 6px 14px 10px; }
        .var-chip {
            background: var(--surface-3); border: 1px solid var(--border);
            border-radius: 4px; padding: 3px 7px; font-size: 11px;
            font-family: monospace; color: var(--accent);
            cursor: pointer; transition: border-color .15s, color .15s;
            user-select: none;
        }
        .var-chip:hover { border-color: var(--accent); color: #fff; }

        /* Canvas area */
        #canvas-area {
            flex: 1; overflow: auto;
            background: repeating-conic-gradient(var(--surface-2) 0% 25%, var(--bg) 0% 50%) 0 0 / 20px 20px;
            display: flex; align-items: flex-start; justify-content: center;
            padding: 32px;
        }

        /* Card canvas */
        #card-canvas {
            position: relative;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,.7), 0 0 0 1px var(--border);
            border-radius: 12px;
            cursor: default;
            user-select: none;
        }
        #canvas-bg {
            position: absolute; inset: 0; width: 100%; height: 100%;
            object-fit: cover; pointer-events: none;
        }
        #canvas-bg-color {
            position: absolute; inset: 0;
            background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
        }

        /* Canvas elements */
        .cv-element {
            position: absolute;
            cursor: move;
            outline: 1px dashed transparent;
            padding: 2px 4px;
            border-radius: 3px;
            transition: outline-color .12s;
            white-space: nowrap;
            line-height: 1.2;
        }
        .cv-element:hover { outline-color: rgba(99,102,241,.6); }
        .cv-element.selected {
            outline: 2px solid var(--primary) !important;
            background: rgba(99,102,241,.1);
        }
        .cv-element .del-btn {
            display: none;
            position: absolute; top: -10px; right: -10px;
            width: 18px; height: 18px;
            background: var(--danger); border-radius: 50%;
            font-size: 10px; color: #fff; text-align: center;
            line-height: 18px; cursor: pointer; z-index: 10;
        }
        .cv-element.selected .del-btn { display: block; }

        /* Resize handle */
        .cv-element .resize-handle {
            display: none;
            position: absolute; bottom: -5px; right: -5px;
            width: 10px; height: 10px;
            background: var(--primary); border-radius: 2px;
            cursor: se-resize; z-index: 10;
        }
        .cv-element.selected .resize-handle { display: block; }

        /* Right properties panel */
        #props-panel {
            width: var(--panel-w); flex-shrink: 0;
            background: var(--surface); border-left: 1px solid var(--border);
            display: flex; flex-direction: column; overflow-y: auto;
        }
        .prop-group { padding: 10px 14px; border-bottom: 1px solid var(--border); }
        .prop-label { font-size: 10px; text-transform: uppercase; letter-spacing: .6px; color: var(--text-dim); margin-bottom: 7px; font-weight: 600; }
        .prop-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .prop-row label { font-size: 11.5px; color: var(--text-muted); flex-shrink: 0; width: 72px; }
        .prop-input {
            flex: 1; background: var(--surface-3); border: 1px solid var(--border);
            border-radius: 5px; padding: 5px 8px; color: var(--text);
            font-size: 12px; font-family: Inter, sans-serif;
            transition: border-color .15s;
        }
        .prop-input:focus { outline: none; border-color: var(--primary); }
        .prop-input[type=color] { padding: 2px 4px; height: 28px; cursor: pointer; }
        .prop-input[type=number] { width: 60px; }
        textarea.prop-input { resize: vertical; height: 60px; }

        .btn-row { display: flex; gap: 8px; padding: 0 14px 14px; }
        .btn-prop {
            flex: 1; padding: 7px 12px; border-radius: 6px;
            border: none; font-size: 12px; font-weight: 600;
            cursor: pointer; font-family: Inter, sans-serif;
            transition: opacity .15s;
        }
        .btn-prop:hover { opacity: .85; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-danger  { background: var(--danger); color: #fff; }
        .btn-secondary { background: var(--surface-3); color: var(--text); border: 1px solid var(--border); }

        /* No-selection placeholder */
        #no-selection {
            padding: 24px 14px; text-align: center;
            color: var(--text-dim); font-size: 12px; line-height: 1.7;
        }
        #no-selection .icon { font-size: 28px; display: block; margin-bottom: 8px; }

        /* Topbar buttons */
        .tbtn {
            padding: 6px 14px; border-radius: 7px; border: none;
            font-family: Inter, sans-serif; font-size: 12.5px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; gap: 5px;
            transition: opacity .15s;
        }
        .tbtn:hover { opacity: .85; }
        .tbtn-primary { background: var(--primary); color: #fff; }
        .tbtn-secondary { background: var(--surface-3); color: var(--text); border: 1px solid var(--border); }
        .tbtn-success { background: var(--success); color: #fff; }

        /* Zoom controls */
        #zoom-ctrl { display: flex; align-items: center; gap: 6px; }
        #zoom-label { font-size: 11px; color: var(--text-muted); min-width: 36px; text-align: center; }

        /* Toast */
        #toast {
            position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
            background: var(--success); color: #fff;
            padding: 10px 20px; border-radius: 8px;
            font-weight: 600; font-size: 13px;
            opacity: 0; transition: opacity .3s;
            pointer-events: none; z-index: 9999;
        }
        #toast.show { opacity: 1; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--surface-3); border-radius: 3px; }
    </style>
</head>
<body>
<div id="app">

    {{-- ── Topbar ─────────────────────────────────────────────── --}}
    <div id="topbar">
        <span class="logo">⬡</span>
        <a href="{{ route('admin.card-templates.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:13px">Plantillas</a>
        <span class="sep">/</span>
        <span class="tpl-name" id="tpl-name-label">{{ $template->name }}</span>
        <span class="dim-badge" id="dim-badge">{{ round($template->width / 37.795276, 2) }} × {{ round($template->height / 37.795276, 2) }} cm</span>
        <div class="spacer"></div>

        {{-- Zoom --}}
        <div id="zoom-ctrl">
            <button class="tbtn tbtn-secondary" onclick="zoom(-0.1)" title="Alejar">−</button>
            <span id="zoom-label">100%</span>
            <button class="tbtn tbtn-secondary" onclick="zoom(+0.1)" title="Acercar">+</button>
            <button class="tbtn tbtn-secondary" onclick="setZoom(1)" title="Restablecer">⊡</button>
        </div>

        <form id="save-form" method="POST" action="{{ route('admin.card-templates.update', $template) }}">
            @csrf @method('PUT')
            <input type="hidden" name="name" id="f-name" value="{{ $template->name }}">
            <input type="hidden" name="design_json" id="f-design-json" value="{{ $template->design_json ?? '{}' }}">
            <input type="hidden" name="width" id="f-width" value="{{ round($template->width / 37.795276, 2) }}">
            <input type="hidden" name="height" id="f-height" value="{{ round($template->height / 37.795276, 2) }}">
        </form>

        <button class="tbtn tbtn-secondary" onclick="openSettings()">⚙ Configurar</button>
        <button class="tbtn tbtn-success" onclick="saveDesign()">💾 Guardar diseño</button>
        <a href="{{ route('admin.card-templates.index') }}" class="tbtn tbtn-secondary"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Cerrar</a>
    </div>

    {{-- ── Workspace ───────────────────────────────────────────── --}}
    <div id="workspace">

        {{-- Left: Toolbox --}}
        <div id="toolbox">
            <div class="panel-label">Agregar elementos</div>

            <button class="tool-btn" onclick="addElement('text','Texto libre')">
                <span class="icon">T</span> Texto libre
            </button>
            <button class="tool-btn" onclick="addElement('field','@{{names}} @{{lastnames}}')">
                <span class="icon">👤</span> Nombre completo
            </button>
            <button class="tool-btn" onclick="addElement('field','@{{student_code}}')">
                <span class="icon">#</span> Código de carnet
            </button>
            <button class="tool-btn" onclick="addElement('field','@{{ci_number}}')">
                <span class="icon">🪪</span> Cédula
            </button>
            <button class="tool-btn" onclick="addElement('field','@{{career}}')">
                <span class="icon">🎓</span> Carrera
            </button>
            <button class="tool-btn" onclick="addElement('field','@{{semester}}')">
                <span class="icon">📅</span> Semestre
            </button>
            <button class="tool-btn" onclick="addElement('date','Vigente: @{{year}}')">
                <span class="icon">📆</span> Fecha / Vigencia
            </button>
            <button class="tool-btn" onclick="addElement('photo','[FOTO]')">
                <span class="icon">📸</span> Marcador de foto
            </button>

            <div class="tool-divider"></div>
            <div class="panel-label">Símbolos decorativos</div>
            <div class="var-grid">
                @foreach(['⬡','★','●','◆','◉','▶','⊕','⬛','🔷','🔶','✦','✧','❋','⚡','🔒'] as $sym)
                    <div class="var-chip" onclick="addElement('symbol','{{ $sym }}')" title="Agregar {{ $sym }}">{{ $sym }}</div>
                @endforeach
            </div>

            <div class="tool-divider"></div>
            <div class="panel-label">Variables disponibles</div>
            <div class="var-grid">
                @foreach(['{{names}}','{{lastnames}}','{{ci_number}}','{{student_code}}','{{career}}','{{semester}}','{{year}}'] as $var)
                    <div class="var-chip" onclick="insertVar('{{ $var }}')" title="Copiar variable">{{ $var }}</div>
                @endforeach
            </div>

            <div class="tool-divider"></div>
            <div class="panel-label">Información</div>
            <div style="padding:6px 14px 12px;color:var(--text-dim);font-size:11.5px;line-height:1.7">
                • Haz clic en un elemento para seleccionarlo<br>
                • Arrastra para reposicionar<br>
                • Usa el panel derecho para editar propiedades<br>
                • Las variables se reemplazan al generar el PDF
            </div>
        </div>

        {{-- Center: Canvas --}}
        <div id="canvas-area" onclick="deselectAll($event)">
            <div id="card-canvas"
                 style="width:{{ $template->width }}px;height:{{ $template->height }}px"
                 ondrop="handleDrop(event)" ondragover="event.preventDefault()">

                @if($template->background_path)
                    <img id="canvas-bg" src="{{ asset('storage/'.$template->background_path) }}" alt="Fondo">
                @else
                    <div id="canvas-bg-color"></div>
                @endif

                {{-- Elements rendered from design_json --}}
            </div>
        </div>

        {{-- Right: Properties --}}
        <div id="props-panel">
            <div id="no-selection">
                <span class="icon">🖱</span>
                Selecciona un elemento del canvas para editar sus propiedades
            </div>
            <div id="props-content" style="display:none">
                <div class="prop-group">
                    <div class="prop-label">Contenido</div>
                    <textarea class="prop-input" id="p-content" oninput="applyProps()" style="width:100%;margin-bottom:0"></textarea>
                </div>
                <div class="prop-group">
                    <div class="prop-label">Posición (px)</div>
                    <div class="prop-row">
                        <label>X</label>
                        <input type="number" class="prop-input" id="p-x" oninput="applyProps()" style="width:70px">
                        <label style="width:20px">Y</label>
                        <input type="number" class="prop-input" id="p-y" oninput="applyProps()" style="width:70px">
                    </div>
                </div>
                <div class="prop-group">
                    <div class="prop-label">Tipografía</div>
                    <div class="prop-row">
                        <label>Tamaño</label>
                        <input type="number" class="prop-input" id="p-font-size" min="6" max="200" oninput="applyProps()" style="width:60px">
                        <span style="font-size:11px;color:var(--text-dim)">px</span>
                    </div>
                    <div class="prop-row">
                        <label>Peso</label>
                        <select class="prop-input" id="p-font-weight" onchange="applyProps()">
                            <option value="400">Normal</option>
                            <option value="600">Semibold</option>
                            <option value="700">Bold</option>
                            <option value="800">ExtraBold</option>
                            <option value="900">Black</option>
                        </select>
                    </div>
                    <div class="prop-row">
                        <label>Alineación</label>
                        <select class="prop-input" id="p-text-align" onchange="applyProps()">
                            <option value="left">Izquierda</option>
                            <option value="center">Centro</option>
                            <option value="right">Derecha</option>
                        </select>
                    </div>
                    <div class="prop-row">
                        <label>Familia</label>
                        <select class="prop-input" id="p-font-family" onchange="applyProps()">
                            <option value="Inter, sans-serif">Inter</option>
                            <option value="Outfit, sans-serif">Outfit</option>
                            <option value="'Courier New', monospace">Monospace</option>
                            <option value="Georgia, serif">Serif</option>
                            <option value="Arial, sans-serif">Arial</option>
                            @foreach($fonts as $font)
                                <option value="'{{ $font->name }}', sans-serif">{{ $font->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="prop-group">
                    <div class="prop-label">Apariencia</div>
                    <div class="prop-row">
                        <label>Color</label>
                        <input type="color" class="prop-input" id="p-color" oninput="applyProps()">
                    </div>
                    <div class="prop-row">
                        <label>Opacidad</label>
                        <input type="range" class="prop-input" id="p-opacity" min="10" max="100" step="5"
                               oninput="applyProps(); document.getElementById('p-opacity-val').textContent=this.value+'%'">
                        <span id="p-opacity-val" style="font-size:11px;color:var(--text-dim);min-width:30px">100%</span>
                    </div>
                    <div class="prop-row">
                        <label>Ancho</label>
                        <input type="number" class="prop-input" id="p-width" min="0" oninput="applyProps()" style="width:70px">
                        <span style="font-size:11px;color:var(--text-dim)">px (0=auto)</span>
                    </div>
                    <div class="prop-row">
                        <label>Alto</label>
                        <input type="number" class="prop-input" id="p-height" min="0" oninput="applyProps()" style="width:70px">
                        <span style="font-size:11px;color:var(--text-dim)">px (0=auto)</span>
                    </div>
                    <div class="prop-row">
                        <label>Sombra</label>
                        <select class="prop-input" id="p-shadow" onchange="applyProps()">
                            <option value="">Ninguna</option>
                            <option value="1px 1px 3px rgba(0,0,0,.8)">Suave</option>
                            <option value="2px 2px 6px rgba(0,0,0,.9)">Media</option>
                            <option value="0 0 8px rgba(0,0,0,1),2px 2px 4px rgba(0,0,0,.9)">Fuerte</option>
                        </select>
                    </div>
                    <div class="prop-row">
                        <label>Fondo</label>
                        <select class="prop-input" id="p-bg" onchange="applyProps()">
                            <option value="">Ninguno</option>
                            <option value="rgba(0,0,0,.35)">Negro 35%</option>
                            <option value="rgba(0,0,0,.6)">Negro 60%</option>
                            <option value="rgba(255,255,255,.15)">Blanco 15%</option>
                            <option value="rgba(99,102,241,.5)">Violeta 50%</option>
                            <option value="rgba(6,182,212,.4)">Azul Foto</option>
                        </select>
                    </div>
                    <div class="prop-row">
                        <label>Borde-r.</label>
                        <input type="number" class="prop-input" id="p-radius" min="0" max="50" oninput="applyProps()" style="width:60px">
                        <span style="font-size:11px;color:var(--text-dim)">px</span>
                    </div>
                    <div class="prop-row">
                        <label>Padding</label>
                        <input type="number" class="prop-input" id="p-padding" min="0" max="40" oninput="applyProps()" style="width:60px">
                        <span style="font-size:11px;color:var(--text-dim)">px</span>
                    </div>
                </div>

                <div class="btn-row">
                    <button class="btn-prop btn-danger" onclick="deleteSelected()">🗑 Eliminar</button>
                    <button class="btn-prop btn-secondary" onclick="duplicateSelected()">⧉ Duplicar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> Diseño guardado</div>

{{-- Settings Modal --}}
<div id="settings-modal" style="
    display:none; position:fixed; inset:0; background:rgba(0,0,0,.7);
    z-index:1000; align-items:center; justify-content:center">
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;width:360px">
        <div style="font-size:16px;font-weight:700;margin-bottom:16px">⚙ Configuración de plantilla</div>
        <div style="margin-bottom:12px">
            <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Nombre</label>
            <input type="text" id="s-name" class="prop-input" style="width:100%" value="{{ $template->name }}">
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
            <div>
                <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Ancho (cm)</label>
                <input type="number" step="0.01" id="s-width" class="prop-input" style="width:100%" value="{{ round($template->width / 37.795276, 2) }}">
            </div>
            <div>
                <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Alto (cm)</label>
                <input type="number" step="0.01" id="s-height" class="prop-input" style="width:100%" value="{{ round($template->height / 37.795276, 2) }}">
            </div>
        </div>
        <div style="margin-bottom:18px">
            <label style="font-size:11px;color:var(--text-muted);display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Imagen de fondo</label>
            <form id="bg-form" method="POST" action="{{ route('admin.card-templates.update', $template) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="file" name="background" id="s-bg" accept="image/jpeg,image/png" style="font-size:12px;color:var(--text)">
                <button type="submit" class="btn-prop btn-primary" style="margin-top:8px;padding:7px 14px">Subir fondo</button>
            </form>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
            <button class="btn-prop btn-secondary" onclick="closeSettings()">Cancelar</button>
            <button class="btn-prop btn-primary" onclick="applySettings()">Aplicar</button>
        </div>
    </div>
</div>

<script>
// ── State ────────────────────────────────────────────────────────────────────
const CANVAS_W = {{ $template->width }};
const CANVAS_H = {{ $template->height }};

let elements   = [];
let selected   = null;
let zoomLevel  = 1;
let elIdCtr    = 0;

// ── Init ─────────────────────────────────────────────────────────────────────
(function init() {
    // Load saved design
    const saved = @json($template->design_json ?? '{}');
    const data  = typeof saved === 'string' ? JSON.parse(saved || '{}') : (saved || {});
    const objs  = data.objects || [];
    objs.forEach(o => {
        elIdCtr = Math.max(elIdCtr, (parseInt(o.id) || 0));
        elements.push(o);
    });
    renderAll();
    applyZoom();
})();

// ── Zoom ──────────────────────────────────────────────────────────────────────
function zoom(delta) { setZoom(Math.min(Math.max(zoomLevel + delta, 0.2), 3)); }
function setZoom(z) {
    zoomLevel = Math.round(z * 10) / 10;
    document.getElementById('zoom-label').textContent = Math.round(zoomLevel * 100) + '%';
    applyZoom();
}
function applyZoom() {
    const canvas = document.getElementById('card-canvas');
    canvas.style.transform = 'scale(' + zoomLevel + ')';
    canvas.style.transformOrigin = 'top center';
    // Adjust wrapper height so scroll works
    canvas.parentElement.style.minHeight = (CANVAS_H * zoomLevel + 64) + 'px';
}

// ── Element defaults ──────────────────────────────────────────────────────────
function defaultProps(type) {
    const base = {
        fontSize: 20, fontWeight: '700', color: '#ffffff',
        textAlign: 'left', fontFamily: 'Inter, sans-serif',
        opacity: 100, width: 0, height: 0, shadow: '1px 1px 3px rgba(0,0,0,.8)',
        background: '', borderRadius: 4, padding: 4,
    };
    if (type === 'symbol') { base.fontSize = 32; base.fontWeight = '400'; }
    if (type === 'photo')  { base.fontSize = 14; base.fontWeight = '400'; base.background = 'rgba(6,182,212,.4)'; base.width = 100; base.height = 130; base.textAlign = 'center'; }
    if (type === 'date')   { base.fontSize = 14; }
    return base;
}

// ── Add element ───────────────────────────────────────────────────────────────
function addElement(type, content) {
    elIdCtr++;
    const el = {
        id: String(elIdCtr),
        type,
        content,
        x: 30,
        y: 30 + (elements.length % 10) * 28,
        ...defaultProps(type),
    };
    elements.push(el);
    renderAll();
    selectById(el.id);
}

// ── Render all ────────────────────────────────────────────────────────────────
function renderAll() {
    const canvas = document.getElementById('card-canvas');
    // Remove existing elements (keep bg)
    canvas.querySelectorAll('.cv-element').forEach(e => e.remove());
    elements.forEach(el => renderElement(el));
}

function renderElement(el) {
    const canvas = document.getElementById('card-canvas');
    const div    = document.createElement('div');
    div.className  = 'cv-element';
    div.dataset.id = el.id;
    applyStyle(div, el);

    // Delete button
    const del = document.createElement('div');
    del.className   = 'del-btn';
    del.innerHTML = '×';
    del.addEventListener('mousedown', e => { e.stopPropagation(); deleteById(el.id); });
    div.appendChild(del);

    // Resize handle
    const rh = document.createElement('div');
    rh.className = 'resize-handle';
    rh.addEventListener('mousedown', e => startResize(e, el.id));
    div.appendChild(rh);

    // Content span — use innerHTML to allow SVG and rich content
    const content = document.createElement('span');
    content.className   = 'cv-content';
    content.innerHTML = el.content;
    div.appendChild(content);

    // Drag
    div.addEventListener('mousedown', e => startDrag(e, el.id));
    div.addEventListener('click', e => { e.stopPropagation(); selectById(el.id); });

    canvas.appendChild(div);
}

function applyStyle(div, el) {
    const w = el.width > 0 ? el.width + 'px' : 'auto';
    const h = el.height > 0 ? el.height + 'px' : 'auto';
    div.style.cssText = `
        left: ${el.x}px;
        top: ${el.y}px;
        font-size: ${el.fontSize}px;
        font-weight: ${el.fontWeight};
        color: ${el.color};
        text-align: ${el.textAlign};
        font-family: ${el.fontFamily};
        opacity: ${(el.opacity || 100) / 100};
        width: ${w};
        height: ${h};
        text-shadow: ${el.shadow || ''};
        background: ${el.background || ''};
        border-radius: ${el.borderRadius || 0}px;
        padding: ${el.padding || 0}px;
        position: absolute;
        cursor: move;
        white-space: nowrap;
        line-height: 1.2;
    `;
    if (el.width > 0 || el.height > 0) { 
        div.style.whiteSpace = 'normal'; 
        div.style.display = 'flex';
        div.style.flexDirection = 'column';
        div.style.justifyContent = 'center';
        div.style.alignItems = el.textAlign === 'center' ? 'center' : (el.textAlign === 'right' ? 'flex-end' : 'flex-start');
    }
}

// ── Select ────────────────────────────────────────────────────────────────────
function selectById(id) {
    selected = id;
    document.querySelectorAll('.cv-element').forEach(d => d.classList.remove('selected'));
    const div = document.querySelector('[data-id="' + id + '"]');
    if (div) div.classList.add('selected');
    const el = elements.find(e => e.id === id);
    if (el) loadProps(el);
}

function deselectAll(e) {
    if (e && e.target !== document.getElementById('canvas-area') &&
             e.target !== document.getElementById('card-canvas') &&
             !e.target.classList.contains('cv-element') &&
             !e.target.closest('.cv-element')) return;
    selected = null;
    document.querySelectorAll('.cv-element').forEach(d => d.classList.remove('selected'));
    document.getElementById('no-selection').style.display = '';
    document.getElementById('props-content').style.display = 'none';
}

// ── Props Panel ───────────────────────────────────────────────────────────────
function loadProps(el) {
    document.getElementById('no-selection').style.display  = 'none';
    document.getElementById('props-content').style.display = '';
    document.getElementById('p-content').value   = el.content;
    document.getElementById('p-x').value         = Math.round(el.x);
    document.getElementById('p-y').value         = Math.round(el.y);
    document.getElementById('p-font-size').value = el.fontSize;
    document.getElementById('p-font-weight').value = String(el.fontWeight);
    document.getElementById('p-text-align').value = el.textAlign;
    document.getElementById('p-font-family').value = el.fontFamily;
    document.getElementById('p-color').value     = el.color;
    document.getElementById('p-opacity').value   = el.opacity || 100;
    document.getElementById('p-opacity-val').textContent = (el.opacity || 100) + '%';
    document.getElementById('p-width').value     = el.width || 0;
    document.getElementById('p-height').value    = el.height || 0;
    document.getElementById('p-shadow').value    = el.shadow || '';
    document.getElementById('p-bg').value        = el.background || '';
    document.getElementById('p-radius').value    = el.borderRadius || 0;
    document.getElementById('p-padding').value   = el.padding || 0;
}

function applyProps() {
    if (!selected) return;
    const el = elements.find(e => e.id === selected);
    if (!el) return;
    el.content      = document.getElementById('p-content').value;
    el.x            = parseFloat(document.getElementById('p-x').value) || 0;
    el.y            = parseFloat(document.getElementById('p-y').value) || 0;
    el.fontSize     = parseInt(document.getElementById('p-font-size').value) || 16;
    el.fontWeight   = document.getElementById('p-font-weight').value;
    el.textAlign    = document.getElementById('p-text-align').value;
    el.fontFamily   = document.getElementById('p-font-family').value;
    el.color        = document.getElementById('p-color').value;
    el.opacity      = parseInt(document.getElementById('p-opacity').value) || 100;
    el.width        = parseInt(document.getElementById('p-width').value) || 0;
    el.height       = parseInt(document.getElementById('p-height').value) || 0;
    el.shadow       = document.getElementById('p-shadow').value;
    el.background   = document.getElementById('p-bg').value;
    el.borderRadius = parseInt(document.getElementById('p-radius').value) || 0;
    el.padding      = parseInt(document.getElementById('p-padding').value) || 0;

    const div = document.querySelector('[data-id="' + selected + '"]');
    if (div) {
        applyStyle(div, el);
        div.classList.add('selected');
        div.querySelector('.cv-content').innerHTML = el.content;
    }
}

// ── Drag & Drop ───────────────────────────────────────────────────────────────
let dragging = null, dragOffX = 0, dragOffY = 0;

function startDrag(e, id) {
    if (e.target.classList.contains('del-btn') ||
        e.target.classList.contains('resize-handle')) return;
    e.preventDefault();
    selectById(id);
    dragging = id;
    const canvas = document.getElementById('card-canvas');
    const rect   = canvas.getBoundingClientRect();
    const el     = elements.find(x => x.id === id);
    dragOffX = (e.clientX - rect.left) / zoomLevel - el.x;
    dragOffY = (e.clientY - rect.top)  / zoomLevel - el.y;

    document.addEventListener('mousemove', onDragMove);
    document.addEventListener('mouseup',   onDragEnd, { once: true });
}

function onDragMove(e) {
    if (!dragging) return;
    const canvas = document.getElementById('card-canvas');
    const rect   = canvas.getBoundingClientRect();
    const el     = elements.find(x => x.id === dragging);
    if (!el) return;
    el.x = Math.round(Math.max(0, (e.clientX - rect.left) / zoomLevel - dragOffX));
    el.y = Math.round(Math.max(0, (e.clientY - rect.top)  / zoomLevel - dragOffY));
    const div = document.querySelector('[data-id="' + dragging + '"]');
    if (div) { div.style.left = el.x + 'px'; div.style.top = el.y + 'px'; }
    document.getElementById('p-x').value = el.x;
    document.getElementById('p-y').value = el.y;
}

function onDragEnd() {
    dragging = null;
    document.removeEventListener('mousemove', onDragMove);
}

// ── Resize ────────────────────────────────────────────────────────────────────
let resizing = null, resizeStartX = 0, resizeStartW = 0;

function startResize(e, id) {
    e.preventDefault();
    e.stopPropagation();
    resizing    = id;
    resizeStartX = e.clientX;
    const el    = elements.find(x => x.id === id);
    resizeStartW = el.width || 150;
    document.addEventListener('mousemove', onResizeMove);
    document.addEventListener('mouseup',   onResizeEnd, { once: true });
}

function onResizeMove(e) {
    if (!resizing) return;
    const el = elements.find(x => x.id === resizing);
    if (!el) return;
    const delta = (e.clientX - resizeStartX) / zoomLevel;
    el.width    = Math.max(20, Math.round(resizeStartW + delta));
    const div   = document.querySelector('[data-id="' + resizing + '"]');
    if (div)    { div.style.width = el.width + 'px'; div.style.whiteSpace = 'normal'; }
    document.getElementById('p-width').value = el.width;
}

function onResizeEnd() {
    resizing = null;
    document.removeEventListener('mousemove', onResizeMove);
}

// ── Delete / Duplicate ────────────────────────────────────────────────────────
function deleteSelected() {
    if (!selected) return;
    deleteById(selected);
}

function deleteById(id) {
    elements = elements.filter(e => e.id !== id);
    const div = document.querySelector('[data-id="' + id + '"]');
    if (div) div.remove();
    selected = null;
    document.getElementById('no-selection').style.display  = '';
    document.getElementById('props-content').style.display = 'none';
}

function duplicateSelected() {
    if (!selected) return;
    const orig = elements.find(e => e.id === selected);
    if (!orig) return;
    elIdCtr++;
    const copy = { ...orig, id: String(elIdCtr), x: orig.x + 12, y: orig.y + 12 };
    elements.push(copy);
    renderElement(copy);
    selectById(copy.id);
}

// ── Variable insertion ────────────────────────────────────────────────────────
function insertVar(variable) {
    if (!selected) { addElement('field', variable); return; }
    const ta = document.getElementById('p-content');
    const pos = ta.selectionStart;
    const curr = ta.value;
    ta.value = curr.slice(0, pos) + variable + curr.slice(ta.selectionEnd);
    ta.focus();
    applyProps();
}

// ── Settings ──────────────────────────────────────────────────────────────────
function openSettings()  { document.getElementById('settings-modal').style.display = 'flex'; }
function closeSettings() { document.getElementById('settings-modal').style.display = 'none'; }
function applySettings() {
    const name = document.getElementById('s-name').value.trim();
    const w_cm = parseFloat(document.getElementById('s-width').value);
    const h_cm = parseFloat(document.getElementById('s-height').value);
    if (name) {
        document.getElementById('f-name').value = name;
        document.getElementById('tpl-name-label').textContent = name;
    }
    if (w_cm && h_cm) {
        document.getElementById('f-width').value  = w_cm;
        document.getElementById('f-height').value = h_cm;
        document.getElementById('dim-badge').textContent = w_cm + ' × ' + h_cm + ' cm';
        
        const px_w = Math.round(w_cm * 37.795276);
        const px_h = Math.round(h_cm * 37.795276);
        document.getElementById('card-canvas').style.width  = px_w + 'px';
        document.getElementById('card-canvas').style.height = px_h + 'px';
        applyZoom();
    }
    closeSettings();
}

// ── Keyboard shortcuts ────────────────────────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return;
    if (e.key === 'Delete' || e.key === 'Backspace') { deleteSelected(); e.preventDefault(); }
    if (e.ctrlKey && e.key === 'd') { duplicateSelected(); e.preventDefault(); }
    if (e.ctrlKey && e.key === 's') { saveDesign(); e.preventDefault(); }
    // Arrow nudge
    const nudge = e.shiftKey ? 10 : 1;
    const el = elements.find(x => x.id === selected);
    if (el) {
        if (e.key === 'ArrowLeft')  { el.x -= nudge; }
        if (e.key === 'ArrowRight') { el.x += nudge; }
        if (e.key === 'ArrowUp')    { el.y -= nudge; }
        if (e.key === 'ArrowDown')  { el.y += nudge; }
        if (['ArrowLeft','ArrowRight','ArrowUp','ArrowDown'].includes(e.key)) {
            e.preventDefault();
            const div = document.querySelector('[data-id="' + selected + '"]');
            if (div) { div.style.left = el.x + 'px'; div.style.top = el.y + 'px'; }
            document.getElementById('p-x').value = Math.round(el.x);
            document.getElementById('p-y').value = Math.round(el.y);
        }
    }
});

// ── Save ──────────────────────────────────────────────────────────────────────
function saveDesign() {
    const json = JSON.stringify({ objects: elements });
    document.getElementById('f-design-json').value = json;
    document.getElementById('save-form').submit();
}

// Show toast on load if success
@if(session('success'))
    window.addEventListener('DOMContentLoaded', () => showToast('{{ session('success') }}'));
@endif

function showToast(msg) {
    const t = document.getElementById('toast');
    t.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px"><polyline points="20 6 9 17 4 12"></polyline></svg> ' + msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
</body>
</html>
