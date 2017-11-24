<div class="panel panel-default">
    <div class="panel-heading"><h4>Trending Threads</h4></div>

    <ul class="list-group">
        @forelse($trendingThreads as $trendingThread)
            <li class="list-group-item">
                <a href="{{ $trendingThread->path }}">{{ $trendingThread->title }}</a>
            </li>
        @empty
            <li class="list-group-item">No trending threads.</li>
        @endforelse
    </ul>
</div>
