<div class="dialog">
    @if(isset($conv->messages))

        @include('inbox.partials.dialog.nav-bar')

        @include('inbox.partials.dialog.messages')

        @include('inbox.partials.dialog.reply-to-form')

    @else

        <h1>You haven't talked to anyone.</h1>

        @include('inbox.partials.send-message-form')

    @endif
</div>