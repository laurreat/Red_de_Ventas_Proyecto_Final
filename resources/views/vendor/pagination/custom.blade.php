{{-- Load pagination styles with cache busting --}}
@once
    <link rel="stylesheet" href="{{ asset('css/components/pagination.css') }}?v={{ filemtime(public_path('css/components/pagination.css')) }}">
@endonce

@if ($paginator->hasPages())
    <nav class="custom-pagination" aria-label="Navegación de páginas">
        <div class="pagination-info">
            <p class="pagination-text">
                Mostrando <strong>{{ $paginator->firstItem() }}</strong> a <strong>{{ $paginator->lastItem() }}</strong> de <strong>{{ $paginator->total() }}</strong> resultados
            </p>
        </div>

        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled" aria-disabled="true">
                    <span class="pagination-link pagination-arrow" aria-hidden="true">
                        <i class="bi bi-chevron-left"></i>
                        <span class="pagination-arrow-text">Anterior</span>
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <a class="pagination-link pagination-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Página anterior">
                        <i class="bi bi-chevron-left"></i>
                        <span class="pagination-arrow-text">Anterior</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="pagination-item disabled" aria-disabled="true">
                        <span class="pagination-link pagination-dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item active" aria-current="page">
                                <span class="pagination-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a class="pagination-link pagination-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Página siguiente">
                        <span class="pagination-arrow-text">Siguiente</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pagination-item disabled" aria-disabled="true">
                    <span class="pagination-link pagination-arrow" aria-hidden="true">
                        <span class="pagination-arrow-text">Siguiente</span>
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
