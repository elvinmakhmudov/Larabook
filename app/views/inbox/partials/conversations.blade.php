@if($convs->count())
    @foreach($convs as $conv)
       <div class="list-group">
         <a href="#" class="list-group-item active">
           <h4 class="list-group-item-heading">{{ $conv->messages[0]->sender->username }}</h4>
           <p class="list-group-item-text">{{ $conv->messages[0]->content }}</p>
         </a>
       </div>
    @endforeach
@else
    <p>You have no conversation to show.</p>
@endif