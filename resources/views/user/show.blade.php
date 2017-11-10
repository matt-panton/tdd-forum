@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-header">
                <h1>
                    {{ $user->name }}
                    <small>Since {{ $user->created_at->diffForHumans() }}</small>
                </h1>
            </div>

            @forelse ($user->activity as $date => $activities)
                <h3 class="page-header">{{ $date }}</h3>
                @foreach ($activities as $activity)
                    @if (view()->exists("user.activities._{$activity->type}"))
                        @include("user.activities._{$activity->type}")
                    @endif
                @endforeach
            @empty
                <p>There is no activity for this user yet.</p>
            @endforelse 
        </div>
    </div>
</div>
@endsection
