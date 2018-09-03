<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Services</h4>
            </div>
            <form action="" method="POST" id="add-service-form">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group col-md-12">
                            @if($servicesAvailables->count() > 0)
                                {!! Form::select('service_id', ['' => ''] + $servicesAvailables->pluck('code','id')->toArray(), null, ['id' => 'service', 'class'=>'form-control', 'style' => 'width: 100%;']) !!}
                            @else
                                There is not services availables.
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    @if($servicesAvailables->count() > 0)
                        <button type="submit" class="btn btn-primary">Add Service</button>
                    @endif
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>

@section('inline_scripts')
    <script>
        jQuery(function ($) {
            $("#service").select2({
                placeholder: "-- Select Service --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@append