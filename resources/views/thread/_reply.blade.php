<div class="panel panel-default">
    <div class="panel-heading">
        <div class="level">
            <h5 class="flex"><a href="{{ route('user.show', $reply->user) }}">{{ $reply->user->name }}</a> said {{ $reply->created_at->diffForHumans() }}:</h5>
            
            <div>
                <form method="POST" action="{{ route('reply.favourite', $reply) }}">
                    {{ csrf_field() }}
                    <button class="btn btn-default btn-xs" {{ $reply->isFavourited() || !auth()->check() ? 'disabled' : '' }}>
                        {{ $reply->favourites_count }} {{ str_plural('Favourite', $reply->favourites_count) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="panel-body">
        {{ $reply->body }}
    </div>
</div>
