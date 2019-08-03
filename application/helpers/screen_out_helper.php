<?php
defined('BASEPATH') OR exit('No direct script access allowed');//?

if ( ! function_exists('print_sql_table'))
{
	/**
	 * print array table
	 * Made to print database tables, with link for editing each row
	 * The cooresponding value for key_name is encoded with base64_encode function
	 *
	 * @param	array - the 2-dimensional array that should be printed as a html-table
	 * @param   edit_page - url of edit page or view-page
	 * @param   key_name - name of the 
	 * @return	-
	 */
	function print_array_table($array, $edit_page=FALSE, $key_name=FALSE)
	{
		//TODO: make id column hidden (css)
		//error_log("print_array_table " . print_r($array, true));
		$server_name = $_SERVER['SERVER_NAME'];

		$link = substr($edit_page,stripos($edit_page,'/')+1);

		if(! is_array($array)){
			echo("Nothing to print");
			exit;
		}

		if (count($array) > 0): ?>
	<table>
	    <thead>
		<tr>
		    <th><?php echo implode('</th><th>', array_keys(current($array))); ?></th>
		</tr>
	    </thead>
	<tbody>
<?php
			if($key_name === FALSE){}
?>
	<?php foreach ($array as $row): array_map('htmlentities', $row); ?>
	    <tr>
		<?php if($edit_page===FALSE || $key_name === FALSE){ //båda behövs ju ?>
		<td><?php echo implode('</td><td>', $row); ?></td>
<?php }
else if(is_numeric($row[$key_name])){
	echo "<td>".implode('</td><td>', $row)."</a></td>";
	echo "<td><a href='".base_url()."index.php/$edit_page/".base64_encode($row[$key_name])."'>$link</a></td>";//var redigera
} ?>
	    </tr>
	<?php endforeach; ?>
	<tbody>
	</table>
<?php endif;
//else skriv ut: Inga rader
	}

	function print_array_table_divs($array,$edit_page=FALSE, $key_name=FALSE){
		echo "<div>";

$server_name = $_SERVER['SERVER_NAME'];

		$link = substr($edit_page,stripos($edit_page,'/')+1);

		if (count($array) > 0): ?>
		    <div><?php echo implode('</div><div>', array_keys(current($array))); ?></div>


		<?php foreach ($array as $row): array_map('htmlentities', $row); ?>
	    	<tr>
		<?php if($edit_page===FALSE || $key_name === FALSE){ //båda behövs ju ?>
			<td><?php echo implode('</div><div>', $row); ?></td>
		<?php }
		else if(is_numeric($row[$key_name])){
		echo "<div>".implode('</div><div>', $row)."</a></div>";
		echo "<div><a href='".base_url()."index.php/$edit_page/".base64_encode($row[$key_name])."'>$link</a></div>";//var redigera
			} ?>
		</tr>
		<?php endforeach; ?>

		<?php
			echo "</div>";

		endif;
	}

}
	/*arg selected_tbl - (string) villken tabell (<td>) som ska markeras (company, kund, arbetsplats, arbetstillfälle, personal)*/
	function get_table_chain($selected_tbl=FALSE){
		/*use selected*/
		$selected = array();
		$selected[] = $selected_tbl=='company'?'selected':'';
		$selected[] = $selected_tbl=='kund'?'selected':'';
		$selected[] = $selected_tbl=='arbetsplats'?'selected':'';
		$selected[] = $selected_tbl=='arbetstillfälle'?'selected':'';
		$selected[] = $selected_tbl=='personal'?'selected':'';

		foreach($selected as $key => &$val){
			if(isset($val)){
				$val = "class='" . $val . "'";/*add 'class=' before*/
			}
		}/*only one will have 'class=...'*/





		$html = "<table class='db_chain' style='border: 3px outset gray;background: rgba(255,255,255,.9);' title='Översikt över inblandade tabeller'>
			<tr>
<td rowspan='2' ".$selected[0].">Company</td>
<td>*&ndash;*</td>
<td rowspan='2' ".$selected[1].">Kund</td>
<td>*&ndash;*</td>
<td rowspan='2' ".$selected[2].">Arbetsplats</td>
<td>1&ndash;*</td>
<td rowspan='2' ".$selected[3].">Arbetstillfälle</td>
<td>*&ndash;*</td>
<td rowspan='2' ".$selected[4].">Personal</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>";
		return $html;
	}

function get_table_chain_staff($selected_tbl=FALSE){
		/*use selected*/
		$selected = array();
		$selected[] = $selected_tbl=='personal'?'selected':'';
		$selected[] = $selected_tbl=='arbets_tillfalle'?'selected':'';
		$selected[] = $selected_tbl=='arbetsplats'?'selected':'';
		$selected[] = $selected_tbl=='jobb_schema'?'selected':'';

		foreach($selected as $key => &$val){
			if(isset($val)){
				$val = "class='" . $val . "'";/*add 'class=' before*/
			}
		}/*only one will have 'class=...'*/





		$html = "<table class='db_chain' style='border: 3px outset gray;background: rgba(255,255,255,.9);'>
			<tr>
<td rowspan='2' ".$selected[0].">Personal</td>
<td>*&ndash;*</td>
<td rowspan='2' ".$selected[1].">Arbetstillfälle</td>
<td>*&ndash;1</td>
<td rowspan='2' ".$selected[2].">Arbetsplats</td>
<td>|</td>
<td rowspan='2' ".$selected[3].">Schema</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>";
		return $html;
	}

	function header_view_footer($view, $data=FALSE){
		$CI = &get_instance();

		if($data == FALSE){
			$CI->load->view('templates/header');
		}
		else{
			$CI->load->view('templates/header', $data);
		}

		$CI->load->view($view);
		$CI->load->view('templates/footer');
	}


	function print_breadcrumb($use_links=false){
		$CI = &get_instance();
		$tots = $CI->uri->total_segments();
		$string = "";
		$path = "";
		$links = array();
		$pages = array();
		for($i=1; $i<=$tots; $i++){
			if($i>2) break;//to hide parameters

			$link1 = "<a href='#'>";
			$link2 = "</a>";
			$string .= "> " . $CI->uri->segment($i) . " ";
			$path .= $CI->uri->segment($i) . "/";
			$links[] = base_url() . "index.php/" . $path;
			$pages[] = $CI->uri->segment($i);
		}
		if(! $use_links){
			echo $string;
		}
		else{
			$nrp= count($pages);
			$i=0;
			for(; $i< $nrp-1; $i++){
				$page = $pages[$i];
				echo "<a href='".$links[$i]."'>> $page</a>&nbsp;&nbsp;";
			}
			echo "> " . $pages[$i];
		}


	}

	function text_boolean_of_expression($expression){
	    if($expression){
	        return "TRUE";
        }
	    return "FALSE";
    }
?>
