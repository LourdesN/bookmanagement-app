{!! Form::open(['route' => ['deliveries.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('deliveries.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('deliveries.edit', $id) }}" class='btn btn-default btn-xs'>
        <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => 'return confirm("Are you sure you want to delete this Delivery?")'

    ]) !!}
</div>
{!! Form::close() !!}
