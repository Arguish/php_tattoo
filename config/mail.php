<?php
return [
    'smtp_host' => getenv('SMTP_HOST'),
    'smtp_port' => getenv('SMTP_PORT'),
    'smtp_user' => getenv('SMTP_USER'),
    'smtp_pass' => getenv('SMTP_PASS'),
    'from_email' => getenv('MAIL_FROM'),
    'from_name' => 'Centro de Tatuajes:setTattoo($INK)'
];
