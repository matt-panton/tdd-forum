@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <ais-index
         app-id="{{ config('scout.algolia.id') }}"
         api-key="{{ config('scout.algolia.public') }}"
         index-name="threads"
         query="{{ request('q') }}"
        >
            <div class="col-md-8">
                <ais-results>
                    <template slot-scope="{ result }">
                        <li>
                            <a :href="result.path">
                                <ais-highlight :result="result" attribute-name="title"></ais-highlight>
                            </a>
                        </li>
                    </template>
                </ais-results>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><h4>Search</h4></div>
                    <div class="panel-body">
                        <ais-search-box>
                            <div class="input-group">
                                <ais-input placeholder="Search something..." :autofocus="true" class="form-control"></ais-input>
                                <span class="input-group-btn">
                                    <ais-clear class="btn btn-default"></ais-clear>
                                </span>
                            </div>
                        </ais-search-box>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading"><h4>Filter by channel</h4></div>
                    <div class="panel-body">
                        <ais-refinement-list attribute-name="channel.name"></ais-refinement-list>
                    </div>
                </div>
            </div>
        </ais-index>
    </div>
</div>
@endsection
