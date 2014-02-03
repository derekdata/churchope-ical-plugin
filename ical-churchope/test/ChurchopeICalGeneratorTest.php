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
include_once('ical-churchope/classes/ChurchopeICalGenerator.php');
include_once('ical-churchope/classes/ChurchopeFunctionProxy.php');
include_once('ical-churchope/classes/WordPressFunctionProxy.php');

class ChurchopeICalGeneratorTest extends PHPUnit_Framework_TestCase
{

    private $timezone;
    private $gmtOffset;

    public function setUp()
    {

        $this->timezone = 'America/New_York';
        $this->gmtOffset = -4;
        date_default_timezone_set($this->timezone);

    }

    public function testGetICalData()
    {

        /**
         * Setup variables for test
         */
        $siteName = 'All your base are belong to us';
        $siteUrl = 'https://www.kernel.org/';
        $shortName = 'ch';
        $eventsPerDay = 4;

        /**
         * BEGIN Setup ChurchopeFunctionProxy stub
         */
        $churchHopeFunctionProxyStub = $this->getMock('ChurchopeFunctionProxy', array(), array(null));

        $events = array();

        for ($i = 1; $i <= $eventsPerDay; $i++) {
            $event = new StdClass();
            $event->post_id = $i;
            $events[$i - 1] = $event;
        }

        $eventsArray = array('01' => $events, '02' => $events);

        $churchHopeFunctionProxyStub->expects($this->any())
            ->method('getMonthEvents_proxy')
            ->will($this->returnValue($eventsArray));
        /**
         * END Setup ChurchopeFunctionProxy stub
         */

        /**
         * BEGIN Setup WordPressFunctionProxy stub
         */
        $WordPressFunctionProxyStub = $this->getMock('WordPressFunctionProxy');

        $post = new StdClass();
        $post->ID = 1;
        $post->post_status = 'publish';
        $post->post_title = 'post title';
        $post->post_content = 'post_content';
        $post->post_date = '2013-09-29 10:52:11';

        $WordPressFunctionProxyStub->expects($this->any())
            ->method('get_post_proxy')
            ->will($this->returnValue($post));

        $WordPressFunctionProxyStub->expects($this->any())
            ->method('get_permalink_proxy')
            ->will($this->returnValue($siteUrl));

        $WordPressFunctionProxyStub->expects($this->any())
            ->method('get_post_meta_proxy')
            ->will($this->returnValue('12:30 AM'));
        /**
         * END Setup WordPressFunctionProxy stub
         */

        $churchopeICalGenerator = new ChurchopeICalGenerator($this->timezone, $churchHopeFunctionProxyStub, $siteName, $siteUrl, $WordPressFunctionProxyStub, $shortName, $this->gmtOffset);
        $output = $churchopeICalGenerator->getICalData();

        /**
         * Assertions
         *
         * This section should be expanded to include a wider variety of unit tests & assertions.
         */

        //ensure that UID appears once per event
        $this->assertEquals(substr_count($output, 'UID'), count($eventsArray) * $eventsPerDay);

        //website address shoudld be seen once in the header and once per event
        $this->assertEquals(substr_count($output, $siteUrl), (count($eventsArray) * $eventsPerDay) + 1);

        $expectedOutput = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//All your base are belong to us//NONSGML v1.0//EN
X-WR-CALNAME:All your base are belong to us - Events
X-WR-TIMEZONE:America/New_York
X-ORIGINAL-URL:https://www.kernel.org/
X-WR-CALDESC:All your base are belong to us - Events
CALSCALE:GREGORIAN
METHOD:PUBLISH";

        foreach ($eventsArray as $dayNumber => $events) {
            for ($i = 1; $i <= $eventsPerDay; $i++) {
                $expectedOutput .= "
BEGIN:VEVENT
UID:1-201501${dayNumber}T053000Z@www.kernel.org
DTSTAMP:20130929T145211Z
DTSTART:201501${dayNumber}T053000Z
DTEND:201501${dayNumber}T073000Z
SUMMARY:post title
DESCRIPTION:post_content
URL:https://www.kernel.org/
END:VEVENT";
            }
        }


        $expectedOutput .= "\nEND:VCALENDAR";

        $this->assertEquals($expectedOutput, $output);
    }

}

?>