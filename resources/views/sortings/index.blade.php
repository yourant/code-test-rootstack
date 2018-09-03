@extends('layouts.admin.page')

@section('page_title') Sorting @stop

@section('breadcrumbs')
    <div class="header-actions">
        {!! link_to_route('sortings.create', 'Create Sorting', null, ['class'=>'btn btn-primary btn-outline btn-flat btn-md pull-right']) !!}
    </div>
    <ol class="breadcrumb">
        <li>{!! link_to_route('root', 'Home') !!}</li>
        <li><strong>Sorting</strong></li>
    </ol>
@stop

@section('content')
    <div class="i-box">
        <div class="ibox-content">
            {!! $filters !!}
        </div>
    </div>
    <div class="i-box m-t">
        <div class="ibox-content">
            <div class="table-responsive">
                {!! $table !!}
            </div>
        </div>
    </div>
@stop