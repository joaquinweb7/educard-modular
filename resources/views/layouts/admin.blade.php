<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Panel EduCard' }}</title>
    <meta name="description" content="Panel administrativo EduCard Modular">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .menu-group { margin-bottom: 5px; }
        .menu-trigger {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 15px; cursor: pointer; color: #9ca3af;
            border-radius: 6px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .menu-trigger:hover { color: #f3f4f6; }
        .menu-title { display: flex; align-items: center; gap: 8px; }
        .menu-trigger .chevron { transition: transform 0.2s; }
        .menu-group.open .menu-trigger .chevron { transform: rotate(180deg); }
        .submenu {
            list-style: none; padding: 0; margin: 0; display: none;
            padding-left: 10px; margin-top: 5px;
        }
        .menu-group.open .submenu { display: block; }
        .submenu li a {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 15px; color: #d1d5db; text-decoration: none;
            border-radius: 6px; margin-bottom: 2px; transition: background 0.2s, color 0.2s;
        }
        .submenu li a:hover, .submenu li a.active {
            background-color: rgba(255, 255, 255, 0.1); color: #fff;
        }
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <div class="brand">⬡ EduCard Modular</div>
        <nav class="sidebar-nav" style="padding-top: 10px;">
            @php
                $menuManager = app(\App\Services\MenuManager::class);
                $items = collect($menuManager->items());
                $groupedItems = $items->groupBy(fn($item) => $item['group'] ?? 'Complementos');
            @endphp

            @foreach($groupedItems as $groupName => $groupItems)
                @php
                    $isGroupActive = collect($groupItems)->contains(fn($item) => isset($item['route']) && request()->routeIs($item['route']));
                @endphp
                <div class="menu-group {{ $isGroupActive ? 'open' : '' }}">
                    <div class="menu-trigger" onclick="this.parentElement.classList.toggle('open')">
                        <div class="menu-title">{{ $groupName }}</div>
                        <i data-lucide="chevron-down" class="chevron" style="width: 16px; height: 16px;"></i>
                    </div>
                    <ul class="submenu">
                        @foreach($groupItems as $item)
                            <li>
                                <a href="{{ $menuManager->urlFor($item) }}" class="{{ isset($item['route']) && request()->routeIs($item['route']) ? 'active' : '' }}">
                                    @if(isset($item['icon']))
                                        <i data-lucide="{{ $item['icon'] }}" style="width: 18px; height: 18px;"></i>
                                    @else
                                        <span>•</span>
                                    @endif
                                    {{ $item['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn light w-100" type="submit">↩ Cerrar sesión</button>
        </form>
    </aside>

    <main class="main">
        <div class="topbar">
            <div>
                <h1>@yield('heading', 'Panel')</h1>
                <div class="muted small">Sistema de carnets estudiantiles con arquitectura modular</div>
            </div>
            <div class="user-chip">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                {{ auth()->user()->name ?? '' }}
            </div>
        </div>

        @if(session('success'))
            <div class="alert success"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> {{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</div>

<!-- Global Toast -->
<div id="global-toast">
    <span id="global-toast-icon"></span>
    <span id="global-toast-msg"></span>
</div>

<script>
    window.showToast = function(msg, type = 'success') {
        const toast = document.getElementById('global-toast');
        const icon = document.getElementById('global-toast-icon');
        const msgEl = document.getElementById('global-toast-msg');
        
        toast.className = '';
        toast.classList.add(type);
        
        icon.innerHTML = type === 'error' ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        msgEl.textContent = msg;
        
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    };

    window.confirmAction = function(message, formElement) {
        let overlay = document.getElementById('global-confirm-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'global-confirm-overlay';
            overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;backdrop-filter:blur(4px);opacity:0;transition:opacity 0.2s;';
            
            overlay.innerHTML = `
                <div style="background:var(--surface-1);border:1px solid var(--border);border-radius:12px;padding:24px;max-width:400px;width:90%;box-shadow:0 20px 25px -5px rgba(0,0,0,0.5);transform:scale(0.95);transition:transform 0.2s;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                        <div style="background:rgba(239,68,68,0.1);color:#ef4444;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        </div>
                        <h3 style="margin:0;font-size:16px;color:#fff;">Confirmar acción</h3>
                    </div>
                    <p id="global-confirm-message" style="margin:0 0 24px;color:var(--text-muted);font-size:14px;line-height:1.5;"></p>
                    <div style="display:flex;justify-content:flex-end;gap:12px;">
                        <button id="global-confirm-cancel" class="btn secondary" style="padding:8px 16px;">Cancelar</button>
                        <button id="global-confirm-accept" class="btn danger" style="padding:8px 16px;background:#ef4444;color:#fff;border:none;">Eliminar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);

            document.getElementById('global-confirm-cancel').addEventListener('click', () => {
                overlay.style.opacity = '0';
                overlay.children[0].style.transform = 'scale(0.95)';
                setTimeout(() => overlay.style.display = 'none', 200);
            });
        }
        
        document.getElementById('global-confirm-message').textContent = message;
        
        let acceptBtn = document.getElementById('global-confirm-accept');
        // Remove old event listeners
        let newAcceptBtn = acceptBtn.cloneNode(true);
        acceptBtn.parentNode.replaceChild(newAcceptBtn, acceptBtn);
        
        newAcceptBtn.addEventListener('click', () => {
            if(formElement) formElement.submit();
        });

        overlay.style.display = 'flex';
        // force reflow
        void overlay.offsetWidth;
        overlay.style.opacity = '1';
        overlay.children[0].style.transform = 'scale(1)';
    };

    window.lucide && lucide.createIcons();
</script>

@stack('scripts')
</body>
</html>
