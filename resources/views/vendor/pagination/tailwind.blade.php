@if ($paginator->hasPages())
    <nav style="display:flex;align-items:center;justify-content:space-between;gap:1rem;" aria-label="Pagination">

        {{-- Left: showing X to Y of Z --}}
        <p style="font-size:0.78rem;color:var(--text-muted);white-space:nowrap;">
            Showing
            <span style="font-weight:700;color:var(--text-secondary);">{{ $paginator->firstItem() }}</span>
            –
            <span style="font-weight:700;color:var(--text-secondary);">{{ $paginator->lastItem() }}</span>
            of
            <span style="font-weight:700;color:var(--text-secondary);">{{ $paginator->total() }}</span>
            results
        </p>

        {{-- Right: page buttons --}}
        <div style="display:flex;align-items:center;gap:0.25rem;">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border-color);border-radius:var(--radius-sm);color:var(--text-muted);opacity:0.4;cursor:not-allowed;">
                    <i class="ph ph-caret-left" style="font-size:0.85rem;"></i>
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                        style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border-color);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);cursor:pointer;transition:all 0.15s;"
                        onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                        onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'"
                        aria-label="Previous page">
                    <i class="ph ph-caret-left" style="font-size:0.85rem;"></i>
                </button>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:0.78rem;color:var(--text-muted);">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:1px solid var(--primary);border-radius:var(--radius-sm);background:var(--primary);color:white;font-size:0.78rem;font-weight:700;">
                                {{ $page }}
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $page }})"
                                    style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border-color);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.15s;"
                                    onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                                    onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'"
                                    aria-label="Go to page {{ $page }}">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                        style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border-color);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);cursor:pointer;transition:all 0.15s;"
                        onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                        onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-secondary)'"
                        aria-label="Next page">
                    <i class="ph ph-caret-right" style="font-size:0.85rem;"></i>
                </button>
            @else
                <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border-color);border-radius:var(--radius-sm);color:var(--text-muted);opacity:0.4;cursor:not-allowed;">
                    <i class="ph ph-caret-right" style="font-size:0.85rem;"></i>
                </span>
            @endif

        </div>
    </nav>
@endif
