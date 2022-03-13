<?php

const REQUEST_KEY_EMAIL        = 'email';
const REQUEST_KEY_MASTER_EMAIL = 'masterEmail';

const MYSQL_PASSWORD = 'sldjfpoweifns'; // todo: consider taking it from env
const MYSQL_USERNAME = 'root'; // todo: consider taking it from env
const MYSQL_HOST     = 'localhost'; // todo: consider taking it from env
const MYSQL_DATABASE = 'my_database'; // todo: consider taking it from env

const UNKNOWN_EMAIL_KEYWORD_IN_DATABASE = 'unknown';

const COLUMN_NAME_EMAIL    = 'email';
const COLUMN_NAME_USERNAME = 'username';
const TABLE_NAME_USERS     = 'users';

/**
 * Extracts email to look for from $_REQUEST
 *
 * @return string
 */
function extractEmailFromRequest(): string
{
    $masterEmail = $_REQUEST[REQUEST_KEY_EMAIL] ?? null;

    if (!$masterEmail) {
        $masterEmail = $_REQUEST[REQUEST_KEY_MASTER_EMAIL] ?? null;
    }

    if (!$masterEmail) {
        $masterEmail = UNKNOWN_EMAIL_KEYWORD_IN_DATABASE;
    }

    return $masterEmail;
}

/**
 * throws if string does not contain a valid email
 *
 * @param string $email
 *
 * @throws Exception
 */
function validateEmail(string $email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('invalid email');
    }
}

$masterEmail = extractEmailFromRequest();
validateEmail($masterEmail);
echo 'The master email is ' . $masterEmail . '\n';

$conn = mysqli_connect(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

$query = implode(
    ' ',
    [
        'SELECT',
        COLUMN_NAME_USERNAME,
        'FROM',
        TABLE_NAME_USERS,
        'WHERE',
        COLUMN_NAME_EMAIL . '=',
        '\'' . mysqli_real_escape_string($conn, $masterEmail) . '\'',
    ]
);

$res = mysqli_query(
    $conn, $query
);

$row = mysqli_fetch_row($res);
echo $row[COLUMN_NAME_USERNAME] . "\n";