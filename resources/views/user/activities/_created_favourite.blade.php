@component('user.activities._activity', ['activity'=>$activity])
    @slot('heading')
        <a href="{{ $activity->subject->favouriteable->path() }}">
            {{ $user->name }} favourited a reply.
        </a>
    @endslot

    @slot('body')
        {{ $activity->subject->favouriteable->body }}
    @endslot
@endcomponent
