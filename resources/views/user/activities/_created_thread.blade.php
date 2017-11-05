@component('user.activities._activity', ['activity'=>$activity])
    @slot('heading')
        {{ $user->name }} published <a href="{{ $activity->subject->path() }}">"{{ $activity->subject->title }}"</a>
    @endslot

    @slot('body')
        {{ $activity->subject->body }}
    @endslot
@endcomponent
