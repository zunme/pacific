@if ($paginator->hasPages())
    <ul class="pagination justify-content-center mb-0">
       
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span class="page-link"><i class="fas fa-angle-left"></i></span></li>
        @else
            <li class="page-item"><a onClick="getlist('{{ $paginator->previousPageUrl() }}')" rel="prev" class="page-link"><i class="fas fa-angle-left"></i></a></li>
        @endif


      
        @foreach ($elements as $element)
           
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif


           
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active my-active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a onClick="getlist('{{ $url }}')" class="page-link">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach


        
        @if ($paginator->hasMorePages())
            <li class="page-item"><a onClick="getlist('{{ $paginator->nextPageUrl() }}')" rel="next" class="page-link"><i class="fas fa-angle-right"></i></a></li>
        @else
            <li class="page-item disabled"><span  class="page-link"><i class="fas fa-angle-right"></i></span></li>
        @endif
    </ul>
@endif 