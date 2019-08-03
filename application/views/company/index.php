<div class="form_holder">
<fieldset>
<?php
echo validation_errors();//"<span>","</span>"

if(isset($reuse)){

	$id = $reuse["id"];
	$name = $reuse["name"];
	$street = $reuse["street"];
	$postal_code = $reuse["postal_code"];
	$phone = $reuse["phone"];
	$cctld = $reuse["cctld"];
	$email = $reuse["email"];
	$controlled_mode = $reuse["controlled_mode"];

}


$comp_id = array("id"=>$id);

error_log("View got controlled_mode $controlled_mode");
$checked = $controlled_mode == 1 ? "checked" : "";

?>
<?=form_open('company/update', '', $comp_id);?>
<div class='inline_info'>
<label>Namn</label>
<?=form_input('name',$name);?>
</div>
<div class='inline_info'>
<label>Gatuadress</label>
<?=form_input('street',$street);?>
</div>
<div class='inline_info'>
<label>Postnummer</label>
<?=form_input('postal_code', $postal_code);?>
</div>
<div class='inline_info'>
<label>Telefon</label>
<?=form_input('phone', $phone);?>
</div>
<div class='inline_info'>
<label>Internet lands-kod</label>
<?php
$attrs_cctls = array('name' => 'cctld', 'id' => 'cctld', 'value' => $cctld);

echo form_input($attrs_cctls);
?>
</div>
<div class='inline_info'>
<label>Email</label>
<?=form_input('email', $email);?>
</div>
<div class='inline_info'>
<label>Kontrollerat läge</label>&nbsp;<span class="question"></span><br>
<input type="checkbox" name="controlled_mode" value="controlled_mode" <?=$checked?>>
</div>
<div class='inline_info'>
<label>&nbsp;</label>
<?=form_submit('submit','Uppdatera')?>
</div>
    
</form>
<span class="question"></span>&nbsp;&nbsp;Om den är markerad ska personal endast kunna stämpla in på jobb som de tilldelats (med ett registrerat arbetstillfälle)
    </fieldset>

</div>

<section class="message_section" id="message_section">
</section>

<script>

