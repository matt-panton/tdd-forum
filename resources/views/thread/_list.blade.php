@forelse ($threads as $thread)
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <div class="flex">
                    <h4>
                        <a href="{{ $thread->path() }}">
                            @if ($thread->hasUpdatesFor(auth()->user()))
                                <strong>{{ $thread->title }}</strong>
                            @else
                                {{ $thread->title }}
                            @endif
                        </a>
                    </h4>
                    
                    <h5>Posted by: <a href="{{ route('user.show', $thread->user) }}">{{ $thread->user->name }}</a></h5>
                </div>

                <strong><a href="{{ $thread->path() }}">{{ $thread->replies_count }} {{ str_plural('comment', $thread->replies_count) }}</a></strong>
            </div>
        </div>
        <div class="panel-body">
            <article>
                <div class="body">{{ $thread->body }}</div>
            </article>
        </div>
        <div class="panel-footer">
            {{ $thread->visits }} {{ str_plural('Visit', $thread->visits) }}
        </div>
    </div>
@empty
    <p>There are no relevant results to show at this time.</p>
@endforelse
