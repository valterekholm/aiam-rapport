<?php
require_once("Calendarprint_interface.php");

/*interface Calendarprint
{
	public function printCal($header, $referenceDay, $events);
}
class Monthprint implements Calendarprint_interface
{
	public function printCal($header, $referenceDay, $events)
	{
		error_log("printCal (month) with $header " . $referenceDay->format("Y-m-d") . ", events count " . count($events));
	}
}


class Weekprint implements Calendarprint_interface
{
	public function printCal($header, $referenceDay, $events)
	{
		error_log("printCal (week) with $header " . $referenceDay->format("Y-m-d") . ", events count " . count($events));
	}
} */

class Dayprint implements Calendarprint_interface
{
	public function printCal($header, $referenceDay, $events)
	{
		error_log("printCal (day) with $header " . $referenceDay->format("Y-m-d") . ", events count " . count($events));
	}
}

