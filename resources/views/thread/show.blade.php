@extends('layouts.app')

@section('content')
<thread-view inline-template :initial-replies-count="0">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="level">
                            <span class="flex">
                                <a href="{{ route('user.show', $thread->user) }}">{{ $thread->user->name }}</a> posted:
                                {{ $thread->title }}
                            </span>


                            @can('destroy', $thread)
                                <form action="{{ $thread->path() }}" method="POST">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    <div class="panel-body">
                        {{ $thread->body }}
                    </div>
                </div>

                <replies @removed="repliesCount--" @added="repliesCount++" @loaded="handleRepliesLoaded"></replies>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>This thread was published {{ $thread->created_at->diffForHumans() }} by <a href="{{ route('user.show', $thread->user) }}">{{ $thread->user->name }}</a> and currently has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
@endsection
