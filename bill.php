<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $rates = [
        'residential' => 10.50,
        'commercial'  => 12.00,
    ];

    $name = trim($_POST['name']              ?? '');
    $prev = $_POST['prev_reading']            ?? '';
    $curr = $_POST['curr_reading']            ?? '';
    $type = strtolower($_POST['consumer_type'] ?? '');

    if (!$name || $prev === '' || $curr === '' || !isset($rates[$type])) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields correctly.']);
        exit;
    }

    if ((float)$curr < (float)$prev) {
        echo json_encode(['success' => false, 'message' => 'Current reading must be greater than previous reading.']);
        exit;
    }

    $consumption = (float)$curr - (float)$prev;
    $amount_due  = $consumption * $rates[$type];

    echo json_encode([
        'success'     => true,
        'name'        => htmlspecialchars($name),
        'type'        => ucfirst($type),
        'consumption' => round($consumption, 2),
        'rate'        => $rates[$type],
        'amount_due'  => round($amount_due, 2),
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electric Bill Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        h1 { font-weight: 700; color: #212529; }
        .card { border: 1px solid #dee2e6; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        label { font-weight: 500; color: #495057; }
        .form-control, .form-select { border-radius: 8px; border-color: #ced4da; }
        .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15); }
        .btn-primary { border-radius: 8px; font-weight: 600; padding: 0.6rem 1.5rem; }
        #result { margin-top: 1.25rem; }
        .result-card { border-radius: 10px; border: 1px solid #c3e6cb; background-color: #f0fff4; padding: 1.25rem 1.5rem; animation: fadeIn 0.25s ease; }
        .result-card h5 { font-weight: 700; color: #212529; margin-bottom: 0.75rem; }
        .result-row { display: flex; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #e2e8e0; font-size: 0.95rem; color: #495057; }
        .result-row:last-of-type { border-bottom: none; }
        .result-row span:last-child { font-weight: 600; color: #212529; }
        .amount-row { display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 2px solid #c3e6cb; }
        .amount-row .label { font-weight: 700; font-size: 1rem; color: #212529; }
        .amount-row .value { font-size: 1.5rem; font-weight: 700; color: #198754; }
        .alert-danger { border-radius: 8px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
<div class="container mt-5 mb-5" style="max-width: 520px;">
    <h1 class="text-center mb-1">Electric Bill Calculator</h1>
    <p class="text-center text-muted mb-4">Eco-Friendly Electric Bill App</p>

    <div class="card p-4">
        <div class="mb-3">
            <label for="name" class="form-label">Consumer Name</label>
            <input type="text" id="name" class="form-control" placeholder="Enter name">
        </div>

        <div class="mb-3">
            <label for="prev" class="form-label">Previous Reading (kWh)</label>
            <input type="number" id="prev" class="form-control" placeholder="Enter kWh" min="0">
        </div>

        <div class="mb-3">
            <label for="curr" class="form-label">Current Reading (kWh)</label>
            <input type="number" id="curr" class="form-control" placeholder="Enter kWh" min="0">
        </div>

        <div class="mb-3">
            <label for="customerType" class="form-label">Consumer Type</label>
            <select id="customerType" class="form-select">
                <option value="residential">Residential</option>
                <option value="commercial">Commercial</option>
            </select>
        </div>

        <div class="d-grid">
            <button onclick="calculate()" class="btn btn-primary">Calculate Bill</button>
        </div>

        <div id="result"></div>
    </div>
</div>

<script>
    function calculate() {
        const name = document.getElementById('name').value.trim();
        const prev = document.getElementById('prev').value;
        const curr = document.getElementById('curr').value;
        const type = document.getElementById('customerType').value;
        const resultDiv = document.getElementById('result');

        if (!name || prev === '' || curr === '') {
            resultDiv.innerHTML = `<div class="alert alert-danger text-center mt-3">Please fill in all fields.</div>`;
            return;
        }

        const data = new URLSearchParams({
            name:          name,
            prev_reading:  prev,
            curr_reading:  curr,
            consumer_type: type,
        });

        fetch('bill.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data
        })
        .then(res => res.json())
        .then(json => {
            if (json.success) {
                resultDiv.innerHTML = `
                    <div class="result-card mt-3">
                        <h5>Bill Summary for ${json.name}</h5>
                        <div class="result-row">
                            <span>Consumer Type</span>
                            <span>${json.type}</span>
                        </div>
                        <div class="result-row">
                            <span>Consumption</span>
                            <span>${json.consumption.toFixed(2)} kWh</span>
                        </div>
                        <div class="result-row">
                            <span>Rate per kWh</span>
                            <span>&#8369;${json.rate.toFixed(2)}</span>
                        </div>
                        <div class="amount-row">
                            <span class="label">Amount Due</span>
                            <span class="value">&#8369;${json.amount_due.toFixed(2)}</span>
                        </div>
                    </div>`;
            } else {
                resultDiv.innerHTML = `<div class="alert alert-danger text-center mt-3">${json.message}</div>`;
            }
        })
        .catch(() => {
            resultDiv.innerHTML = `<div class="alert alert-danger text-center mt-3">Something went wrong. Make sure you're running this on a PHP server.</div>`;
        });
    }
</script>
</body>
</html>
