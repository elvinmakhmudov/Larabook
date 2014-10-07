@extends('layouts.default')

@section('content')
    <div class="row">
        <div class="col-md-offset-1 col-md-10">

        @include('inbox.partials.send-message-form')

        </div>
    </div>
@stop
