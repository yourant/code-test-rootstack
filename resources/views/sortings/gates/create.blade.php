@extends('layouts.admin.page')

@section('page_title') Create Sorting Gate @stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li><a href="{!! route('root') !!}">Home</a></li>
        <li><a href="{!! route('sortings.index') !!}">Sorting</a></li>
        <li><a href="{!! route('sortings.gates.index', $sorting->id) !!}">Gates</a></li>
        <li>Create</li>
    </ol>
@stop

@section('content')

    <div class="ibox">
        <div class="ibox-content">
            {!! Form::open(['route' => ['sortings.gates.store', $sorting->id], 'method' => 'POST']) !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="well">
                        <h2>Gate Code</h2>
                        <div style="display: flex;">
                            <!-- Continent -->
                            @php $i = 0; @endphp
                            @foreach (str_split($sorting->continent_abbreviation) as $k => $digit)
                                {!! Form::text('digits[' . ($k + $i) .']', $digit, ['class' => 'text-input number', 'readonly', 'disabled', 'maxlength' => 1]) !!}
                                @php ++$i; @endphp
                            @endforeach

                            <!-- Country -->
                            @foreach (str_split($sorting->country_code) as $digit)
                                {!! Form::text('digits[' . ($k + $i) .']', $digit, ['class' => 'text-input number', 'readonly', 'disabled', 'maxlength' => 1]) !!}
                                @php ++$i; @endphp
                            @endforeach

                            <!-- Service Code -->
                            @foreach (str_split(str_pad($sorting->service_code, 11, '0', STR_PAD_LEFT)) as $digit)
                                {!! Form::text('digits[' . ($k + $i) .']', $digit, ['class' => 'text-input number', 'readonly', 'disabled', 'maxlength' => 1]) !!}
                                @php ++$i; @endphp
                            @endforeach

                            <!-- Gate Name -->
                            @foreach (str_split(str_pad('', 8, '0', STR_PAD_LEFT)) as $digit)
                                {!! Form::text('digits[' . ($k + $i) .']', null, ['class' => 'text-input number editable', 'maxlength' => 1]) !!}
                                @php ++$i; @endphp
                            @endforeach

                            <!-- Gate Number -->
                            @foreach (str_split(str_pad('', 2, '0', STR_PAD_LEFT)) as $digit)
                                {!! Form::text('digits[' . ($k + $i) .']', null, ['class' => 'text-input number editable', 'maxlength' => 1]) !!}
                                @php ++$i; @endphp
                            @endforeach
                        </div>
                        <div style="display: flex;">
                            <span class="text-center" style="width: 15.8%; border-top: 2px solid gray; margin-left: 0.1%; margin-right: 0.1%; margin-top: 2px; padding-top: 3px;"><span>Continent + Country</span></span>
                            <span class="text-center" style="width: 43.8%; border-top: 2px solid gray; margin-left: 0.1%; margin-right: 0.1%; margin-top: 2px; padding-top: 3px;"><span>Solution Code</span></span>
                            <span class="text-center" style="width: 31.8%; border-top: 2px solid gray; margin-left: 0.1%; margin-right: 0.1%; margin-top: 2px; padding-top: 3px;"><span style="font-family: 'Avenir Heavy', sans-serif;">Gate Name</span></span>
                            <span class="text-center" style="width: 7.8%; border-top: 2px solid gray; margin-left: 0.1%; margin-right: 0.1%; margin-top: 2px; padding-top: 3px;"><span style="font-family: 'Avenir Heavy', sans-serif;">Gate #</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            @foreach($sorting->sortingTypes as $sortingType)
                @if($sortingType->get_sorting_type)
                    @include("sortings.gates.partials.". str_replace(' ', '_', strtolower($sortingType->get_sorting_type)))
                @endif
            @endforeach

            {!! Form::submit(null, ['class'=> 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>

@stop

@section('inline_scripts')
    <script>
        jQuery(function ($) {
            $('.country_id').select2({
                placeholder: "-- Select country --",
                allowClear: true,
                width: '100%'
            });

            $('.region_id').select2({
                placeholder: "Region",
                allowClear: true,
                width: '100%'
            });

            $('.state_id').select2({
                placeholder: "City",
                allowClear: true,
                width: '100%'
            });

            $('.city_id').select2({
                placeholder: "City",
                allowClear: true,
                width: '100%'
            });

        });
    </script>
@append

@section('inline_styles')
    <style>
        span.number, input.number {
            font-family: Courier, sans-serif;
            font-size: 2.6em;
            width: 4%;
            position: relative;
            display: inline-block;
            text-align: center;
            border: 1px solid silver;
            margin: 0.1%;
            padding: 0;
        }

        input.number.editable {
            background-color: lightyellow;
        }

        .action-arrows input {
            margin-top: 5px;
        }

        .action-arrows {
            padding-top: 20px;
        }

        .lavel-top {
            padding-top: 10px;
        }
    </style>
@append