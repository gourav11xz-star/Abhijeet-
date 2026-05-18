<?php
// Sanitize data
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email
function is_valid_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Minimum length check
function min_length($str, $length)
{
    return strlen($str) >= $length;
}
