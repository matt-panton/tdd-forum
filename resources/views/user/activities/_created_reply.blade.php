@component('user.activities._activity', ['activity'=>$activity])
    @slot('heading')
        {{ $user->name }} replied to a <a href="{{ $activity->subject->thread->path() }}">"{{ $activity->subject->thread->title }}"</a>
    @endslot

    @slot('body')
        {{ $activity->subject->body }}
    @endslot
@endcomponent
