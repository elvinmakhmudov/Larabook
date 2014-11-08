<div class="dialog-messages">
    @foreach($messages as $message)
        <article class="media message">
            <div class="pull-left">
                @include('users.partials.avatar', ['user' => $message->sender ])
            </div>

            <div class="media-body message">
                <h4 class="media-heading sender"><a href="{{ $message->sender->present()->profileUrl() }}">{{ $message->sender->present()->username() }}</a></h4>
                {{ $message->content }}
            </div>
        </article>
    @endforeach
</div>
