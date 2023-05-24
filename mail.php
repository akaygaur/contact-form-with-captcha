<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");

function isValid()
{
    // Perform your server-side validation here
    // Ensure that all required fields are provided and meet your validation criteria
    if (!empty($_POST['name']) && !empty($_POST['email'])) {
        return true;
    } else {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input to prevent potential security vulnerabilities
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['mob']);
    $ipAddress = $_POST['ipaddress'];
    $pageUrl = $_POST['pageurl'];
    $message = htmlspecialchars($_POST['message']);

    $to = 'Your Email';
    $subject = 'Enquiry @Your Site';

    $emailHeaders = "From: Example <info@example.com>\r\n";
    $emailHeaders .= "Reply-To: $email\r\n";
    $emailHeaders .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    if (isValid()) {
        // Verify reCAPTCHA response
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = 'Your Key'; // Insert your secret key here
        $recaptcha_response = $_POST['recaptcha_response'];

        $recaptcha_data = [
            'secret' => $recaptcha_secret,
            'response' => $recaptcha_response
        ];

        $recaptcha_options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($recaptcha_data)
            ]
        ];

        $recaptcha_context = stream_context_create($recaptcha_options);
        $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);

        if ($recaptcha_result !== false) {
            $recaptcha_result = json_decode($recaptcha_result, true);

            if ($recaptcha_result['success'] && $recaptcha_result['score'] >= 0.5) {
                // Send email to admin
                $emailMessage = "Name: $name\r\n";
                $emailMessage .= "Email: $email\r\n";
                $emailMessage .= "Telephone: $telephone\r\n";
                $emailMessage .= "Message: $message\r\n";
                $emailMessage .= "IP Address: $ipAddress\r\n";
                $emailMessage .= "Page URL: $pageUrl\r\n";

                if (mail($to, $subject, $emailMessage, $emailHeaders)) {
                    $outputCode = 0; // Success
                } else {
                    $outputCode = 3; // Failed to send email
                }
            } else {
                $outputCode = 1; // Invalid reCAPTCHA score
            }
        } else {
            $outputCode = 4; // Failed to verify reCAPTCHA
        }
    } else {
        $outputCode = 2; // Server-side validation failed
    }

    $output = [
        'output' => $outputCode
    ];

    // Output needs to be in JSON format
    header('Content-Type: application/json');
    echo json_encode($output);
}
?>
