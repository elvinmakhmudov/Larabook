@if(count($previews))
    <div class="list-group preview">
    @foreach($previews as $preview)
           <a href="{{ route('inbox_path'). '?c='.$preview->id }}" class="list-group-item @if($preview->unread) list-group-item-info @endif @if($preview->id == $conversation->id) active @endif">
            <h4 class="list-group-item-heading">{{ $preview->present()->otherUsers() }}</h4>
            <p class="list-group-item-text">{{ $preview->content }}</p>
         </a>
    @endforeach
    </div>
@else
    <p>You have no conversation to show.</p>
@endif