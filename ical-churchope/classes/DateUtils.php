<?php

/*
Copyright 2014 Derek - web.development.help@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class DateUtils
{

    /**
     * @var String time zone string representing time zone of dates passed into this class http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
     */
    private $timeZone;

    /**
     * @param String $timeZone String time zone string representing time zone of dates passed into this class http://en.wikipedia.org/wiki/List_of_tz_database_time_zones
     * @param Integer $gmtOffset integer representing offset from GMT
     */
    function __construct($timeZone, $gmtOffset)
    {
        $this->timeZone = $this->getTimezoneString($timeZone, $gmtOffset);
    }

    /**
     * Returns the timezone string for a site, even if it's set to a UTC offset
     *
     * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
     * Inspiration from http://www.skyverge.com/blog/down-the-rabbit-hole-wordpress-and-timezones/
     *
     * @param String $timeZoneString timezone (can be null) - may br sourced from WordPress 'timezone_string'
     * @param Integer $gmtOffset GMT offset
     * @return string valid PHP timezone string
     */
    private function getTimezoneString($timeZoneString, $gmtOffset)
    {

        // if site timezone string exists, return it
        if ($timezone = $timeZoneString)
            return $timezone;

        // get UTC offset, if it isn't set then return UTC
        if (0 === ($utc_offset = $gmtOffset))
            return 'UTC';

        // adjust UTC offset from hours to seconds
        $utc_offset *= 3600;

        // attempt to guess the timezone string from the UTC offset
        $timezone = timezone_name_from_abbr('', $utc_offset);

        // last try, guess timezone string manually
        if (false === $timezone) {

            $is_dst = date('I');

            foreach (timezone_abbreviations_list() as $abbr) {
                foreach ($abbr as $city) {
                    if ($city['dst'] == $is_dst && $city['offset'] == $utc_offset)
                        return $city['timezone_id'];
                }
            }
        }

        // fallback to UTC
        return 'UTC';
    }

    /**
     * Convert a date string to GMT iCal formatted String
     *
     * @param $string String in a format accepted by strtotime.
     * @param DateInterval $pad_time interval to add to DateTime when converting
     * @return String date
     */
    public function convertDateStringToGMTiCalString($string, DateInterval $pad_time = null)
    {
        $format = 'Ymd\THis\Z';

        $datetime = date_create($string, new DateTimeZone($this->timeZone));
        if (!$datetime)
            return gmdate($format, 0);
        $datetime->setTimezone(new DateTimeZone('UTC'));
        if ($pad_time != null) {
            $datetime->add($pad_time);
        }
        return $datetime->format($format);

    }

    /**
     * Get a DatePeriod back that extends from roughly 1 year previous to 1 year in the future, with a monthly interval
     *
     * @return DatePeriod
     */
    public function getPeriod()
    {
        $start = new DateTime();
        $start->modify('-1 year');

        $end = new DateTime();
        $end->modify('+1 year');

        $interval = new DateInterval('P1M'); // 1 month

        $period = new DatePeriod($start, $interval, $end);

        return $period;
    }

    /**
     * Get the month and the year from the passed in DateTime object.
     *
     * @param $dt DateTime object
     * @return array returning the month and year.
     */
    public function getMonthAndYearFromDate($dt)
    {
        $month = $dt->format('m');
        $year = $dt->format('Y');
        return array($month, $year);
    }
}