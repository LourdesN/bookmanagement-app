{!! Form::open(['route' => ['books.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group' role="group" aria-label="Book Actions">
    <a href="{{ route('books.show', $id) }}" class='btn btn-outline-primary btn-sm' title="View">
        <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('books.edit', $id) }}" class='btn btn-outline-warning btn-sm' title="Edit">
        <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-outline-danger btn-sm',
        'title' => 'Delete',
        'onclick' => 'return confirm("Are you sure you want to delete this book?")'
    ]) !!}
</div>
{!! Form::close() !!}

