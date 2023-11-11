<?php

namespace Phonghaw2\Analytics;

use Phonghaw2\Analytics\Exceptions\InvalidPeriod;

class Period
{
    public $startDate;
    public $endDate;

    public function __construct($startDate, $endDate)
    {
        self::checkDate($startDate, $endDate);

        $this->startDate = $startDate;

        $this->endDate = $endDate;
    }

    public static function set($startDate, $endDate = 'today'): self
    {
        return new static($startDate, $endDate);
    }

    public static function days(int $numberOfDays): self
    {
        $endDate = date('Y-m-d H:i:s', strtotime('now'));

        $startDate = date('Y-m-d H:i:s', strtotime('-' . $numberOfDays . 'days'));

        return new static($startDate, $endDate);
    }

    public static function months(int $numberOfMonths): self
    {
        $endDate = date('Y-m-d H:i:s', strtotime('now'));

        $startDate = date('Y-m-d H:i:s', strtotime('-' . $numberOfMonths . 'months'));

        return new static($startDate, $endDate);
    }

    public static function years(int $numberOfYears): self
    {
        $endDate = date('Y-m-d H:i:s', strtotime('now'));

        $startDate = date('Y-m-d H:i:s', strtotime('-' . $numberOfYears . 'years'));

        return new static($startDate, $endDate);
    }

    public static function checkDate($startDate, $endDate)
    {
        if (!is_string($startDate) || !is_string($endDate)) {
            throw InvalidPeriod::notStringInDate();
        }

        if (!self::isValidDate($startDate) || !self::isValidDate($endDate)) {
            throw InvalidPeriod::notValidDate();
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            throw InvalidPeriod::startDateCannotBeAfterEndDate($startDate, $endDate);
        }
    }

    public static function isValidDate($date)
    {
        return (strtotime($date) !== false);
    }
}
