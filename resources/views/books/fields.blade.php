<!-- Title Field -->
<div class="form-group col-sm-6">
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title', null, ['class' => 'form-control', 'required', 'maxlength' => 500, 'maxlength' => 500]) !!}
</div>

<!-- Print Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('print_date', 'Print Date:') !!}
    {!! Form::text('print_date', null, ['class' => 'form-control','id'=>'print_date']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#print_date').datepicker()
    </script>
@endpush

<!-- Unit Cost Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unit_cost', 'Unit Cost:') !!}
    {!! Form::number('unit_cost', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Isbn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isbn', 'Isbn:') !!}
    {!! Form::number('isbn', null, ['class' => 'form-control']) !!}
</div>