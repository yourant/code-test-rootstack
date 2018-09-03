@extends('layouts.admin.page')

@section('page_title') Edit Sorting @stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li><a href="{!! route('root') !!}">Home</a></li>
        <li><a href="{!! route('sortings.index') !!}">Sorting</a></li>
        <li>Edit</li>
    </ol>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="ibox">
                {!! Form::model($sorting, ['route' => ['sortings.update', $sorting->id], 'method' => 'PUT']) !!}
                <div class="ibox-content">
                    <div class="form-group">
                        {!! Form::label('name', 'Sorting Name') !!}
                        <p class="help-block">Define a name for this sorting. For example: Mexico by postal offices</p>
                        {!! Form::input('text', 'name', null, ['class'=>'form-control', 'placeholder'=>'']) !!}
                    </div>

                    <div class="form-group m-t-lg">
                        {!! Form::label('service_id', 'Service') !!}
                        {!! Form::text('service_id', $sorting->service_code, ['class'=>'form-control', 'readonly', 'disabled']) !!}
                    </div>

                    <div class="form-group m-t-lg">
                        {!! Form::label('sorting_type_id[]', 'Sorting Types') !!}
                        <p class="help-block">Now, specify a sorting type. You can select multiple sorting types too, but try to keep it simple.</p>
                        @foreach ($sortingTypes as $sortingType)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="sorting_type_id[{!! $sortingType->id !!}]" readonly="readonly" disabled
                                           value="1" {!! $sorting->getWrappedObject()->containsSortingType($sortingType->getWrappedObject()) ? 'checked' : '' !!}> {!! $sortingType->description !!}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <hr>

                    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@stop

@section('inline_scripts')
    <script>
        jQuery(function ($) {
            $('select[name="service_id"]').select2({
                placeholder: "-- Select a service",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@append