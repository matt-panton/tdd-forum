@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @include('thread._list')

            {{ $threads->links() }}
        </div>

        <div class="col-md-4">
            @include('thread._trending')
        </div>
    </div>
</div>
@endsection
