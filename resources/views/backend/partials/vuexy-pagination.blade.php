@if ($paginator->hasPages())
    <nav class="vx-pagination" role="navigation" aria-label="Pagination Navigation">
        <ul>
            <li class="{{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span>&laquo;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="disabled"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="{{ $page === $paginator->currentPage() ? 'active' : '' }}">
                            @if ($page === $paginator->currentPage())
                                <span>{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li class="{{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
                @else
                    <span>&raquo;</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
