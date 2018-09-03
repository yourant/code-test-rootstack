@if ($package)
    {!! Form::open(['route' => 'search', 'class' => 'search-form form', 'role' => 'search', 'method' => 'GET']) !!}
    <div class="input-group">
        {!! Form::text('tracking', $package->tracking_number, ['class' => 'form-control text-center', 'placeholder' => 'Enter Tracking Number ...']) !!}
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="fa fa-fw fa-search"></i></button>
        </span>
    </div>
    {!! Form::close() !!}
@endif
