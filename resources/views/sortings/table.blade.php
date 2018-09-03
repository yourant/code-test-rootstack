<table class="table table-hover table-stripped table-condensed">
    <thead>
    <tr>
        <th>Name</th>
        <th>Service</th>
        <th>Country</th>
        <th>Sorting Types</th>
        <th>Number of Gates</th>
        <th class="text-right" >Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->service_code }}</td>
            <td>{{ $item->country_name }}</td>
            <td>{{ $item->sorting_types }}</td>
            <td>{{ $item->gate_count }}</td>
            <td class="text-right">
                <div class="btn-group">
                    <a class="btn btn-default btn-flat btn-sm" href="{{ route('sortings.gates.index', $item->id) }}">Define Gates</a>
                    <a class="btn btn-default btn-flat btn-sm" href="{{ route('sortings.edit', $item->id) }}"><i class="fa fa-fw fa-pencil"></i></a>
                    <a class="btn btn-default btn-flat btn-sm" href="{{ route('sortings.destroy', $item->id) }}" data-confirm="Are you sure?" data-method="DELETE"><i class="fa fa-fw fa-trash-o"></i></a>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6">No records found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

{!! $items->appends($params)->render() !!}

