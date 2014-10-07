@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-md-offset-1 col-md-3">

            <div class="text-center">
                <p><a class="btn btn-primary btn-lg new-message-button" href="{{ route('new_message_path') }}">New Message</a></p>
            </div>

            @include('inbox.partials.previews')

        </div>
        <div class="col-md-7">

            @include('inbox.partials.dialog', ['conv' => $conversation])

        </div>
    </div>
@stop