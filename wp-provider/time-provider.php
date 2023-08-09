<?php
require_once __DIR__ . './../wp-provider/conf-provider.php';

// Set default timezone
date_default_timezone_set(get_app_timezone());

// Function to format a DateTime object to a string
function format_date(DateTime $dateTime, $format = 'Y-m-d H:i:s'): string
{
    return $dateTime->format($format);
}

// Function to parse a string and return a DateTime object
function parse_date_string($inputString, $format = 'Y-m-d H:i:s'): DateTime|bool
{
    return DateTime::createFromFormat($format, $inputString);
}

// Function to add days to a DateTime object
function add_days(DateTime $dateTime, $days): DateTime
{
    $interval = new DateInterval("P{$days}D");
    return $dateTime->add($interval);
}

// Function to subtract days from a DateTime object
function subtract_days(DateTime $dateTime, $days): DateTime
{
    $interval = new DateInterval("P{$days}D");
    return $dateTime->sub($interval);
}

// Function to calculate the difference between two DateTime objects
function date_difference(DateTime $dateTime1, DateTime $dateTime2, $format = '%a days'): string
{
    $interval = $dateTime1->diff($dateTime2);
    return $interval->format($format);
}

// Function to get the current date and time
function get_current_date_time(): DateTime
{
    return new DateTime();
}

// Function to get the current Unix timestamp
function get_current_timestamp()
{
    return time();
}

// Function to get the day of the week for a given date
function get_day_of_week(DateTime $dateTime): string
{
    return $dateTime->format('l');
}

?>