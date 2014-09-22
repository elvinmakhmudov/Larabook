@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-md-offset-1 col-md-3">
            @include('inbox.partials.conversations')
        </div>
        <div class="col-md-7">
            @foreach($convs as $conv)
                @include('inbox.partials.messages')
            @endforeach
            @include('inbox.partials.send-message-form')
            <article class="media">
                <div class="pull-left">
                    @include('users.partials.avatar', ['user' => $currentUser ])
                </div>
                <div class="pull-right">
                    @include('users.partials.avatar', ['user' => $currentUser ])
                </div>
            </article>
        </div>
    </div>
@stop