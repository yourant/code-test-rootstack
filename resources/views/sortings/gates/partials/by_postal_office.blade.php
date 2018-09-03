<div class="row">
    <div class="form-group col-md-12">
        <h3>{!! $sortingType->get_sorting_type !!}</h3>
    </div>
</div>
<div class="row">
    {!! Form::label('country_id', 'Country', ['class' => 'col-md-1']) !!}
    <div class="col-md-2">
        {!! Form::select('country_id', ['' => ''] + $countries->pluck('name','id')->toArray(), null, ['class' => 'form-control country_id', 'id' => 'country-'.$sortingType->id, 'data-val' => $sortingType->id]) !!}
    </div>
<!--
    <div class="col-md-2">
        {!! Form::select('', [], null, ['class' => 'form-control region_id', 'id' => 'region-'.$sortingType->id, 'data-val' => $sortingType->id]) !!}
        </div>
        <div class="col-md-2">
{!! Form::select('', [], null, ['class' => 'form-control state_id', 'id' => 'state-'.$sortingType->id, 'data-val' => $sortingType->id]) !!}
        </div>-->
</div>
<br>
<div class="row">
    <div class="form-group col-md-5">
        {!! Form::label('', 'Select Postal Offices:') !!}
        {!! Form::select('', [], null, ['id' => 'postal_office_list_1', 'class'=>'form-control', 'style' => 'width: 100%;min-height: 200px;','multiple' => 'multiple']) !!}
        <div class="spinner"></div>
    </div>

    <div class="form-group col-md-2 text-center action-arrows">
        <input type="button" id="btnRightPostalOffice" value="Add -->>" class="btn btn-default btn-block" /><br />
        <input type="button" id="btnLeftPostalOffice" value="<<-- Remove" class="btn btn-default btn-block" /><br />
    </div>

    <div class="form-group col-md-5">
        {!! Form::label('', 'Selected Postal Offices:') !!}
        {!! Form::select('postal_office_id[]', [], null, ['id' => 'postal_office_list_2', 'class'=>'form-control', 'style' => 'width: 100%;min-height: 200px;','multiple' => 'multiple']) !!}
    </div>
</div>

@section('inline_scripts')
    <script>
        $(document).ready(function () {
            $('#btnRightPostalOffice').click(function(e){
                var selectedOptions = $('#postal_office_list_1 option:selected');
                $('#postal_office_list_2').append($(selectedOptions).clone());
                $("#postal_office_list_2").find("option").each(function() {
                    $(this).attr('selected', true);
                });
                $(selectedOptions).remove();
                e.preventDefault();
            });

            $('#btnLeftPostalOffice').click(function(e){
                var selectedOptions = $('#postal_office_list_2 option:selected');
                $('#postal_office_list_1').append($(selectedOptions).clone());
                $(selectedOptions).remove();
                $("#postal_office_list_2 option").prop("selected",true);
                e.preventDefault();
            });

            var country_selector = 'country-{{$sortingType->id}}';
            $("#"+country_selector).change(function () {
                var dataVal = $(this).attr('data-val');
                var country_id = $(this).val();
                if($(this).val()==''){
                    $('#region-'+dataVal).html('');
                } else {
                    $("#postal_office_list_1").html('');
                    $("#postal_office_list_2").html('');
                    $(".spinner").spin({scale: 0.5});
                    $.ajax({
                        url:"{{route('locations.get_postal_offices')}}?country_id="+country_id,
                        method: "GET",
                        success: function (response) {
                            if(response.data){
                                for(var i=0; i<response.data.length;i++){
                                    $("#postal_office_list_1").append('<option value="'+response.data[i].id+'">'+response.data[i].name+'</option>');
                                }
                            }
                            $(".spinner").spin(false);
                        }
                    });
                }
            });
        });
    </script>
@append
