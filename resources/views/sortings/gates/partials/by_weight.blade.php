<div class="row">
    <div class="form-group col-md-12">
        {!! Form::label('name', $sortingType->get_sorting_type) !!}
        <div class="row">
            {!! Form::label('name', 'After Then:', ['class' => 'col-md-2 lavel-top']) !!}
            <div class="col-md-4">
                {!! Form::input('text', 'after_weight', null, ['class'=>'form-control', 'placeholder'=>'', 'id' => 'code']) !!}
            </div>
            {!! Form::label('name', 'Before Then:', ['class' => 'col-md-2 lavel-top']) !!}
            <div class="col-md-4">
                {!! Form::input('text', 'before_weight', null, ['class'=>'form-control', 'placeholder'=>'', 'id' => 'code']) !!}
            </div>
        </div>
    </div>
</div>
