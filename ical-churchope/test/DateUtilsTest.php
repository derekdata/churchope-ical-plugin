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
include_once('ical-churchope/classes/DateUtils.php');

class DateUtilsTest extends PHPUnit_Framework_TestCase
{
    private $dateUtils;

    public function setUp()
    {
        $timezone = 'America/New_York';
        $gmtOffset = -4;
        date_default_timezone_set($timezone);
        $this->dateUtils = new DateUtils($timezone, $gmtOffset);
    }

    public function testGetMonthAndYearFromDate()
    {
        $dt = DateTime::createFromFormat('j-M-Y', '15-Feb-2009');
        list($month, $year) = $this->dateUtils->getMonthAndYearFromDate($dt);
        $this->assertEquals($month, 2);
        $this->assertEquals($year, 2009);
    }

    public function testGetPeriod()
    {
        $period = $this->dateUtils->getPeriod();

        //start out with a 1 year, 1 month old date
        $previousDt = new DateTime();
        $previousDt->modify('-1 year');
        $previousDt->modify('-1 month');

        foreach ($period as $dt) {

            $interval = $dt->diff($previousDt);

            //verify that dates are ascending and 1 month apart, as desired
            $this->assertEquals(1, ($interval->format('%m')));
            $this->assertGreaterThan($previousDt, $dt);

            $previousDt = $dt;

        }
    }

    public function testConvertDateStringToGMTiCalString()
    {

        $iCalString = $this->dateUtils->convertDateStringToGMTiCalString('5 September 2010');
        $this->assertEquals($iCalString, "20100905T040000Z");

        $iCalString = $this->dateUtils->convertDateStringToGMTiCalString('5 December 2010');
        $this->assertEquals($iCalString, "20101205T050000Z");

        $iCalString = $this->dateUtils->convertDateStringToGMTiCalString('5 December 2010', new DateInterval('P1D'));
        $this->assertEquals($iCalString, "20101206T050000Z");

        $iCalString = $this->dateUtils->convertDateStringToGMTiCalString('5 December 2010', new DateInterval('PT1H'));
        $this->assertEquals($iCalString, "20101205T060000Z");

    }

    public function testClassTimezoneSetup_nullTimezone()
    {

        $gmtOffset = -10;
        $this->dateUtils = new DateUtils(null, $gmtOffset);

        $iCalString = $this->dateUtils->convertDateStringToGMTiCalString('5 December 2010');
        $this->assertEquals($iCalString, "20101205T090000Z");

        $iCalString = $this->dateUtils->convertDateStringToGMTiCalString('5 December 2010');
        $this->assertEquals($iCalString, "20101205T090000Z");

        $iCalString = $this->dateUtils->convertDateStringToGMTiCalString('5 December 2010', new DateInterval('P1D'));
        $this->assertEquals($iCalString, "20101206T090000Z");

        $iCalString = $this->dateUtils->convertDateStringToGMTiCalString('5 December 2010', new DateInterval('PT1H'));
        $this->assertEquals($iCalString, "20101205T100000Z");

    }
}

?>