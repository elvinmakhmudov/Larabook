<div class="messages">
    @foreach($conv->messages as $message)
        <article class="media">
            <div class="pull-left">
                @include('users.partials.avatar', ['user' => $message->sender ])
            </div>

            <div class="media-body">
                <h4 class="media-heading">{{ $message->sender->username }}</h4>
                {{ $message->content }}
            </div>
        </article>
    @endforeach
</div>