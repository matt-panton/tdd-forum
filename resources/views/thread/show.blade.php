@extends('layouts.app')

@section('content')
<thread-view inline-template :initial-thread="{{ $thread }}">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="level">
                            <img src="{{ $thread->user->avatar }}" width="25" height="25" style="margin-right: 10px;">

                            <span class="flex">
                                <a href="{{ route('user.show', $thread->user) }}">{{ $thread->user->name }}</a> posted:
                                <input type="text" class="form-control" v-model="editedThread.title" v-if="editing">
                                <template v-else>@{{ thread.title }}</template>
                            </span>
                        </div>
                    </div>

                    <div class="panel-body">
                        <textarea v-if="editing" v-model="editedThread.body" class="form-control" rows="5"></textarea>
                        <div v-else v-text="thread.body"></div>
                    </div>

                    <div class="panel-footer" v-if="authorize('owns', thread)">
                        <div class="clearfix">
                            <div class="btn-group">
                                <div class="btn btn-xs btn-default" type="button" @click="editing = true" v-if="!editing">Edit</div>
                                <div class="btn btn-xs btn-default" type="button" @click="editing = false" v-if="editing">Cancel</div>
                                <div class="btn btn-xs btn-success" type="button" @click="save()" v-if="editing" :disabled="saving">@{{ saving ? 'Saving...' : 'Save' }}</div>
                            </div>

                            <form action="{{ $thread->path() }}" method="POST" v-if="authorize('owns', thread)" class="pull-right">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

                <replies @removed="repliesCount--" @added="repliesCount++" @loaded="handleRepliesLoaded"></replies>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>This thread was published {{ $thread->created_at->diffForHumans() }} by <a href="{{ route('user.show', $thread->user) }}">{{ $thread->user->name }}</a> and currently has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}.</p>
                        <subscribe-button v-if="signedIn" :initial-active="{{ json_encode($thread->is_subscribed_to) }}"></subscribe-button>
                    
                        <button type="button" v-if="authorize('isAdmin')" class="btn btn-warning" @click="toggleLock()" v-text="thread.locked ? 'Unlock Thread' : 'Lock Thread'"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
@endsection



@push('css')
    <link rel="stylesheet" href="{{ asset('css/vendor/jquery.atwho.css') }}">
@endpush
