@if ($paginator->hasPages())
    <ul class="pagination m-0">

        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <a class="page-link">
                    <i data-feather="chevrons-left"></i>&nbsp;Previous
                </a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                    <i data-feather="chevrons-left"></i>&nbsp;Previous
                </a>
            </li>
        @endif

        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                    Next&nbsp;<i data-feather="chevrons-right"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <a class="page-link">
                    Next&nbsp;<i data-feather="chevrons-right"></i>
                </a>
            </li>
        @endif

    </ul>
@endif