window.onload = function(){

	var landCodes = "Country	ISO Code<br>AFGHANISTAN	AF<br>ALBANIA	AL<br>ALGERIA	DZ<br>AMERICAN SAMOA	AS<br>ANDORRA	AD<br>ANGOLA	AO<br>ANTARCTICA	AQ<br>ANTIGUA AND BARBUDA	AG<br>ARGENTINA	AR<br>ARMENIA	AM<br>ARUBA	AW<br>AUSTRALIA	AU<br>AUSTRIA	AT<br>AZERBAIJAN	AZ<br>BAHAMAS	BS<br>BAHRAIN	BH<br>BANGLADESH	BD<br>BARBADOS	BB<br>BELARUS	BY<br>BELGIUM	BE<br>BELIZE	BZ<br>BENIN	BJ<br>BERMUDA	BM<br>BHUTAN	BT<br>BOLIVIA	BO<br>BOSNIA AND HERZEGOVINA	BA<br>BOTSWANA	BW<br>BOUVET ISLAND	BV<br>BRAZIL	BR<br>BRITISH INDIAN OCEAN TERRITORY	IO<br>BRUNEI DARUSSALAM	BN<br>BULGARIA	BG<br>BURKINA FASO	BF<br>BURUNDI	BI<br>CAMBODIA	KH<br>CAMEROON	CM<br>CANADA	CA<br>CAPE VERDE	CV<br>CAYMAN ISLANDS	KY<br>CENTRAL AFRICAN REPUBLIC	CF<br>CHAD	TD<br>CHILE	CL<br>CHINA	CN<br>CHRISTMAS ISLAND	CX<br>COCOS (KEELING) ISLANDS	CC<br>COLOMBIA	CO<br>COMOROS	KM<br>CONGO	CG<br>CONGO, THE DEMOCRATIC REPUBLIC OF THE	CD<br>COOK ISLANDS	CK<br>COSTA RICA	CR<br>CÔTE D'IVOIRE	CI<br>CROATIA	HR<br>CUBA	CU<br>CYPRUS	CY<br>CZECH REPUBLIC	CZ<br>DENMARK	DK<br>DJIBOUTI	DJ<br>DOMINICA	DM<br>DOMINICAN REPUBLIC	DO<br>ECUADOR	EC<br>EGYPT	EG<br>EL SALVADOR	SV<br>EQUATORIAL GUINEA	GQ<br>ERITREA	ER<br>ESTONIA	EE<br>ETHIOPIA	ET<br>FALKLAND ISLANDS (MALVINAS)	FK<br>FAROE ISLANDS	FO<br>FIJI	FJ<br>FINLAND	FI<br>FRANCE	FR<br>FRENCH GUIANA	GF<br>FRENCH POLYNESIA	PF<br>FRENCH SOUTHERN TERRITORIES	TF<br>GABON	GA<br>GAMBIA	GM<br>GEORGIA	GE<br>GERMANY	DE<br>GHANA	GH<br>GIBRALTAR	GI<br>GREECE	GR<br>GREENLAND	GL<br>GRENADA	GD<br>GUADELOUPE	GP<br>GUAM	GU<br>GUATEMALA	GT<br>GUINEA	GN<br>GUINEA-BISSAU	GW<br>GUYANA	GY<br>HAITI	HT<br>HEARD ISLAND AND MCDONALD ISLANDS	HM<br>HONDURAS	HN<br>HONG KONG	HK<br>HUNGARY	HU<br>ICELAND	IS<br>INDIA	IN<br>INDONESIA	ID<br>IRAN, ISLAMIC REPUBLIC OF	IR<br>IRAQ	IQ<br>IRELAND	IE<br>ISRAEL	IL<br>ITALY	IT<br>JAMAICA	JM<br>JAPAN	JP<br>JORDAN	JO<br>KAZAKHSTAN	KZ<br>KENYA	KE<br>KIRIBATI	KI<br>KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF	KP<br>KOREA, REPUBLIC OF	KR<br>KUWAIT	KW<br>KYRGYZSTAN	KG<br>LAO PEOPLE'S DEMOCRATIC REPUBLIC (LAOS)	LA<br>LATVIA	LV<br>LEBANON	LB<br>LESOTHO	LS<br>LIBERIA	LR<br>LIBYAN ARAB JAMAHIRIYA	LY<br>LIECHTENSTEIN	LI<br>LITHUANIA	LT<br>LUXEMBOURG	LU<br>MACAO	MO<br>MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF	MK<br>MADAGASCAR	MG<br>MALAWI	MW<br>MALAYSIA	MY<br>MALDIVES	MV<br>MALI	ML<br>MALTA	MT<br>MARSHALL ISLANDS	MH<br>MARTINIQUE	MQ<br>MAURITANIA	MR<br>MAURITIUS	MU<br>MAYOTTE	YT<br>MEXICO	MX<br>MICRONESIA, FEDERATED STATES OF	FM<br>MOLDOVA, REPUBLIC OF	MD<br>MONACO	MC<br>MONGOLIA	MN<br>MONTENEGRO	ME<br>MONTSERRAT	MS<br>MOROCCO	MA<br>MOZAMBIQUE	MZ<br>MYANMAR	MM<br>NAMIBIA	NA<br>NAURU	NR<br>NEPAL	NP<br>NETHERLANDS	NL<br>NETHERLANDS ANTILLES	AN<br>NEW CALEDONIA	NC<br>NEW ZEALAND	NZ<br>NICARAGUA	NI<br>NIGER	NE<br>NIGERIA	NG<br>NIUE	NU<br>NORFOLK ISLAND	NF<br>NORTHERN MARIANA ISLANDS	MP<br>NORWAY	NO<br>OMAN	OM<br>PAKISTAN	PK<br>PALAU	PW<br>PALESTINIAN TERRITORY, OCCUPIED	PS<br>PANAMA	PA<br>PAPUA NEW GUINEA	PG<br>PARAGUAY	PY<br>PERU	PE<br>PHILIPPINES	PH<br>PITCAIRN	PN<br>POLAND	PL<br>PORTUGAL	PT<br>PUERTO RICO	PR<br>QATAR	QA<br>RÉUNION	RE<br>ROMANIA	RO<br>RUSSIAN FEDERATION	RU<br>RWANDA	RW<br>SAINT HELENA	SH<br>SAINT KITTS AND NEVIS	KN<br>SAINT LUCIA	LC<br>SAINT PIERRE AND MIQUELON	PM<br>SAINT VINCENT AND THE GRENADINES	VC<br>SAMOA	WS<br>SAN MARINO	SM<br>SAO TOME AND PRINCIPE	ST<br>SAUDI ARABIA	SA<br>SENEGAL	SN<br>SERBIA	RS<br>SEYCHELLES	SC<br>SIERRA LEONE	SL<br>SINGAPORE	SG<br>SLOVAKIA	SK<br>SLOVENIA	SI<br>SOLOMON ISLANDS	SB<br>SOMALIA	SO<br>SOUTH AFRICA	ZA<br>SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS	GS<br>SPAIN	ES<br>SRI LANKA	LK<br>SUDAN	SD<br>SURINAME	SR<br>SVALBARD AND JAN MAYEN	SJ<br>SWAZILAND	SZ<br>SWEDEN	SE<br>SWITZERLAND	CH<br>SYRIAN ARAB REPUBLIC	SY<br>TAIWAN	TW<br>TAJIKISTAN	TJ<br>TANZANIA, UNITED REPUBLIC OF	TZ<br>THAILAND	TH<br>TIMOR-LESTE	TL<br>TOGO	TG<br>TOKELAU	TK<br>TONGA	TO<br>TRINIDAD AND TOBAGO	TT<br>TUNISIA	TN<br>TURKEY	TR<br>TURKMENISTAN	TM<br>TURKS AND CAICOS ISLANDS	TC<br>TUVALU	TV<br>UGANDA	UG<br>UKRAINE	UA<br>UNITED ARAB EMIRATES	AE<br>UNITED KINGDOM	GB<br>UNITED STATES	US<br>UNITED STATES MINOR OUTLYING ISLANDS	UM<br>URUGUAY	UY<br>UZBEKISTAN	UZ<br>VANUATU	VU<br>VENEZUELA	VE<br>VIET NAM	VN<br>VIRGIN ISLANDS, BRITISH	VG<br>VIRGIN ISLANDS, U.S.	VI<br>WALLIS AND FUTUNA	WF<br>WESTERN SAHARA	EH<br>YEMEN	YE<br>ZAMBIA	ZM<br>ZIMBABWE	ZW";

	var cctld = document.querySelector("#cctld");
	cctld.addEventListener("click", function(){
		document.querySelector("#message_section").innerHTML = landCodes;
	});
	cctld.addEventListener("blur", function(){
		document.querySelector("#message_section").innerHTML = "";
	});

}

</script>
