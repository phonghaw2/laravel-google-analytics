<?php

namespace Phonghaw2\Analytics\Exceptions;

use Exception;

class InvalidPeriod extends Exception
{
    public static function startDateCannotBeAfterEndDate($startDate, $endDate)
    {
        return new static("Start date " . date('Y-m-d', strtotime($startDate)) . " cannot be after end date " . date('Y-m-d', strtotime($endDate)) . ".");
    }

    public static function notStringInDate()
    {
        return new static("The date must be a string.");
    }

    public static function notValidDate()
    {
        return new static("The date is not a valid date.");
    }
}
