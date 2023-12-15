<?php

namespace App;

use Illuminate\Support\Facades\Auth;

/**
 * Get formatted datetime in database friendly format from a given input datetime string.
 *
 * @param string $input
 *
 * @return null|string
 */
function get_datetime_for_db($input = 'now')
{
    $input = wt_clean_input_date($input);

    // Get timestamp
    $ts = strtotime($input);

    // If timestamp is low enough, that means the date is empty
    if ($ts < strtotime('1980-01-01')) {
        return null;
    }

    return date('Y-m-d H:i:s', $ts);
}

/**
 * Get formatted date in database friendly format from a given input date string.
 *
 * @param string $input
 *
 * @return false|string
 */
function wt_get_date_for_db($input = 'now')
{
    $input = wt_clean_input_date($input);

    // strtotime() does not work for dates like dd/mm/yyyy so change it to dd-mm-yyyy
    $input = str_replace('/', '-', $input);

    // Get timestamp
    $ts = strtotime($input);

    // If timestamp is low enough, that means the date is empty
    if ($ts < strtotime('1980-01-01')) {
        return null;
    }

    return date('Y-m-d', $ts);
}

/**
 * Get formatted datetime for display.
 *
 * @param string $input
 *
 * @return false|string
 */
function wt_get_datetime_for_display($input = 'now')
{
    $input = wt_clean_input_date($input);

    // Get timestamp
    $ts = strtotime($input);

    // If timestamp is low enough, that means the date is empty
    if ($ts < strtotime('1980-01-01')) {
        return 'n/a';
    }

    return date('d-M-Y h:ia', $ts);
}

/**
 * Get formatted date for display.
 *
 * @param string $input
 *
 * @return false|string
 */
function wt_get_date_for_display($input = 'now')
{
    $input = wt_clean_input_date($input);

    // Get timestamp
    $ts = strtotime($input);

    // If timestamp is low enough, that means the date is empty
    if ($ts < strtotime('1980-01-01')) {
        return 'n/a';
    }

    return date('d-M-Y', $ts);
}

/**
 * Add days to a date.
 *
 * @param int $days
 * @param string $date
 *
 * @return string
 */
function wt_add_days_to_date($days, $date = 'now')
{
    return date('Y-m-d', strtotime(wt_clean_input_date($date)) + 60 * 60 * 24 * $days);
}

/**
 * Add months to a date.
 *
 * @param int $months
 * @param string $date
 *
 * @return string
 */
function wt_add_months_to_date($months, $date = 'now')
{
    return date('Y-m-d', strtotime(wt_clean_input_date($date) . ' +' . $months . ' months'));
}

/**
 * Get month start date for a given date (or today).
 *
 * @param string $date
 *
 * @return string
 */
function wt_get_month_start_date_of_date($date = 'now')
{
    return date('Y-m-01', strtotime(wt_clean_input_date($date)));
}

/**
 * Get month end date for a given date (or today).
 *
 * @param string $date
 *
 * @return string
 */
function wt_get_month_end_date_of_date($date = 'now')
{
    return date('Y-m-t', strtotime(wt_clean_input_date($date)));
}

/**
 * Get no of days in two dates.
 *
 * @param string $date
 *
 * @return string
 */
function wt_get_days_between_two_dates($date1, $date2)
{
    $ts1 = strtotime(wt_clean_input_date($date1));
    $ts2 = strtotime(wt_clean_input_date($date2));

    return ceil(($ts2 - $ts1) / (60 * 60 * 24));
}

function wt_get_hours_between_two_dates($date1, $date2)
{
    $ts1 = strtotime(wt_clean_input_date($date1));
    $ts2 = strtotime(wt_clean_input_date($date2));

    return abs(($ts2 - $ts1) / (60 * 60));
}

function wt_get_seconds_between_two_dates($date1, $date2)
{
    $ts1 = strtotime(wt_clean_input_date($date1));
    $ts2 = strtotime(wt_clean_input_date($date2));

    return abs(($ts2 - $ts1));
}


/**
 * Get no of months in two dates.
 *
 * @param string $date
 *
 * @return string
 */
function wt_get_months_between_two_dates($date1, $date2)
{
    /* Does not work well
    $year1 = date('Y', $ts1);
    $year2 = date('Y', $ts2);

    $month1 = date('m', $ts1);
    $month2 = date('m', $ts2);

    return (($year2 - $year1) * 12) + ($month2 - $month1); */

    /* Does not work well either
    $ts1 = strtotime(wt_clean_input_date($date1));
    $ts2 = strtotime(wt_clean_input_date($date2));

    return ceil(($ts2 - $ts1) / (60 * 60 * 24 * 30)); */

    $inverval = date_diff(date_create($date1), date_create($date2));
    return $inverval->m;
}

/**
 * Remove values that cause strtotime() to fail
 *
 * @param string $input
 *
 * @return string
 */
function wt_clean_input_date($input)
{
    return str_replace('"', '', $input);
}

/**
 * Find if input date is first day of month
 *
 * @param string $input
 *
 * @return boolean
 */
function wt_is_date_first_day_of_month($input)
{
    return substr($input, 8, 2) == '01';
}

/**
 * Find if input date is last day of month
 *
 * @param string $input
 *
 * @return boolean
 */
function wt_is_date_last_day_of_month($input)
{
    return $input == wt_get_month_end_date_of_date($input);
}

/**
 * Format a given date with the given format.
 *
 * @param string $date
 * @param string $format
 *
 * @return string
 */
function wt_check_leap_year($year)
{

    if ($year % 400 == 0) {
        return true;
    } elseif ($year % 100 == 0) {
        return false;
    } elseif ($year % 4 == 0) {
        return true;
    } else {
        return false;
    }
}
?>
