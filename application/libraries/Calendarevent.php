<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendarevent implements JsonSerializable
{

	private $start;/*DateTime*/
	private $end;/*DateTime*/
	private $description;
	private $originId; //if null, this is original event, else repeated

	public function __construct(){
		$this->originId = null;
	}

        public function setStart($startDateTime)
	{
	    if(gettype($startDateTime)!="object"){
	        return false;//must be DateTime
        }
		$this->start = $startDateTime;
		return $this;
	}

	public function getStart(){
	    return $this->start;
    }

	public function setEnd($endDateTime)
	{
        if(gettype($endDateTime)!="object"){
            return false;//must be DateTime
        }
		$this->end = $endDateTime;
		return $this;
	}

	public function getEnd(){
	    return $this->end;
    }

	public function setDescription($descr)
	{
		$this->description = $descr;
		return $this;
	}

	public function getDescription(){
	    return $this->description;
    }

	public function setOriginId($id){
		$this->originId = $id;
		return $this;
	}

	public function getOriginId(){
	    return $this->originId;
    }

	public function toJson()
	{
		$json = json_encode($this);
		error_log("json: " . $json);
		return $json;
	}

	public function jsonSerialize()
	{
		return array
        (
			'start'   => $this->start,
			'end' => $this->end,
			'description' => $this->description
        );
	}
	
	public function jsonSerializeWithOrigin()
	{
		return array
        (
			'start'   => $this->start,
			'end' => $this->end,
			'description' => $this->description,
			'origin' => $this->originId
        );
	}
}

