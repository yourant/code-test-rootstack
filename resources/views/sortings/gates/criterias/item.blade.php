<b>{{$sortingGateCriteria->sorting_type_description}}</b>
{{--<br>--}}
{{--Geographical--}}

{{--@if($sortingGateCriteria->isByRegion)--}}
    {{--@foreach($sortingGateCriteria->list_regions_names as $region)--}}
        {{--<p class="m-l" title="{{$region->name}}">{{$region->name}}</p>--}}
    {{--@endforeach--}}
{{--@elseif($sortingGateCriteria->isByState)--}}
    {{--@foreach($sortingGateCriteria->list_states_names as $state)--}}
        {{--<p class="m-l" title="{{$state->name}}">{{$state->name}}</p>--}}
    {{--@endforeach--}}
{{--@elseif($sortingGateCriteria->isByTown)--}}
    {{--@foreach($sortingGateCriteria->list_towns_names as $town)--}}
        {{--<p class="m-l" title="{{$town->name}}">{{$town->name}}</p>--}}
    {{--@endforeach--}}
{{--@elseif($sortingGateCriteria->isByPostalOffice)--}}
    {{--@foreach($sortingGateCriteria->list_postal_offices_names as $postal)--}}
        {{--<p class="m-l" title="{{$postal->name}}">{{$postal->name}}</p>--}}
    {{--@endforeach--}}


{{--Particular--}}

{{--@elseif($sortingGateCriteria->isByValue)--}}
    {{--<p class="m-l">{{$sortingGateCriteria->range_values}}</p>--}}
{{--@elseif($sortingGateCriteria->isByWeight)--}}
    {{--<p class="m-l">{{$sortingGateCriteria->range_weight}}</p>--}}
{{--@elseif($sortingGateCriteria->isByCriteria)--}}
    {{--<p class="m-l">{{$sortingGateCriteria->criteria_code}}</p>--}}

{{--@endif--}}
