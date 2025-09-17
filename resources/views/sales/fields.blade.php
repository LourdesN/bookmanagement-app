<div class="row">
    <!-- Book -->
    <div class="form-group col-sm-6">
        {!! Form::label('book_id', 'Book:') !!}
        {!! Form::select('book_id', $books, null, ['class' => 'form-control', 'placeholder' => 'Select a book', 'id' => 'book_id', 'required']) !!}
    </div>

    <!-- Customer -->
    <div class="form-group col-sm-6">
        {!! Form::label('customer_id', 'Customer:') !!}
        {!! Form::select('customer_id', $customers, null, ['class' => 'form-control', 'placeholder' => 'Select a Customer', 'id' => 'customer_id', 'required']) !!}
    </div>

    <!-- Quantity -->
    <div class="form-group col-sm-6">
        {!! Form::label('quantity', 'Quantity:') !!}
        <input type="number" class="form-control" name="quantity" id="quantity" value="1" min="1" required>
    </div>

    <!-- Unit Price (readonly) -->
    <div class="form-group col-sm-6">
        {!! Form::label('unit_price', 'Unit Price:') !!}
        <input type="number" class="form-control" name="unit_price" id="unit_price" step="0.01" readonly required>
    </div>

    <!-- Total (readonly) -->
    <div class="form-group col-sm-6">
        {!! Form::label('total', 'Total:') !!}
        <input type="number" class="form-control" name="total" id="total" step="0.01" readonly required>
    </div>

    <!-- Amount Paid -->
    <div class="form-group col-sm-6">
        {!! Form::label('amount_paid', 'Amount Paid:') !!}
        <input type="number" class="form-control" name="amount_paid" id="amount_paid" step="0.01" value="0" required>
    </div>

    <!-- Balance Due (readonly) -->
    <div class="form-group col-sm-6">
        {!! Form::label('balance_due', 'Balance Due:') !!}
        <input type="number" class="form-control" name="balance_due" id="balance_due" step="0.01" readonly>
    </div> 
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const booksData = JSON.parse('{!! json_encode($booksData) !!}');

    const bookSelect = document.getElementById("book_id");
    const unitPriceInput = document.getElementById("unit_price");
    const quantityInput = document.getElementById("quantity");
    const totalInput = document.getElementById("total");
    const amountPaidInput = document.getElementById("amount_paid");
    const balanceDueInput = document.getElementById("balance_due");

    function calculateTotal() {
        const qty = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(unitPriceInput.value) || 0;
        totalInput.value = (qty * price).toFixed(2);
        calculateBalanceDue();
    }

    function calculateBalanceDue() {
        const total = parseFloat(totalInput.value) || 0;
        const paid = parseFloat(amountPaidInput.value) || 0;
        balanceDueInput.value = Math.max(total - paid, 0).toFixed(2);
    }

    // Update unit price when book changes
    bookSelect.addEventListener("change", () => {
        const bookId = bookSelect.value;
        unitPriceInput.value = booksData[bookId] ?? 0;
        calculateTotal();
    });

    // Recalculate totals
    quantityInput.addEventListener("input", calculateTotal);
    amountPaidInput.addEventListener("input", calculateBalanceDue);

    // Initialize on page load
    if (bookSelect.value) {
        unitPriceInput.value = booksData[bookSelect.value] ?? 0;
        calculateTotal();
    }
});
</script>