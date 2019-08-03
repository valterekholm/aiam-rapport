<?php
require_once("Calendarprint_interface.php");

class Weekprint implements Calendarprint_interface
{
    public function __construct(){
        error_log("Constructor of Weekprint");
    }

    /**
     * @param $header - calendar caption
     * @param $referenceDay - to decide time window
     * @param $events - array of events (Calendarevent)
     */
    public function printCal($header, $referenceDay, $events)
    {
        error_log("printCal (week) with $header " . $referenceDay->format("Y-m-d") . ", events count " . count($events));
        foreach ($events as $event) {
            /*
 * private $start;
private $end;
private $description;
private $originId;
 */
            echo $event->getStart()->format("Y-m-d") . " - " . $event->getEnd()->format("Y-m-d") . ", beskr.: " . $event->getDescription() . ", origin id: " . $event->getOriginId() . "<br>";
        }
    }

    public function logHello()
    {
        error_log("Weekprint, Hello");
    }
}