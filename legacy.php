<?php

if ($_REQUEST['email']) {
    $masterEmail = $_REQUEST['email'];
}
$masterEmail = isset($masterEmail) && $masterEmail ? $masterEmail : array_key_exists(
    'masterEmail',
    $_REQUEST
) && $_REQUEST["masterEmail"] ? $_REQUEST['masterEmail'] : 'unknown';

echo 'The master email is ' . $masterEmail . '\n';

$conn = mysqli_connect('localhost', 'root', 'sldjfpoweifns', 'my_database');
$res  = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE email='" .
    $masterEmail . "'"
);

$row = mysqli_fetch_row($res);
echo $row['username'] . "\n";