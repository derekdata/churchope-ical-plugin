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

include_once('DateUtils.php');
include_once('WordPressFunctionProxy.php');

class ChurchopeICalGenerator
{

    /**
     * @var DateUtils class that provides date functions
     */
    private $dateUtils;

    /**
     * @var String time zone that server is running in
     */
    private $timezone;

    /**
     * @var String
     */
    private $siteName;

    /**
     * @var ChurchopeFunctionProxy function proxy to make this class testable
     */
    private $churchopeFunctionProxy;

    /**
     * @var String
     */
    private $siteUrl;

    /**
     * @var WordPressFunctionProxy function proxy to make this class testable
     */
    private $wordPressFunctionProxy;

    /**
     * @var String custom post meta prefix used by theme.  Called SHORTNAME in theme.
     */
    private $shortName;

    /**
     * @param String $timezone
     * @param ChurchopeFunctionProxy $churchopeFunctionProxy
     * @param String $siteName
     * @param String $siteUrl
     * @param WordPressFunctionProxy $wordPressFunctionProxy
     * @param String $shortName
     * @param Integer $gmtOffset offset from GMT
     */
    function __construct($timezone, ChurchopeFunctionProxy $churchopeFunctionProxy, $siteName, $siteUrl, WordPressFunctionProxy $wordPressFunctionProxy, $shortName, $gmtOffset)
    {
        $this->timezone = $timezone;
        $this->dateUtils = new DateUtils($timezone, $gmtOffset);
        $this->churchopeFunctionProxy = $churchopeFunctionProxy;
        $this->siteName = $siteName;
        $this->siteUrl = $siteUrl;
        $this->wordPressFunctionProxy = $wordPressFunctionProxy;
        $this->shortName = $shortName;
    }

    /**
     * Get event data from CHURCHOPE getMonthEvents function and format in iCal format.  Retrieves the year prior and future year.
     */
    public function getICalData()
    {
        //get host
        $parsedUrl = parse_url($this->siteUrl);
        $domain = $parsedUrl['host'];

        //init eventsString
        $eventsString = '';

        //setup date period variables for looping
        $period = $this->dateUtils->getPeriod();

        foreach ($period as $dt) {

            list($month, $year) = $this->dateUtils->getMonthAndYearFromDate($dt);
            $calendarEvents = $this->churchopeFunctionProxy->getMonthEvents_proxy($month, $year); // array of month days with events
            $eventsString = $this->createICalEventsFromCalendarEvents($calendarEvents, $year, $month, $domain);

        }


        $content = <<<CONTENT
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//$this->siteName//NONSGML v1.0//EN
X-WR-CALNAME:{$this->siteName} - Events
X-WR-TIMEZONE:{$this->timezone}
X-ORIGINAL-URL:{$this->siteUrl}
X-WR-CALDESC:{$this->siteName} - Events
CALSCALE:GREGORIAN
METHOD:PUBLISH
{$eventsString}END:VCALENDAR
CONTENT;

        return $content;
    }

    /**
     * @param $calendarEvents array of event posts
     * @param $year String
     * @param $month String
     * @param $domain String actual domain of website
     * @return String iCal string of events
     */
    private function createICalEventsFromCalendarEvents($calendarEvents, $year, $month, $domain)
    {
        $postCache = '';
        $eventsString = '';

        if ($calendarEvents && is_array($calendarEvents) && count($calendarEvents)) {

            foreach ($calendarEvents as $dayNumber => $events) {

                if ($events && is_array($events) && count($events)) {
                    foreach ($events as $event) {

                        if (isset($postCache[$event->post_id])) {
                            $details = $postCache[$event->post_id];
                        } else {

                            $postCache[$event->post_id] = array(
                                'post' => $this->wordPressFunctionProxy->get_post_proxy($event->post_id),
                                'url' => $this->wordPressFunctionProxy->get_permalink_proxy($event->post_id),
                                'time' => $this->wordPressFunctionProxy->get_post_meta_proxy($event->post_id, $this->shortName . '_event_time', true),
                            );
                            $details = $postCache[$event->post_id];

                        }

                        if ($details && $details['post'] && $details['post']->post_status == 'publish') {

                            //string for the date we are currently processing
                            $thisDateString = $year . "/" . $month . "/" . $dayNumber;

                            //prepare summary
                            $summary = html_entity_decode(strip_tags($details['post']->post_title));

                            //prepare content
                            $content = html_entity_decode(strip_tags($details['post']->post_content));
                            $content = str_replace(array("\r\n", "\r", "\n"), '\n', $content);

                            //prepare url
                            $url = strip_tags($details['url']);

                            //prepare start time
                            $startTime = $this->dateUtils->convertDateStringToGMTiCalString($thisDateString . " " . $details['time']);

                            //prepare end time
                            $endTime = $this->dateUtils->convertDateStringToGMTiCalString($thisDateString . " " . $details['time'], new DateInterval('PT2H'));

                            //prepare datetimestamp based on original post date
                            $dateTimeStamp = $this->dateUtils->convertDateStringToGMTiCalString($details['post']->post_date);

                            //create uid
                            $uid = $details['post']->ID . "-" . $startTime . "@" . $domain;

                            //TODO Location could be added in the future

                            $eventsString .= <<<EVENT
BEGIN:VEVENT
UID:$uid
DTSTAMP:$dateTimeStamp
DTSTART:$startTime
DTEND:$endTime
SUMMARY:$summary
DESCRIPTION:$content
URL:$url
END:VEVENT

EVENT;


                        }
                    }
                }
            }
            return $eventsString;
        }
        return $eventsString;
    }

}

?>