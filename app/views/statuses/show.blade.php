@extends('layouts.default')

@section('content')
    <script src="{{ asset('js/statuses/main.js') }}"></script>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">

        @include('statuses.partials.publish-status-form')

        <div id="statuses"></div>

        {{--@include('statuses.partials.statuses')--}}
        </div>
    </div>
@stop