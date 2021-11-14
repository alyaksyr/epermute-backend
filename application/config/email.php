<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
    'smtp_host' => 'ssl://smtp.gmail.com', 
    'smtp_port' => 465,
    'smtp_user' => '',
    'smtp_pass' => '',
    'mailtype' => 'text', //plaintext 'text' mails or 'html'
    'smtp_timeout' => '4', //in seconds
    'charset' => 'iso-8859-1',
    'crlf' => '\r\n',
    'wordwrap' => TRUE,
    'validation' => TRUE
);  