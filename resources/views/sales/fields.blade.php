<!-- Book Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('book_id', 'Book Title:') !!}
    {!! Form::select('book_id', $books, null, ['class' => 'form-control', 'placeholder' => 'Select Book', 'required']) !!}
</div>

<!-- Customer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customer_id', 'Customer Name:') !!}
    {!! Form::select('customer_id', $customers, null, ['class' => 'form-control', 'placeholder' => 'Select Customer', 'required']) !!}
</div>

<!-- Quantity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('quantity', 'Quantity:') !!}
    {!! Form::number('quantity', null, ['class' => 'form-control', 'required', 'id' => 'quantity']) !!}
</div>

<!-- Unit Price Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unit_price', 'Unit Price:') !!}
    {!! Form::number('unit_price', null, ['class' => 'form-control', 'required', 'id' => 'unit_price']) !!}
</div>

<!-- Total Field -->
<div class="form-group col-sm-6">
    {!! Form::label('total', 'Total:') !!}
    {!! Form::number('total', null, ['class' => 'form-control', 'readonly' => true, 'id' => 'total']) !!}
</div>

<!-- Amount Paid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('amount_paid', 'Amount Paid:') !!}
    {!! Form::number('amount_paid', null, ['class' => 'form-control', 'required', 'id' => 'amount_paid']) !!}
</div>
<!-- Balance Due Field -->
<div class="form-group col-sm-6">
    {!! Form::label('balance_due', 'Balance Due:') !!}
    {!! Form::number('balance_due', null, ['class' => 'form-control', 'readonly' => true, 'id' => 'balance_due']) !!}
</div>

<!-- Payment Status (Auto-updated but still submitted) -->
<div class="form-group col-sm-6">
    {!! Form::label('payment_status', 'Payment Status:') !!}
    {!! Form::text('payment_status', null, ['class' => 'form-control', 'readonly', 'id' => 'payment_status']) !!}
</div>

<script>
    function updatePaymentStatus() {
        const total = parseFloat(document.getElementById('total').value) || 0;
        const paid = parseFloat(document.getElementById('amount_paid').value) || 0;
        let status = 'Unpaid';

        if (paid >= total) {
            status = 'Paid';
        } else if (paid > 0 && paid < total) {
            status = 'Partially Paid';
        }

        document.getElementById('payment_status').value = status;
    }

    document.getElementById('quantity').addEventListener('input', () => {
        calculateTotal();
        updatePaymentStatus();
    });

    document.getElementById('unit_price').addEventListener('input', () => {
        calculateTotal();
        updatePaymentStatus();
    });

    document.getElementById('amount_paid').addEventListener('input', updatePaymentStatus);
</script>


<script>
    function calculateTotal() {
        let quantity = parseFloat(document.getElementById('quantity').value) || 0;
        let unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
        document.getElementById('total').value = (quantity * unitPrice).toFixed(2);
    }

    document.getElementById('quantity').addEventListener('input', calculateTotal);
    document.getElementById('unit_price').addEventListener('input', calculateTotal);
</script>


<script>
    function calculateBalanceDue() {
        let total = parseFloat(document.getElementById('total')?.value || 0);
        let amountPaid = parseFloat(document.getElementById('amount_paid')?.value || 0);
        let balanceDue = total - amountPaid;
        document.getElementById('balance_due').value = balanceDue.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('total')?.addEventListener('input', calculateBalanceDue);
        document.getElementById('amount_paid')?.addEventListener('input', calculateBalanceDue);

        // Calculate on load
        calculateBalanceDue();
    });
</script>

