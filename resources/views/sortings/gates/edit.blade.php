@extends('layouts.admin.page')

@section('page_title') Edit Gate @stop

@section('breadcrumbs')
<ol class="breadcrumb">
    <li><a href="{!! route('root') !!}">Home</a></li>
    <li><a href="{!! route('sortings.index') !!}">Sorting</a></li>
    <li><a href="{!! route('sortings.gates.index', $sorting->id) !!}">Gates</a></li>
    <li>Edit</li>
</ol>
@stop

@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="ibox">
            {!! Form::model($sortingGate, ['route' => ['sortings.gates.update', $sorting->id, $sortingGate->id], 'method' => 'PUT']) !!}
            <div class="ibox-content">
                <div class="row">
                    <div class="form-group col-md-12">
                        {!! Form::label('code', 'Code') !!}
                        {!! Form::input('text', 'code', $sortingGate->gate_code, ['class'=>'form-control', 'placeholder'=>'', 'id' => 'code']) !!}
                    </div>
                    <div class="form-group col-md-12">
                        {!! Form::label('number', 'Number') !!}
                        {!! Form::input('text', 'number', $sortingGate->gate_number, ['class'=>'form-control', 'placeholder'=>'', 'id' => 'number']) !!}
                    </div>
                </div>
                {!! Form::submit(null, ['class'=> 'btn btn-primary btn-lg']) !!}
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

@stop
