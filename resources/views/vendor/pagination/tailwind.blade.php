@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 12px;">
        <div class="pagination-info" style="font-size: 13px; color: var(--text-muted);">
            Mostrando
            <span style="font-weight: 600; color: var(--text);">{{ $paginator->firstItem() }}</span>
            a
            <span style="font-weight: 600; color: var(--text);">{{ $paginator->lastItem() }}</span>
            de
            <span style="font-weight: 600; color: var(--text);">{{ $paginator->total() }}</span> resultados
        </div>

        <ul class="pagination-links" style="display: flex; list-style: none; padding: 0; margin: 0; gap: 4px; align-items: center;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span style="display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: var(--radius-sm); background: var(--surface-2); border: 1px solid var(--border); color: var(--text-muted); font-size: 13px; font-weight: 600; opacity: 0.5; cursor: not-allowed; pointer-events: none;">
                        &laquo;
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: var(--radius-sm); background: var(--surface-2); border: 1px solid var(--border); color: var(--text-muted); font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.background='var(--surface-3)'; this.style.color='var(--text)';" onmouseout="this.style.background='var(--surface-2)'; this.style.color='var(--text-muted)';">
                        &laquo;
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span style="display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; color: var(--text-muted); font-size: 13px;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span style="display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: var(--radius-sm); background: var(--primary); border: 1px solid var(--primary); color: #fff; font-size: 13px; font-weight: 600; box-shadow: 0 2px 8px rgba(99,102,241,0.3);">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" style="display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: var(--radius-sm); background: var(--surface-2); border: 1px solid var(--border); color: var(--text-muted); font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.background='var(--surface-3)'; this.style.color='var(--text)';" onmouseout="this.style.background='var(--surface-2)'; this.style.color='var(--text-muted)';">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: var(--radius-sm); background: var(--surface-2); border: 1px solid var(--border); color: var(--text-muted); font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.background='var(--surface-3)'; this.style.color='var(--text)';" onmouseout="this.style.background='var(--surface-2)'; this.style.color='var(--text-muted)';">
                        &raquo;
                    </a>
                </li>
            @else
                <li>
                    <span style="display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: var(--radius-sm); background: var(--surface-2); border: 1px solid var(--border); color: var(--text-muted); font-size: 13px; font-weight: 600; opacity: 0.5; cursor: not-allowed; pointer-events: none;">
                        &raquo;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
