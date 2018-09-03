@extends('layouts.admin.page')

@section('page_title') Sorting #{{ $sorting->id}} @stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li>{!! link_to_route('root', 'Home') !!}</li>
        <li><a href="/sortings/">Sortings</a></li>
        <li><strong>{{$sorting->name}}</strong></li>
    </ol>
@stop

@section('content')

    <div class="row">
        <?php $colors = ['#9C27B0', '#2196F3', '#4CAF50', '#F4645F']; ?>

        @forelse($sorting_gates as $k => $sortingGate)
            <div class="col-sm-6 col-md-4">
                <div class="ibox product-box" style="border-top: 2px solid {!! $colors[$k % count($colors)] !!};">
                    <div class="ibox-content product-content">
                        <h1 class="pull-right" style="background-color: {!! $colors[$k % count($colors)] !!}; padding: 8px 16px; color: white; opacity: 0.6;">{!! $sortingGate->number !!}</h1>

                        <h2 class="title elipsis m-b" title="{!! $sortingGate->name !!}">
                            {!! $sortingGate->name !!}<br>
                            <small>{!! $sortingGate->code !!}</small>
                        </h2>


                        <div class="well m-b-xs">
                            @if ($sortingGate->default)
                                <b>Default Gate</b>
                            @else
                                @foreach($sortingGate->sortingGateCriterias as $sortingGateCriteria)
                                    <b>{{$sortingGateCriteria->sorting_type_description}}</b>
                                @endforeach
                            @endif
                        </div>
                        <hr>

                        <div class="m-t text-right">
                            <a href="{{ route('sortings.gates.edit', [$sorting->id, $sortingGate->id]) }}" class="btn btn-sm btn-link">
                                <i class="fa fa-trash-o fa-fw"></i>Edit
                            </a>
                            @if (!$sortingGate->default)
                                <a href="{{ route('sortings.gates.destroy', [$sorting->id, $sortingGate->id]) }}" class="btn btn-sm btn-link" data-confirm="Are you sure?" data-method="DELETE">
                                    <i class="fa fa-trash-o fa-fw"></i> Delete
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            Empty.
        @endforelse

        <div class="col-sm-6 col-md-4">
            <div class="ibox product-box" style="border-top: 2px solid gray;">
                <div class="ibox-content product-content" style="min-height: 238px;">
                    {!! link_to_route('sortings.gates.create', 'Add Gate', [$sorting->id], ['class'=>'btn btn-default btn-lg btn-block', 'style' => 'margin-top: 75px; font-size: 20px;']) !!}
                </div>
            </div>
        </div>

    </div>
@stop