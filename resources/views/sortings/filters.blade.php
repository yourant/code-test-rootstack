<div class="filters">
    <form id="filters" role="form">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::select('country_id[]', $countries->pluck('name', 'id')->toArray(), isset($params['country_id']) ? $params['country_id'] : null, ['class' => 'form-control countries', 'multiple' => 'multiple']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::select('service_id[]', $services->pluck('code', 'id')->toArray(), isset($params['service_id']) ? $params['service_id'] : null, ['class' => 'form-control services', 'multiple' => 'multiple']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::select('sorting_type_id[]', $sortingTypes->pluck('name', 'id')->toArray(), isset($params['sorting_type_id']) ? $params['sorting_type_id'] : null, ['class' => 'form-control sorting_types', 'multiple' => 'multiple']) !!}
                </div>
            </div>
        </div>

        <p class="actions">
            <span class="m-r-sm">
                <button class="btn btn-info btn-sm" type="submit">Filter</button>
            </span>
            Showing <strong>{{ $items->count() }}</strong> results.
        </p>

    </form>
</div>

@section('inline_styles')
<style>
    .filters select.form-control { width: 100%;}
    .filters .row div[class*="col-"] { padding-left: 5px; padding-right: 5px;}
    .filters .row > div:first-child { padding-left: 15px !important; }
    .filters .row > div:last-child { padding-right: 15px !important; }
    .filters .input-daterange .form-control {border-radius: 0;}
    .filters .input-group-addon {
        font-size: 12px;
        width: auto;
        min-width: 16px;
        padding: 4px 5px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        text-shadow: 0 1px 0 #FFF;
        vertical-align: middle;
        background-color: #EEE;
        border: solid #E5E6E7;
        border-width: 1px 0;
        margin-left: -5px;
        margin-right: -5px;
    }
    .filters .form-group { margin-bottom: 10px;}
    .filters .actions {line-height: 35px; margin-bottom: 0;}
    .filters .form-group label {
        font-weight: 300;
        line-height: 25px;
        margin: 0;
        margin-right: 5px;
        font-size: 12px;
    }
    .filters .subgroup {
        border-right: 1px solid #EEEEEE;
    }
    .filters .subgroup {
        padding: 0 10px;
        box-shadow: 1px 1px 0px 0px rgba(0,0,0,0.1);
        margin-bottom: 5px;
    }
    .filters .subgroup:nth-child(odd) {
        background-color: #f7f7f7;
    }
    .filters .subgroup:nth-child(even) {
        background-color: #fff6dd;
    }
</style>
@append

@section('inline_scripts')
<script>
    $(document).ready(function () {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        $('.filters select.countries').select2({
            placeholder: "-- Select a Country",
            allowClear: true,
            width: '100%'
        });

        $('.filters select.services').select2({
            placeholder: "-- Select a Service",
            allowClear: true,
            width: '100%'
        });

        $('.filters select.sorting_types').select2({
            placeholder: "-- Select a Sorting Types",
            allowClear: true,
            width: '100%'
        });

    });
</script>
@append