<!-- Title Field -->
<div class="form-group col-sm-6">
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title', null, ['class' => 'form-control', 'required', 'maxlength' => 500, 'maxlength' => 500]) !!}
</div>

<!-- Print Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('print_date', 'Print Date:') !!}
    {!! Form::date('print_date', null, ['class' => 'form-control', 'id' => 'print_date', 'max' => \Carbon\Carbon::now()->format('Y-m-d')]) !!}
</div>

@push('page_scripts')
<script type="text/javascript">
    $(function () {
        $('#print_date').datepicker({
            maxDate: new Date(),
            dateFormat: 'yy-mm-dd'
        });
    });
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

<!-- Description Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<!--reorder_level Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reorder_level', 'Reorder Level:') !!}
    {!! Form::number('reorder_level', null, ['class' => 'form-control']) !!}
</div>
