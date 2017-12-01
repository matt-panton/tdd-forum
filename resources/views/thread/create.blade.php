@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create a New Thread</div>

                <div class="panel-body">
                    <form action="{{ route('thread.store') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="channel_id" class="control-label">Channel</label>
                            <select name="channel_id" id="channel_id" class="form-control">
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            <label for="title" class="control-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
                            {!! $errors->first('title', '<span class="help-block">:message</span>') !!}
                        </div>
                        <div class="form-group {{ $errors->has('body') ? 'has-error' : '' }}">
                            <label for="body" class="control-label">Body</label>
                            <textarea name="body" id="body" rows="5" class="form-control">{{ old('body') }}</textarea>
                            {!! $errors->first('body', '<span class="help-block">:message</span>') !!}
                        </div>

                        <div class="form-group {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                            <div class="g-recaptcha" data-sitekey="6LfG7joUAAAAALXMj0H5KTjJBXNXpus5GYZBVuEI"></div>
                            {!! $errors->first('g-recaptcha-response', '<span class="help-block">:message</span>') !!}
                        </div>

                        <button type="submit" class="btn btn-primary">Publish</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@push('head')
<script src='https://www.google.com/recaptcha/api.js'></script>
@endpush
