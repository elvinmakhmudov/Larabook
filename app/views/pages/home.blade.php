@extends('layouts.default')

@section('content')

    <div class="jumbotron">
        <h1>Welcome to Larabook!</h1>
        <p>Welcome to Larabook's main page. Here you can talk with others. Sign Up to make new friends!</p>
        @if( ! $currentUser )
            <p>
                {{ link_to_route('register_path', 'Sign Up!', null, ['class' => 'btn btn-lg btn-primary']) }}
            </p>
        @endif
    </div>

@stop