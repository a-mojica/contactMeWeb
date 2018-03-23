<?php
/*!
* contactMeWeb.php
* Copyright (c) 2018 Antonio Mojica
* Licensed under The MIT License - http://opensource.org/licenses/MIT
* https://amojica.ch
*/
    // be sure to get the required files for the dependencies!
    /* reCAPTCHA PHP client library */
    require "recaptcha/autoload.php"; // you can get the required files from reCAPTCHA's Repository: https://github.com/google/recaptcha

    /* PHPMailer */
    use PHPMailer\PHPMailer\PHPMailer; // you can get
    use PHPMailer\PHPMailer\Exception; // the required files
    require "PHPMailer/src/Exception.php"; // from PHPMailer's Repository:
    require "PHPMailer/src/PHPMailer.php"; // https://github.com/PHPMailer/PHPMailer
    /* END dependencies */

    $at = "@";
    $dot = ".";

    $fromEmail = "SERVER{$at}SENDER{$dot}ADDRESS"; // replace this with the server's sender address.. e.g. postmaster{$at}domainName{$dot}TLD
    $fromEmail = str_replace("\n", "", str_replace("\r", "", $fromEmail));
    $fromName = "SENDER NAME"; // replace this with, say, the name of the website.. e.g. amojica.ch
    $fromName = str_replace("\n", "", str_replace("\r", "", $fromName));

    $toEmail = "YOUR{$at}RECIPIENT{$dot}ADDRESS"; // replace this with the address in which you want to receive the email.. e.g. myEmailAddress{$at}protonmail{$dot}.ch
    $toEmail = str_replace("\n", "", str_replace("\r", "", $toEmail));
    $subject = "SUBJECT OF THE EMAIL"; // replace this with, say, "New message from <the website's contactMeWeb>"
    $subject = str_replace("\n", "", str_replace("\r", "", $subject));

    $contactMeWeb = array("firstName" => "First Name", "lastName" => "Last Name", "email" => "Email", "phone" => "Phone", "company" => "Company", "message" => "Message");

    $successMsg = "Submission success. Thank you, I will be in touch!";

    $no = "YOUR"; // replace this with your reCAPTCHA's private key
    $touchy = "PRIVATE"; // I split it into 3 parts because...
    $fishy = "KEY"; // ...why not?
    $magic = "{$no}{$touchy}{$fishy}";

    /* reCAPTCHA v2 */    
    try {
        if (!empty($_POST)) {
            // verify that reCaptcha is set
            if (!isset($_POST["g-recaptcha-response"])) {
                throw new \Exception("ERROR: reCAPTCHA is not set. Please verify...");
            }
            $recaptcha = new \ReCaptcha\ReCaptcha($magic, new \ReCaptcha\RequestMethod\CurlPost());
            // validate reCaptcha's response and user's IP
            $response = $recaptcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]);
            if (!$response->isSuccess()) {
                throw new \Exception("Sorry, reCAPTCHA validation is required...");
            }
            // reCAPTCHA OK
            
            /* PHPMailer */
            $emailBody = "<h3>New submission from your <em>Contact Form</em></h3><hr>";
            $emailBody .= "<table>";
            foreach ($_POST as $field => $value) {
                if (isset($contactMeWeb[$field])) {
                    $emailBody .= "<tr><th>$contactMeWeb[$field]</th><td>$value</td></tr>";
                }
            }
            $emailBody .= "</table><hr>";
            
            $mail = new PHPMailer;
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail);
            $mail->addReplyTo($from);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->msgHTML($emailBody); // create a plain-text version
            if(!$mail->send()) {
                throw new \Exception("ERROR: Email could not be sent. Please verify..." . $mail->ErrorInfo);
            }
            
            $responseArray = array("type" => "success", "message" => $successMsg);
            /* END phpmailer */
        }
        else {
            throw new \Exception("Don\'t be silly... the Contact Form is empty!");
        }
    }
    catch (\Exception $e) {
        $responseArray = array("type" => "danger", "message" => $e->getMessage());
    }
    /* END reCaptcha v2 */

    // if requested by AJAX then return JSON response
    if (!empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
        $encoded = json_encode($responseArray);
        header("Content-Type: application/json");
        echo $encoded;
    }
    // else just display the message
    else {
        echo $responseArray["message"];
    }
?>