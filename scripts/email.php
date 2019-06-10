<?php

require '../include/class.phpmailer.php';
require '../include/class.smtp.php';

$mail = new PHPMailer;
$mail->IsSMTP();
$mail->CharSet = 'UTF-8';
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Host = 'smtp.office365.com';
$mail->Username = "do-not-reply@nataviation.com";
$mail->Password = "TeamN@S5921";
$mail->Port = 587;

$mail->setFrom('do-not-reply@nataviation.com', 'National Aviation Services');
$mail->addAddress('info@waypointzero.com');

//$mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name
$mail->isHTML(true);

$mail->Subject = 'National Aviation Services Employment Application';
$mail->Body = 'This is the HTML message body <b>in bold!</b>';

if(!$mail->send()) {
echo 'Message could not be sent.';
echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
echo 'Message has been sent';
}

?>
