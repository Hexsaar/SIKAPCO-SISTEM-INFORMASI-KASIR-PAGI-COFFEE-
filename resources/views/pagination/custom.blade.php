@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center space-x-1">
        <!-- Previous Button -->
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-gray-100 rounded-lg cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="ml-1">Previous</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#4F2E22] rounded-lg hover:bg-[#3e251b] transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="ml-1">Previous</span>
            </a>
        @endif

        <!-- Page Numbers -->
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" 
                              class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-[#4F2E22] rounded-lg shadow-md ring-2 ring-[#4F2E22] ring-offset-2">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" 
                           class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-[#4F2E22] hover:text-[#4F2E22] transition-all duration-200">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        <!-- Next Button -->
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#4F2E22] rounded-lg hover:bg-[#3e251b] transition-colors duration-200 shadow-sm">
                <span class="mr-1">Next</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        @else
            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-gray-100 rounded-lg cursor-not-allowed">
                <span class="mr-1">Next</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </span>
        @endif
    </nav>
@endif
