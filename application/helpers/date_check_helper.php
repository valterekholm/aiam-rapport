<?php
defined('BASEPATH') OR exit('No direct script access allowed');//?

if ( ! function_exists('validateDate'))
{
function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
}
?>