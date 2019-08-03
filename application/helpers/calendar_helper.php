<?php
defined('BASEPATH') OR exit('No direct script access allowed');//?

//en kalender som laddas om vid navigation
//start - datum
//events -array
//type - "week" / "month" ...
//repetitions:
/*
 *
 *
 * */
	function print_calendar($start, $events, $type, $repetitions=false){
		$start_original = $start;
		$weekday = $start->format("w"); //0 is sunday
		$use_nav = true;

		error_log("print_calendar, med start: ". print_r($start, true) . " och events " . count($events) . "");
		$hour_division = 1; /*1= whole h, 2= half hours, 3= 20-min-sections, 4= 15 min sections*/
		$wdays = array('måndag','tisdag','onsdag','torsdag','fredag','lördag','söndag');
		echo "<div class='calendar'>";
		if($type == 'week'){

			//get mondays date
			

			$monday = clone $start;

			switch($weekday){
			case 0:
				$i = 6;
				break;
			case 1:
				$i = 0;
				break;
			case 2:
				$i = 1;
				break;
			case 3:
				$i = 2;
				break;
			case 4:
				$i = 3;
				break;
			case 5:
				$i = 4;
				break;
			case 6:
				$i = 5;
				break;
			}
			$format_interval = "P$i"."D";
			//set mondays date
			$monday->sub(new DateInterval($format_interval));

			//error_log("Found monday to be at " . $monday->format("Y-m-d"));

			$headers_date = clone $monday;

			//TODO: allow other weeks than current week, using $start

			$week_now = $start->format('W');
			$next_week = clone $monday;
			$next_week->add(new DateInterval("P7D"));

			$prev_week = clone $monday;
			$prev_week->sub(new DateInterval("P7D"));

			$link = site_url('jobs/calendar/week/');

			$year_string = /*In case a week goes over two greg.cal years, print both*/
				($start->format("o") == $start->format("Y")) ?
					$start->format("o") :
					$start->format("Y") . "-" . $start->format("o");
			
			echo "<table>";
			
			echo "<caption>";
			if($use_nav) echo "<a id='cal_nav_left' href='$link".($prev_week->format('Y-m-d'))."'><<</a>";
			echo "<span>Vecka $week_now , $year_string</span>";
			if($use_nav) echo "<a id='cal_nav_right' href='$link".($next_week->format('Y-m-d'))."'>>></a>";
			echo "</caption>";

			//gå igenom timmar:
			for($h=-1; $h<24*$hour_division; $h++){
				echo "<tr>";
				if($h <0){
					$cellt = "th";
				}
				else{
					$cellt = "td";
				}

				echo "<$cellt>";

				if($cellt!="th"){
					echo float_to_hour_minute($h/$hour_division);
				}
				else{
					echo "tid";
				}
				echo "</$cellt>";

				$day_count = 0;
				foreach($wdays as $wday){
					$cont = null;	
					if($h <0){
						//rubrik text
						$cont = $wday . " " . $headers_date->format("d M");
						//error_log("$cont");
						$headers_date->add(new DateInterval("P1D"));
					}
					$class_str = "";
					$cell_data = "";
					$c_data = "";
					if($cont == null){


						//date handling
						if($day_count>0){
							$format_interval = "P1D";//"P$day_count" . "D";
							$start->add(new DateInterval($format_interval));

						}
						else{
							//reset to first day
							$start = clone $monday;
							//New row in table
						}
						$day_count++;

						//timestamp
						$timestamp = $start->format("Y-m-d") . " " . float_to_hour_minute($h/$hour_division);

						//ending timestamp
						$end_timest = $start->format("Y-m-d") . " " . float_to_hour_minute(($h+1)/$hour_division);

						//med start och sluttid kan bokningar
						//jämföras där tidsmässigt
						//se om de innefattas

						$cell_st = new DateTime($timestamp);
						$cell_end = new DateTime($end_timest);


						$c_data = "";
						$plats = "";
						$beskrivning = "";
						$tid_info = "";
						$tid_info2 = "";
						$cell_text = "";
						foreach($events as $event){
							$id = intval($event["id"])* 3 + 4;//obfusc
							$evnt_time1 = new DateTime($event["datum_start"]);
							$evnt_time2 = new DateTime($event["datum_slut"]);
							$arb_plats = $event["arbetsplats-namn"];
							$beskrivning = $event["beskrivning"];
							$tid_info = $event["datum_start"];
							$tid_info2 = $event["datum_slut"];
							//if same day don't need date

							$ds = new DateTime($tid_info);/*start*/
							$dsl = new DateTime($tid_info2);/*slut*/



							if(is_within_dates($evnt_time1, $cell_st, $cell_end) ||
								is_within_dates($evnt_time2, $cell_st, $cell_end, false)){
								if(is_same_day($cell_st, $ds)){
									$tid_info = $ds->format("H:i");
								}
								else{
									$tid_info = $ds->format("Y-m-d H:i");
								}
								if(is_same_day($cell_st, $dsl)){
									$tid_info2 = $dsl->format("H:i");
								}
								else{
									$tid_info2 = $dsl->format("Y-m-d H:i");
								}
								$c_data .= "$id,";
								$plats = $arb_plats;
								$cell_text .= "<span class='e$id'>$plats&nbsp;&nbsp;: $beskrivning<br>Start: $tid_info, slut: $tid_info2<br></span>";
							}
							else if(
								$evnt_time1 < $cell_st &&
								$evnt_time2 > $cell_end){
								error_log("id $id overlaps datetime starting " . $cell_st->format("d H:i"));
								if(is_same_day($cell_st, $ds)){
									$tid_info = $ds->format("H:i");
								}
								else{
									$tid_info = $ds->format("Y-m-d H:i");
								}

								if(is_same_day($cell_st, $dsl)){
									$tid_info2 = $dsl->format("H:i");
								}
								else{
									$tid_info2 = $dsl->format("Y-m-d H:i");
								}
								$c_data .= "$id,";
								$plats = $arb_plats;
																
								$cell_text .= "<span class='e$id'>" . $plats . "&nbsp;&nbsp;: $beskrivning<br>Start: $tid_info, slut: $tid_info2<br></span>";
								/*bokningen överlappar helt*/
							}

						}
						if($repetitions){
							//error_log("There are repetitions: " . count($repetitions));
							
							foreach($repetitions as $rep_job){
								//error_log(print_r($rep_job, true));
								$rep_j = $rep_job;
								foreach($rep_j as $rep){
									$r = $rep;
									$es = new DateTime($r["datum_start"]);
									$ee = new DateTime($r["datum_slut"]);
									//error_log(print_r($r, true));
									if(span_touches_span($cell_st, $cell_end, $es, $ee))
									{ $cell_text .= "r"; }
								}
							}
						}
					}


					if($c_data != ""){
						$event_count = substr_count($c_data, ",");
						$mx_css_cls = 11;//see css,booked11
						$classn = $event_count<=$mx_css_cls ? $event_count : $mx_css_cls;

						if($event_count == 1){
							$class_str = "class='booked'";
						}
						else if($event_count > 1){
							$class_str = "class='booked$classn'";
						}
						$c_data = rtrim($c_data, ",");
						$cell_data = "data-jobs='" . $c_data . "'";
					}


					echo "<$cellt $class_str$cell_data>";
					if($cont){
						//table headings
						echo $cont;
					}
					else if($c_data != ""){
						echo $cell_text;
					}
					else if(isset($cell_text)){
						echo "$cell_text";
					}

					echo "</$cellt>";


				}
				echo "</tr>";
			}

			echo "</table>";
		}
		else if($type == "month"){
			if($repetitions){
				error_log(print_r($repetitions, true));// array of 'yyyy-mm-dd' ...
			}
			$m = $start->format("m");
			$Y = $start->format("Y");
			$DayWalker = new DateTime("$Y-$m-01");
			$monthFirstWD = $DayWalker->format("w");
			if($monthFirstWD==0){ $monthFirstWD=7; }//sund
			$preDays = (1 - $monthFirstWD) * -1;
			error_log("preDays: $preDays");

			echo "<table>";
			echo "<caption>".
				"Kalender " . $DayWalker->format("Y-m") . "</caption>";
			//preDays
			$rowCount = 0;
			$finished = false;

			do{
				echo "<tr>";

				if($rowCount ==0){
					for($i=0; $i<$preDays; $i++){
						echo "<td></td>";
					}
				}
				do{
					
					$isRep = false;
					$classN = "hasEvent";
					$classN2 = "originEvent"; //ClassName2
					if($repetitions){
					foreach($repetitions as $key => $val){
						$isRep = ($DayWalker->format("Y-m-d") == $val);
						if($isRep){

							unset($repetitions[$key]);
							break;
						}
					}
					}

					$isOrigin = false;
					foreach($events as $event){
						/*[30-Jul-2019 04:52:50 Europe/Berlin] ----------- Array
(
    [id] => 31
    [arb_pl_id] => 10
    [arb_plats] => Hemma
    [datum_start] => 2019-07-30 04:26:00
    [datum_slut] => 2019-07-30 05:30:00
    [beskrivning] => Jobba med Aiam_rapport
    [schema_id] => 29
*/
						//error_log("----------- " . print_r($event, true) . " -----------");
						$cell_st = new DateTime($DayWalker->format("Y-m-d 00:00"));
						$cell_end = new DateTime($DayWalker->format("Y-m-d 23:59"));
						$es = new DateTime($event["datum_start"]);
						$ee = new DateTime($event["datum_slut"]);
						$isOrigin = span_touches_span($cell_st, $cell_end, $es, $ee);
					}

					$innerHTML = ($rowCount>1 && $DayWalker->format("d")<7) ? "" :
						$DayWalker->format("d");//a number for each cell

					if($isRep) {
						$cellHTML =
							"<td class='$classN'>$innerHTML</td>";
					}
					else if($isOrigin){
						$cellHTML = "<td class='$classN2'>$innerHTML</td>";
					}
					else{
						$cellHTML = "<td>$innerHTML</td>";
					}

					echo $cellHTML;

					$DayWalker->modify("+1 day");
				} while($DayWalker->format("w") != 1);//while not monday
				$rowCount++;
				echo "</tr>";
				$finished = $m != $DayWalker->format("m");/*if month has passed*/
			}while(!$finished);
			echo "</table>";	
		}
		echo "</div>";
	}

	/*4 DateTime args*/
	/*use first pair for cell, 2:nd for event*/
	function span_touches_span($s1_s, $s1_e, $s2_s, $s2_e){
		//$tid_info = $s2_s;
		//$tid_info2 = $s2_e;
		//if same day don't need date

		//$ds = new DateTime($tid_info);
		//$dsl = new DateTime($tid_info2);   


		if(is_within_dates($s2_s, $s1_s, $s1_e) ||
			is_within_dates($s2_e, $s1_s, $s1_e, false)){
			return true;
		}
		else if($s2_s < $s1_s && $s2_e > $s1_e){
			return true;
		}
		return false;

	}

	//takes $date string ("2018-01-01 10:11"), code "14w2m2"
	function list_schedule_from_date($stem, $code, $repetitions = 10){
		/* System for codes of schedules
		 * a string describes repetitions
		 * a maximal code could be 1234567d2w2m2x
		 * the first digits are (optional) week days, starting from 1 (monday)
		 * the "d2" means every other day. "d1" means every day. "d3", every third
		 * "w2" means every other week, "w1"/"w" every week, "w3" every third
		 * "m2" means every other month ...
		 * x (optional) means include both the numbered day(s) with the original day
		 * will not be impl. at first
		 * if both numbered days are and a "d2" or similar without x, the numbered days are to be ignored
		 *
		 * Default value should be d1 - every day
		 * Time of day must be specified in stem date
		 *Other examples: mondays and fridays "15", like stem day (weekday) but every other week "w2", monday once a month 1m1
		 * an empty code means "like stem day every day"
		 * m1 means like stem day, but once a (calendar) month (can be just "m")
		 * A single "m" clear week/day
		 * A single "w" clear d
		 * Only digits (12 or 1234567), means weekly repetition
		 */

		error_log("list_schedule_from_date($stem, $code, $repetitions)");

		$d=0;//day is 1 as default, meaning every day, if no w(eek) or m(onth) are found
		$w=0;
		$m=0;

		$code = strtolower($code);

		if($code == ""){
			$code == "d";//everyday
		}

		$array = str_split($code);

		$levels = array("day","week","month");
		$level =0;//1=day 2=week 3=month

		$gotD = false;
		$gotW = false;
		$gotM = false;
		$gotX = false;

		$count = 0;
		$hasWeekdays=false;//any inital digits representing week days, like 12 mond tuesd
		$weekDays = array();
		$letterCount =0;
		$isCombo = substr($code, -1) == 'x';
		foreach($array as $char){

			if($count==0){
				$hasWeekdays = ctype_digit($char);
			}


			if(ctype_alpha($char)){
				$letterCount++;

				if($char == "d"){
					if(!$gotD && !$gotW && !$gotM){
						$level=1;
					}
					else{
						echo "Fel: d i fel plats";
						return false;
					}
				}
				else if($char == "w"){
					if(!$gotW && !$gotM){
						$level =2;
					}
					else{
						echo "Fel: w i fel plats";
						return false;
					}
				}
				else if($char == "m"){
					if(!$gotM){
						$level = 3;
					}
					else{
						echo "Fel: m i fel plats";
						return false;
					}
				}
				else if($char == "x"){
					/*$gotX = true;/*not impl*/
					echo "Fel: x ej impl";
					return false;
				}
			}
			else if(ctype_digit($char)){/*siffra*/
				if($level == 0){
					$iv = intval($char);
					if($iv==0) $iv=7;/*sunday*/
					if($iv<1 || $iv>7){
						echo "Fel: för hög dagsiffra";
						return false;
					}
					else if(in_array($iv, $weekDays)){
						echo "dublett av dagsiffra";
						return false;
					}
					$weekDays[] =$iv;
				}
				else if($level == 1){
					/*days interv?*/
					$d .= $char;
				}
				else if($level == 2){
					if($w==0){ $w=""; }
					$w .= $char;
				}
				else if($level == 3){
					if($m==0){ $m=""; }
					$m .= $char;
				}
				
			}
			$count++;
		}
		if($hasWeekdays){
			$d=1;/*step*/
		}
		else if($w==0 && $m==0 && $d==0){
			$d=1;
		}
		if($w<2){
			$gotW = false;
		}/*this is used for combinations of d and w*/
		else{
			$gotW = true;
		}
		if($m<2){
			$gotM = false;
		}/*this means no interrupt*/
		else{
			$gotM = true;
		}



		error_log("d $d, w $w, m $m");
		if($hasWeekdays){
			error_log("has weekdays");
		}


		
		$intervald = "+$d day";
		$intervalw = "+$w week";
		$intervalm = "+$m month";

		$date_ = new DateTime($stem);

		echo "<pre>";

		if(!$hasWeekdays){
			echo $date_->format("Y-m-d H:i") . "\n";
		}
		else{
			
			echo "Har fått dagar: " . print_r($weekDays, true);
			if(in_array(7, $weekDays)){
				echo " 0-baserar, "; 
				$weekDays[] = 0; //sunday
			}
			print_r($weekDays);
			if(in_array(intval($date_->format("w")), $weekDays)){
				echo "Startdatum är på en av dessa, " . intval($date_->format("w")). "\n";
				/*om startdatum är på en vald dag, 1-7*/
				echo $date_->format("Y-m-d H:i") . "\n";
			}
		}

		/*
		if($gotD && $gotW && $gotM){//test
			for($mo = intval($date_->format("m")); ; $date_->modify("+".($m-1)." month")){
				for($we = intval($date_->format("W")); ; $date_->modify("+".($w-1)." week")){
					for($da = intval($date_->format("d"));;$date_->modify("+".($d-1)." day")){
						echo "$mo $we $da<br>";
					}
				}
			}
			return;
		}*/

		//annan metod:
		$lastMonth = $date_->format("m");//startmånad
		$lastWeek = $date_->format("W");//startv
		$lastDay = $date_->format("d");//startd


		for($i=0; $i<$repetitions;){
			if($d>0){
				$date_->modify($intervald);
			}
			else if($w>0){
				$date_->modify($intervalw);
			}
			else if($m>0){
				$date_->modify($intervalm);
			}

			$newWeek = $date_->format("W");
			$newMonth = $date_->format("m");
			if($gotW && $newWeek != $lastWeek){
				//$date_->modify("+".($w-1)." week");
				$lastWeek = $newWeek;
				continue;
			}
			if($gotM && $newMonth != $lastMonth){
				//$date_->modify("+".($m-1)." month");
			}

			if($hasWeekdays){
				if(in_array(intval($date_->format("w")), $weekDays)){
					echo $date_->format("Y-m-d H:i") . "\n";
					$i++;
				}
			}
			else{
				echo $date_->format("Y-m-d H:i") . "\n";
				$i++;
			}
			$lastWeek = $newWeek;
			$lastMonth = $newMonth;
		}
		echo "</pre>";
	}

	function is_same_day($dateTime1, $dateTime2){
		return $dateTime1->format("Y-m-d") == $dateTime2->format("Y-m-d");
	}

	function float_to_hour_minute($hour){
		$h = intval($hour);
		$rest = $hour - $h;

		$leading_z = $h<10 ? "0" : "";

		$m = intval($rest * 60);

		if($m == 0){
			return "$leading_z$h:00";
		}

		return "$leading_z$h:$m";
	}

	/*3 DateTime args, one bool*/
	function is_within_dates($event, $start, $end, $event_is_start=true){
		if($event_is_start){
			$res = ($event >= $start && $event < $end);
		}
		else{
			$res = ($event > $start && $event <= $end);
		}
		//if($res) error_log("Kollar om " . $event->format("Y-m-d H:i") . " är inom " . $start->format("Y-m-d H:i") . " och " .$end->format("Y-m-d H:i") . " ($event_is_start) " .$res);
		return $res;
	}
?>
