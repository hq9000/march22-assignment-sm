<?php

/**
 * This script:
 * 1. takes a email from request (using `unknown` if impossible to fetch it from request)
 * 2. looks up the corresponding username record in the db and prints it out
 *
 */

const REQUEST_KEY_EMAIL        = 'email';
const REQUEST_KEY_MASTER_EMAIL = 'masterEmail';

// todo: consider taking mysql credentials from environment variables
const MYSQL_PASSWORD = 'sldjfpoweifns';
const MYSQL_USERNAME = 'root';
const MYSQL_HOST     = 'localhost';
const MYSQL_PORT     = 3306;
const MYSQL_DATABASE = 'my_database';

const UNKNOWN_EMAIL_KEYWORD_IN_DATABASE = 'unknown';

const COLUMN_NAME_EMAIL    = 'email';
const COLUMN_NAME_USERNAME = 'username';
const TABLE_NAME_USERS     = 'users';

/**
 * Extracts email to be used for lookup from $_REQUEST
 *
 * @return string
 */
function extractMasterEmailFromRequest(): string
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
 * throws if string does not contain a valid email.
 *
 * @param string $email
 *
 * @throws Exception
 */
function validateEmail(string $email)
{
    if (UNKNOWN_EMAIL_KEYWORD_IN_DATABASE === $email) {
        // the "unknown" is considered a valid email entry
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('invalid email');
    }
}

$masterEmail = extractMasterEmailFromRequest();
validateEmail($masterEmail);
echo "The master email is " . $masterEmail . "\n";

$mysqli = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE, MYSQL_PORT);

$query = implode(
    ' ',
    [
        'SELECT',
        COLUMN_NAME_USERNAME,
        'FROM',
        TABLE_NAME_USERS,
        'WHERE',
        COLUMN_NAME_EMAIL . '=?',
    ]
);

// using prepared statement as less performant but
// more secure alternative to escaping
$statement = $mysqli->prepare($query);
if (!$statement) {
    throw new Exception('unable to create a statement: ' . $mysqli->error);
}

$statement->bind_param("s", $masterEmail);
$res = $statement->execute();

$statement->bind_result($resultUsername);
$row = $statement->fetch();
$mysqli->close(); // optional, but good to have

if ($row) {
    echo $resultUsername;
}

