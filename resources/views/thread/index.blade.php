@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @include('thread._list')

            {{ $threads->links() }}
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Search</h4></div>
                <div class="panel-body">
                    <form action="{{ route('thread.search') }}" method="GET">
                        <div class="input-group">
                            <input type="text" placeholder="Search for something..." value="{{ request('q') }}" class="form-control" name="q">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary">Go</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @include('thread._trending')
        </div>
    </div>
</div>
@endsection
