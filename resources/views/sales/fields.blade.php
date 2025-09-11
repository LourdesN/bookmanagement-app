<div class="row">
    <!-- Book -->
    <div class="form-group col-sm-6">
        {!! Form::label('book_id', 'Book:') !!}
        {!! Form::select('book_id', $books, old('book_id'), ['class' => 'form-control', 'id' => 'book_id', 'required']) !!}
        @error('book_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Customer -->
    <div class="form-group col-sm-6">
        {!! Form::label('customer_id', 'Customer:') !!}
        {!! Form::select('customer_id', $customers, old('customer_id'), ['class' => 'form-control', 'id' => 'customer_id', 'required']) !!}
        @error('customer_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Quantity -->
    <div class="form-group col-sm-6">
        {!! Form::label('quantity', 'Quantity:') !!}
        <input type="number" class="form-control" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required>
        @error('quantity')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Unit Price -->
    <div class="form-group col-sm-6">
        {!! Form::label('unit_price', 'Unit Price:') !!}
        <input type="number" class="form-control" name="unit_price" id="unit_price" value="{{ old('unit_price', '') }}" step="0.01" required>
        @error('unit_price')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Total (readonly) -->
    <div class="form-group col-sm-6">
        {!! Form::label('total', 'Total:') !!}
        <input type="text" class="form-control" name="total" id="total" value="{{ old('total', '0.00') }}" readonly>
        @error('total')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Amount Paid -->
    <div class="form-group col-sm-6">
        {!! Form::label('amount_paid', 'Amount Paid:') !!}
        <input type="number" class="form-control" name="amount_paid" id="amount_paid" value="{{ old('amount_paid', '0.00') }}" step="0.01" required>
        @error('amount_paid')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Balance Due (readonly) -->
    <div class="form-group col-sm-6">
        {!! Form::label('balance_due', 'Balance Due:') !!}
        <input type="text" class="form-control" name="balance_due" id="balance_due" value="{{ old('balance_due', '0.00') }}" readonly>
        @error('balance_due')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<script>
function calculateTotal() {
    let qty = parseFloat(document.getElementById("quantity").value) || 0;
    let unitPrice = parseFloat(document.getElementById("unit_price").value) || 0;
    let total = qty * unitPrice;
    document.getElementById("total").value = total.toFixed(2);
    calculateBalanceDue();
}

function calculateBalanceDue() {
    let total = parseFloat(document.getElementById("total").value) || 0;
    let amountPaid = parseFloat(document.getElementById("amount_paid").value) || 0;
    let balance = total - amountPaid;
    document.getElementById("balance_due").value = balance.toFixed(2);
}

// Auto-calc on input
document.getElementById("quantity").addEventListener("input", calculateTotal);
document.getElementById("unit_price").addEventListener("input", calculateTotal);
document.getElementById("amount_paid").addEventListener("input", calculateBalanceDue);

// Initial calculation in case old values exist
calculateTotal();
</script>
