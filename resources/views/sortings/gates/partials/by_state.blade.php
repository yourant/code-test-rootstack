<div class="row">
    <div class="form-group col-md-12">
        {!! Form::label('name', $sortingType->get_sorting_type) !!}
    </div>
</div>
<div class="row">
    {!! Form::label('country_id', 'Select', ['class' => 'col-md-1']) !!}
    <div class="col-md-2">
        {!! Form::select('', ['' => ''] + $countries->pluck('name','id')->toArray(), null, ['class' => 'form-control country_id', 'id' => 'country-'.$sortingType->id, 'data-val' => $sortingType->id]) !!}
    </div>
<!--
    <div class="col-md-2">
        {!! Form::select('', [], null, ['class' => 'form-control region_id', 'id' => 'region-'.$sortingType->id, 'data-val' => $sortingType->id]) !!}
        </div>-->
</div>
<br>
<div class="row">
    <div class="form-group col-md-5">
        {!! Form::label('', 'Select States:') !!}
        {!! Form::select('', [], null, ['id' => 'state_list_1', 'class'=>'form-control', 'style' => 'width: 100%;min-height: 200px;','multiple' => 'multiple']) !!}
    </div>

    <div class="form-group col-md-2 text-center action-arrows">
        <input type="button" id="btnRightState" value="Add-->>" class="btn btn-default" /><br />
        <input type="button" id="btnLeftState" value="<<-- Remove" class="btn btn-default" /><br />
    </div>

    <div class="form-group col-md-5">
        {!! Form::label('', 'Selected States:') !!}
        {!! Form::select('state_id[]', [], null, ['id' => 'state_list_2', 'class'=>'form-control', 'style' => 'width: 100%;min-height: 200px;','multiple' => 'multiple']) !!}
    </div>
</div>

@section('inline_scripts')
    <script>
        $(document).ready(function () {
            $('#btnRightState').click(function(e){
                var selectedOptions = $('#state_list_1 option:selected');
                $('#state_list_2').append($(selectedOptions).clone());
                $("#state_list_2").find("option").each(function() {
                    $(this).attr('selected', true);
                });
                $(selectedOptions).remove();
                e.preventDefault();
            });

            $('#btnLeftState').click(function(e){
                var selectedOptions = $('#state_list_2 option:selected');
                $('#state_list_1').append($(selectedOptions).clone());
                $(selectedOptions).remove();
                $("#state_list_2 option").prop("selected",true);
                e.preventDefault();
            });

            var country_selector = 'country-{{$sortingType->id}}';
            $("#"+country_selector).change(function () {
                var dataVal = $(this).attr('data-val');
                var country_id = $(this).val();
                if($(this).val()==''){
                    $('#region-'+dataVal).html('');
                } else {
                    $("#state_list_1").html('');
                    $("#state_list_2").html('');
                    $.ajax({
                        url:"{{route('locations.get_states')}}?country_id="+country_id,
                        method: "GET",
                        success: function (response) {
                            if(response.data){
                                for(var i=0; i<response.data.length;i++){
                                    $("#state_list_1").append('<option value="'+response.data[i].id+'">'+response.data[i].name+'</option>');
                                }
                            }
                        }
                    });
                }
            });
        });
    </script>
@append
