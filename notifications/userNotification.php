<?php

function creationEmail($email, $first_name, $password) {
    $replyTo = "diomenikep@gmail.com";  
    $to = $email;
    $subject = "Notification Account Creation.";
    $message = "
        <p>Hello $first_name,</p>
        <br />
        <p>This is a notification to let you know that your account has been created.</p>
        <p>You can use your account to login:</p>
        <br />
        <p>Email: $email</p>
        <p>Password: $password</p>
        <p>You can log in to your account using this link: <a href='https://accounts-management.vercel.app/login'>https://accounts-management.vercel.app/login</a></p>
        <br />
        <p>Best regards,</p>
        <p>AMS Team</p>
    "; 
    $headers = "From: noreply@halcyonagile.com";  
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "Reply-To: $replyTo\r\n";

    $result = mail($to, $subject, $message, $headers);

    if ($result) {
        echo json_encode(
            array(
                'status' => true,
                'message' => 'Email successfully sent.'
            )
        );
    } else {
        $errorMessage = error_get_last();
        error_log("Email sending error: " . json_encode($errorMessage));

        echo json_encode(
            array(
                'status' => false,
                'message' => "There's an error sending this email."
            )
        );
    }
}

?>