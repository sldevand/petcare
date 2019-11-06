<?php

namespace Framework\Helper;

use DateTime;
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
        return (new DateTime())->format('Y-m-d H:i:s');
    }
}
