<div class="row">
    <div class="form-group col-md-12">
        <h3>{!! $sortingType->get_sorting_type !!}</h3>
        <div class="row">
            {!! Form::label('name', 'Defined Criteria:', ['class' => 'col-md-2 lavel-top']) !!}
            <div class="col-md-10">
                {!! Form::input('text', 'criteria', null, ['class'=>'form-control', 'placeholder'=>'', 'id' => 'code']) !!}
            </div>
        </div>
    </div>
</div>
