<!-- Title Field -->
<div class="col-sm-12">
    {!! Form::label('title', 'Title:') !!}
    <p>{{ $book->title }}</p>
</div>

<!-- Print Date Field -->
<div class="col-sm-12">
    {!! Form::label('print_date', 'Print Date:') !!}
    <p>{{ $book->print_date }}</p>
</div>

<!-- Unit Cost Field -->
<div class="col-sm-12">
    {!! Form::label('unit_cost', 'Unit Cost:') !!}
    <p>{{ $book->unit_cost }}</p>
</div>

<!-- Isbn Field -->
<div class="col-sm-12">
    {!! Form::label('isbn', 'Isbn:') !!}
    <p>{{ $book->isbn }}</p>
</div>

