@if(count($previews))
    @foreach($previews as $preview)
       <div class="list-group preview">
         <a href="{{ route('inbox_path'). '?c='.$preview->id }}" class="list-group-item active">
            <h4 class="list-group-item-heading">{{ $preview->present()->otherUsers() }}</h4>
            <p class="list-group-item-text">{{ $preview->content }}</p>
         </a>
       </div>
    @endforeach
@else
    <p>You have no conversation to show.</p>
@endif