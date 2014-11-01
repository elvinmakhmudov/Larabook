<div class="dialog">
    @if(isset($messages))

        @include('inbox.partials.dialog.nav-bar')

        @include('inbox.partials.dialog.messages')

        @include('inbox.partials.dialog.reply-to-form')

    @else

        <h1>You haven't talked to anyone.</h1>

    @endif
</div>