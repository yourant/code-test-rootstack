<div class="ibox-content">
    <div class="form-group">
        {!! Form::label('name', 'Sorting Name') !!}
        {!! Form::input('text', 'name', null, ['class'=>'form-control', 'placeholder'=>'', 'id' => 'name']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('service_id', 'Service') !!}
        {!! Form::select('service_id', $services->pluck('code', 'id'), null, ['class'=>'form-control', 'style' => 'width: 100%;']) !!}
    </div>

    @if(!isset($edit))
        <div class="form-group">
            {!! Form::label('sorting_type_id[]', 'Sorting Types') !!}
            @foreach ($sortingTypes as $sortingType)
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="sorting_type_id[{!! $sortingType->id !!}]" value="1"> {!! $sortingType->description !!}
                    </label>
                </div>
            @endforeach
        </div>


        <div class="row">
            <div class="form-group col-md-5">
                {!! Form::label('sorting_type_id[]', 'Sorting Types') !!}
                {!! Form::select('sorting_type_id[]', $sortingTypes, null, ['id' => 'sorting_type_list_1', 'class'=>'form-control', 'style' => 'width: 100%;min-height: 200px;','multiple' => 'multiple']) !!}
            </div>

            <div class="form-group col-md-2 text-center action-arrows m-t-lg">
                <input type="button" id="btnRight" value="Add -->>" class="btn btn-default btn-block"/><br/>
                <input type="button" id="btnLeft" value="<<-- Remove" class="btn btn-default btn-block"/><br/>
            </div>

            <div class="form-group col-md-5">
                {!! Form::label('', 'Selected items') !!}
                {!! Form::select('sorting_types[]', [], null, ['id' => 'sorting_type_list_2', 'class'=>'form-control', 'style' => 'width: 100%;min-height: 200px;','multiple' => 'multiple']) !!}
            </div>
        </div>
    @endif

    {!! Form::submit(null, ['class'=> 'btn btn-primary']) !!}
</div>
