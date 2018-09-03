@extends('layouts.admin.page')

@section('page_title') Create Sorting @stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li><a href="{!! route('root') !!}">Home</a></li>
        <li><a href="{!! route('sortings.index') !!}">Sorting</a></li>
        <li>Create</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="ibox">
                {!! Form::open(['route' => 'sortings.store', 'method' => 'POST']) !!}

                <div class="ibox-content">
                    <div class="form-group">
                        <h3>{!! Form::label('name', 'Sorting Name') !!}</h3>
                        <p class="help-block">Define a name for this sorting. For example: Mexico by postal offices</p>
                        {!! Form::input('text', 'name', null, ['class'=>'form-control', 'placeholder'=>'', 'id' => 'name']) !!}
                    </div>

                    <div class="form-group m-t-lg">
                        <h3>{!! Form::label('service_id', 'Available Solutions') !!}</h3>
                        <p class="help-block">Choose one of the available solutions that will be used for this sorting.</p>
                        {!! Form::select('service_id', collect(['' => 'N/A'])->merge($services->pluck('code', 'id')), isset($params['service_id']) ? $params['service_id'] : null, ['class'=>'form-control', 'style' => 'width: 100%;']) !!}
                    </div>

                    <div class="form-group m-t-lg">
                        <h3>{!! Form::label('sorting_type_id[]', 'Sorting Types') !!}</h3>
                        <p class="help-block">Now, specify a sorting type. You can select multiple sorting types too, but try to keep it simple.</p>
                        @foreach ($sortingTypes as $sortingType)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="sorting_type_id[{!! $sortingType->id !!}]"
                                           value="1" {!! $sorting->containsSortingType($sortingType->getWrappedObject()) ? 'checked' : '' !!}> {!! $sortingType->description !!}
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