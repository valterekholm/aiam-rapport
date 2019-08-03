<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar_ implements JsonSerializable
{/* The classname 'Calendar' seems to be taken ... at least could not be used */

	private $start;
	private $events = array();
	private $type;//month or week or day
	public $printer;


	/*Param Printer - an instance of Calendarprint_interface*/
	public function __construct($args){

        error_log("Constructor of Calendar, with printer " . get_class($args["printer"]));

		$this->printer = $args["printer"];


	}

    public function logHello()
    {
        error_log("Calendar, Hello... my printer says " . $this->printer->logHello());
    }

	public static function withEvents($events){
		$instance = new self();
		$instance->setEvents($events);
		return $instance;
	}//call by $cal = Calendar::withEvents($events);

	public function setStart($startDateTime)
	{
		$this->start = $startDateTime;
	}

	protected function setType($tpe){

		$tpe = strtolower($tpe);

		switch($tpe){
		case "day":
		case "week":
		case "month":
			$this->type = $tpe;
			break;
		default:
			return false;
		}
		return true;
	}

	public function setEvents($evnts){
		$this->events = $evnts;
	}

	public function printEvents(){
	    echo "Events count: " . count($this->events);
    }

	function jsonSerialize()
	{
		$ev_json ="";
		foreach($this->events as $e){
			$ev_json .= $e->jsonSerialize();
		}

		return array(
				'start'   => $this->start,
				'events' => $ev_json
        );
	}

	function printCal(){
	    if(count($this->events)==0){
	        return false;
        }
		$refDate = $this->events[0]->getStart();
		/*
		 * private $start;
    	private $end;
    	private $description;
	    private $originId;
		 */
		$this->printer->printCal("Events", $refDate, $this->events);
	}	
}

