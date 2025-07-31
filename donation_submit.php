<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establish connection
$conn = mysqli_connect("localhost:3306", "zts91xzcemos", "AaravAayansh@1", "rejuvenateganga");
if (!$conn) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Sanitize inputs
$currency = isset($_POST['currency']) ? trim(strtolower($_POST['currency'])) : '';
if (!in_array($currency, ['dollar', 'rupee'])) {
    die("ERROR: Please select a valid currency (Dollar or Indian Rupee)");
}

$firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : null;
$lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$transaction_id = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : null;
$donation_method = isset($_POST['donation-method']) ? trim($_POST['donation-method']) : null;
$date_of_donation = isset($_POST['date_of_donation']) ? trim($_POST['date_of_donation']) : null;

// Initialize donation amount based on currency
$donation_amount = isset($_POST['donation_amount']) ? floatval($_POST['donation_amount']) : 0;
$donation_amount_usd = 0;  // Initialize to 0
$donation_amount_inr = 0;  // Initialize to 0

if ($currency === 'dollar') {
    $donation_amount_usd = $donation_amount;
} elseif ($currency === 'rupee') {
    $donation_amount_inr = $donation_amount;
}

// Validate inputs
if (!$firstname || !$lastname || !filter_var($email, FILTER_VALIDATE_EMAIL) || 
    $donation_amount <= 0 || !$date_of_donation || !$transaction_id || !$donation_method) {
    die("ERROR: All fields are required.");
}

if (!DateTime::createFromFormat('Y-m-d', $date_of_donation)) {
    die("ERROR: Invalid date format. Use YYYY-MM-DD.");
}

// Insert into pending_donations table
$sql = "INSERT INTO pending_donations (
    currency,
    transaction_id,
    firstname,
    lastname,
    email,
    donation_amount_usd,
    donation_amount_inr,
    donation_method,
    date_of_donation,
    created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("ERROR: Could not prepare statement: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "sssssddss", 
    $currency,
    $transaction_id,
    $firstname,
    $lastname,
    $email,
    $donation_amount_usd,
    $donation_amount_inr,
    $donation_method,
    $date_of_donation
);

if (mysqli_stmt_execute($stmt)) {
    // Get the most recent donation details
    $recent_donation = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'donation_amount' => $donation_amount,
        'currency' => $currency,
        'donation_method' => $donation_method,
        'transaction_id' => $transaction_id,
        'date_of_donation' => $date_of_donation
    ];
    
    // Create HTML for the success message and donation details
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Donation Submitted - Rejuvenate Ganga</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f4f6f9;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .success-message {
                color: #28a745;
                font-size: 24px;
                margin-bottom: 20px;
                text-align: center;
            }
            .donation-details {
                margin-top: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #dee2e6;
            }
            th {
                background-color: #f8f9fa;
                font-weight: 600;
            }
            .amount {
                font-weight: bold;
                color: #28a745;
            }
            .back-button {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 4px;
            }
            .back-button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='success-message'>
                Thank you for your donation! Our team will verify your payment.
            </div>
            <div class='donation-details'>
                <h2>Your Donation Details</h2>
                <table>
                    <tr>
                        <th>Name</th>
                        <td>" . htmlspecialchars($recent_donation['firstname'] . ' ' . $recent_donation['lastname']) . "</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>" . htmlspecialchars($recent_donation['email']) . "</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td class='amount'>" . ($recent_donation['currency'] === 'dollar' ? '$' : '₹') . number_format($recent_donation['donation_amount'], 2) . "</td>
                    </tr>
                    <tr>
                        <th>Payment Method</th>
                        <td>" . htmlspecialchars($recent_donation['donation_method']) . "</td>
                    </tr>
                    <tr>
                        <th>Transaction ID</th>
                        <td>" . htmlspecialchars($recent_donation['transaction_id']) . "</td>
                    </tr>
                    <tr>
                        <th>Date of Donation</th>
                        <td>" . htmlspecialchars($recent_donation['date_of_donation']) . "</td>
                    </tr>
                </table>
                <a href='donation.php' class='back-button'>Back to Donation Page</a>
            </div>
        </div>
    </body>
    </html>";
    
    echo $html;
} else {
    die("ERROR: Could not execute query: " . mysqli_error($conn));
}

mysqli_close($conn);
?>