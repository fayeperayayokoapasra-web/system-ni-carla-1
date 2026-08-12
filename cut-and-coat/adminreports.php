<?php
include 'functions/adminreports_logic.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Reports</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">
<link rel="stylesheet" href="assets/css/adminreports.css">
</head>

<body>

<div class="topbar">
<h3>Cut & Coat Nail Salon</h3>
</div>

<?php include 'sidebar.php'; ?>

<div class="main" id="main">

<div class="title">Sales Analytics & Customer Records</div>

<div class="report-controls">
    <form method="GET" class="report-form">
        <div class="field">
            <label for="report_date">Daily Report</label>
            <input type="date" id="report_date" name="report_date" value="<?php echo htmlspecialchars($reportDate, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit" class="btn">Update Daily</button>
    </form>
</div>

<div class="report-controls">
    <form method="GET" class="report-form">
        <div class="field">
            <label for="report_month">Monthly Report</label>
            <input type="month" id="report_month" name="report_month" value="<?php echo htmlspecialchars($reportMonth, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit" class="btn">Update Monthly</button>
    </form>
</div>

<div class="section">
    <div class="section-header">
        <div class="section-title">Daily Report</div>
        <div class="section-subtitle"><?php echo htmlspecialchars($reportDate, ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
    <div class="grid">
        <div class="card">
            <h2>₱<?php echo number_format($dailyTotals['total'], 2); ?></h2>
            <p>Daily Sales</p>
        </div>
        <div class="card">
            <h2>₱<?php echo number_format($dailyTotals['online'], 2); ?></h2>
            <p>Online Daily Sales</p>
        </div>
        <div class="card">
            <h2>₱<?php echo number_format($dailyTotals['walkin'], 2); ?></h2>
            <p>Walk-In Daily Sales</p>
        </div>
    </div>
    <div class="chart-card">
        <canvas id="dailyChart"></canvas>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <div class="section-title">Monthly Report</div>
        <div class="section-subtitle"><?php echo htmlspecialchars($reportMonthLabel, ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
    <div class="grid">
        <div class="card">
            <h2>₱<?php echo number_format($monthlyTotals['total'], 2); ?></h2>
            <p>Monthly Sales</p>
        </div>
        <div class="card">
            <h2>₱<?php echo number_format($monthlyTotals['online'], 2); ?></h2>
            <p>Online Sales</p>
        </div>
        <div class="card">
            <h2>₱<?php echo number_format($monthlyTotals['walkin'], 2); ?></h2>
            <p>Walk-In Sales</p>
        </div>
    </div>
    <div class="chart-card">
        <canvas id="monthlyChart"></canvas>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <div class="section-title">Daily Sales Records</div>
        <button type="button" class="toggle-button" data-target="dailyRecordsPanel">Show records</button>
    </div>
    <div id="dailyRecordsPanel" class="records-panel collapsed">
        <div class="section-subtitle"><?php echo htmlspecialchars($reportDate, ENT_QUOTES, 'UTF-8'); ?></div>
        <table>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Payment</th>
                <th>Amount</th>
            </tr>
            <?php if(empty($dailyRecords)): ?>
                <tr><td colspan="4">No sales data for this date.</td></tr>
            <?php else: ?>
                <?php foreach($dailyRecords as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($record['type'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($record['payment'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>₱<?php echo number_format($record['amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <div class="section-title">Monthly Sales Records</div>
        <button type="button" class="toggle-button" data-target="monthlyRecordsPanel">Show records</button>
    </div>
    <div id="monthlyRecordsPanel" class="records-panel collapsed">
        <div class="section-subtitle"><?php echo htmlspecialchars($reportMonthLabel, ENT_QUOTES, 'UTF-8'); ?></div>
        <table>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Payment</th>
                <th>Amount</th>
            </tr>
            <?php if(empty($monthlyRecords)): ?>
                <tr><td colspan="4">No sales data for this month.</td></tr>
            <?php else: ?>
                <?php foreach($monthlyRecords as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($record['type'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($record['payment'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>₱<?php echo number_format($record['amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <div class="section-title">Customer Records</div>
        <button type="button" class="toggle-button" data-target="customerRecordsPanel">Show records</button>
    </div>
    <div class="customer-summary">
        <div><strong>Total customer records:</strong> <?php echo number_format($customerCount); ?></div>
        <div><strong>Customers today:</strong> <?php echo number_format($customerCountToday); ?></div>
        <div><strong>Customers this month:</strong> <?php echo number_format($customerCountMonth); ?></div>
    </div>
    <div id="customerRecordsPanel" class="records-panel collapsed">
        <div class="table-container">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Payment</th>
                </tr>
                <?php if(empty($recentCustomers)): ?>
                    <tr><td colspan="5">No customer records found.</td></tr>
                <?php else: ?>
                    <?php foreach($recentCustomers as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer['service'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer['datetime'] ?? $customer['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer['payment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dailyChart = document.getElementById('dailyChart').getContext('2d');
const monthlyChart = document.getElementById('monthlyChart').getContext('2d');

new Chart(dailyChart, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($dailyChartLabels); ?>,
        datasets: [{
            label: 'Daily Sales Breakdown',
            data: <?php echo json_encode($dailyChartValues); ?>,
            backgroundColor: ['#10b981', '#0284c7', '#a855f7', '#f97316'],
            borderColor: '#ffffff',
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                top: 0,
                bottom: 0,
                left: 0,
                right: 0
            }
        },
        plugins: {
            legend: {
                position: 'bottom'
            },
            title: {
                display: true,
                text: 'Daily Payment Breakdown',
                padding: {
                    top: 8,
                    bottom: 8
                }
            }
        }
    }
});

new Chart(monthlyChart, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($monthlyChartLabels); ?>,
        datasets: [{
            label: 'Sales',
            data: <?php echo json_encode($monthlyChartValues); ?>,
            backgroundColor: '#10b981',
            borderColor: '#047857',
            borderWidth: 1,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: {
                top: 0,
                bottom: 0,
                left: 0,
                right: 0
            }
        },
        scales: {
            x: { title: { display: true, text: 'Day' } },
            y: { title: { display: true, text: 'Sales (₱)' }, beginAtZero: true }
        },
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Daily Sales Trend for the Month',
                padding: {
                    top: 8,
                    bottom: 8
                }
            }
        }
    }
});

const toggleButtons = document.querySelectorAll('.toggle-button');
toggleButtons.forEach(button => {
    button.addEventListener('click', () => {
        const targetId = button.dataset.target;
        const target = document.getElementById(targetId);
        if(!target) return;
        const isHidden = target.classList.toggle('collapsed');
        button.textContent = isHidden ? 'Show records' : 'Hide records';
    });
});
</script>
</body>
</html>