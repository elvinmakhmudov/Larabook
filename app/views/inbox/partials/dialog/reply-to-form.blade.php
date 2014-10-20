<div class="send-message-form">
    @include('layouts.partials.errors')

    {{ Form::open(['route'=> 'inbox_path']) }}
        {{ Form::hidden('sendTo', $conv->id) }}
        <!-- Message Form Input -->
        <div class="form-group">
            {{ Form::label('message', 'Message:') }}
            {{ Form::textarea('message', null, ['class' => 'form-control','rows' => '3', 'placeholder' => 'Enter your message here']) }}
        </div>
        <!-- Submit Form Input -->
        <div class="form-group">
            {{ Form::submit('Send', ['class' => 'btn btn-primary form-control']) }}
        </div>
    {{ Form::close() }}
</div>
