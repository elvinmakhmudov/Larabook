<div class="dialog">
    @if(isset($conv->messages))
        @foreach($conv->messages->reverse() as $message)
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

        @include('inbox.partials.reply-to-form')

    @else

        <h1>You haven't talked to anyone.</h1>

        @include('inbox.partials.send-message-form')

    @endif
</div>