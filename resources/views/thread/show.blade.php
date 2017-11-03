@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="#">{{ $thread->user->name }}</a> posted:
                    {{ $thread->title }}
                </div>

                <div class="panel-body">
                    {{ $thread->body }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @foreach ($thread->replies as $reply)
                @include('thread._reply')
            @endforeach
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @auth
                <form action="{{ route('reply.store', [$thread->cateogry, $thread]) }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea name="body" id="body" class="form-control" placeholder="Have something to say?" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            @else
                <p>Please <a href="{{ route('login') }}">sign in</a> to participate in this discussion.</p>
            @endauth
        </div>
    </div>
</div>
@endsection
