<!DOCTYPE html>
<html>
<head>
    <title>Test Receipt</title>
</head>
<body>
    <h1>TEST RECEIPT</h1>
    <p>Transaction ID: {{ $transaction->id }}</p>
    <p>Transaction Number: {{ $transaction->transaction_number }}</p>
    <p>Total: {{ $transaction->total_amount }}</p>
    <p>Items: {{ is_array($transaction->items) ? count($transaction->items) : 'No items' }}</p>
</body>
</html>
