@extends('layouts.default')

@section('content')
    <script src="{{ asset('js/statuses/main.js') }}"></script>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">

        @include('statuses.partials.publish-status-form')

        @include('statuses.partials.status-template')

        <div class="statuses"></div>

        <button class="btn btn-primary form-control" type="submit" id="loadMoreButton" value='0'>Load more statuses...</button>

        {{--@include('statuses.partials.statuses')--}}
        </div>
    </div>
@stop