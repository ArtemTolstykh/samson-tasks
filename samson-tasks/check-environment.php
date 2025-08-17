<?php
echo 'max_input_vars: ' . ini_get('max_input_vars');

//phpinfo();


$targets = [
    'tcp://127.0.0.1:80',
    'tcp://google.com:80',
    'ssl://google.com:443',
    'tcp://update.bitrix.info:80',
    'ssl://www.1c-bitrix.ru:443',
];

foreach ($targets as $t) {
    $e = $s = null;
    $conn = @stream_socket_client($t, $e, $s, 5);
    echo $t, ' => ', $conn ? "OK\n" : "ERR ($e: $s)\n";
}


phpinfo();