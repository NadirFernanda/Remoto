@php
    $scrollTo = $scrollTo ?? 'body';
    $scrollIntoViewJsSnippet = $scrollTo !== false
        ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
        : '';
@endphp

@if ($paginator->hasPages())
    <nav class="fl-pagination" role="navigation" aria-label="Paginação">
        <p class="fl-pagination-summary">
            A mostrar <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
            de <strong>{{ $paginator->total() }}</strong> freelancers
        </p>

        <div class="fl-pagination-pages">
            @if ($paginator->onFirstPage())
                <span class="fl-page fl-page-disabled" aria-hidden="true">‹</span>
            @else
                <button type="button" class="fl-page" wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}" aria-label="Página anterior">‹</button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="fl-page fl-page-disabled">{{ $element }}</span>
                @elseif (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="fl-page fl-page-current" aria-current="page">{{ $page }}</span>
                        @else
                            <button type="button" class="fl-page" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}" aria-label="Ir para a página {{ $page }}">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button type="button" class="fl-page" wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}" aria-label="Página seguinte">›</button>
            @else
                <span class="fl-page fl-page-disabled" aria-hidden="true">›</span>
            @endif
        </div>
    </nav>
@endif
