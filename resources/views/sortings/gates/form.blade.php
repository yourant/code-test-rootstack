<div class="ibox-content">
    <div class="row">
        <div class="form-group col-md-4">
            {!! Form::label('name', 'Gate Code') !!}
            {!! Form::input('text', 'code', null, ['class'=>'form-control', 'placeholder'=>'', 'id' => 'code']) !!}
            {{--<p class="text-help">Ex: DURANGO</p>--}}
        </div>
    </div>

    <hr>

    @foreach($sorting->sortingTypes as $sortingType)
        @if($sortingType->get_sorting_type)
            @include("sortings.gates.partials.". str_replace(' ', '_', strtolower($sortingType->get_sorting_type)))
        @endif
    @endforeach

    {!! Form::submit(null, ['class'=> 'btn btn-primary']) !!}
</div>

@section('inline_scripts')
    <script>
        jQuery(function($) {
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
        .action-arrows input {
            margin-top: 5px;
        }
        .action-arrows {
            padding-top: 20px;
        }
        .lavel-top{
            padding-top: 10px;
        }
    </style>
@append