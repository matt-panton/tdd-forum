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

            @foreach ($user->activity as $date => $activities)
                <h3 class="page-header">{{ $date }}</h3>
                @foreach ($activities as $activity)
                    @include("user.activities._{$activity->type}")
                @endforeach
            @endforeach 
        </div>
    </div>
</div>
@endsection
