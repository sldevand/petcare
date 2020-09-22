<?php

namespace Framework\Helper;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Class DateHelper
 * @package Framework\Helper
 */
class DateHelper
{
    /**
     * @return string
     * @throws Exception
     */
    public static function now()
    {
        return (new DateTime('now', new DateTimeZone('Europe/Paris')))->format('Y-m-d H:i:s');
    }

    /**
     * @param string $dateTime
     * @return int
     */
    public static function timeElapsedInMinutes($dateTime)
    {
        $now = new DateTime();

        return $now->diff(new DateTime($dateTime))->i;
    }
}
