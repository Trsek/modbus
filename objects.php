<?php
define('DATEOFFSET1970', 946684800);

/********************************************************************
 * @brief Rot hex to other endian
 */
function rotOrder($cut_str, $len)
{
	$answer = "";
	while( $len-- ) 
	{
		$answer .= substr($cut_str, 2*$len, 2);
		$cut_str = substr($cut_str, 0, 2*$len);
	}
	
	return $answer;
}

/********************************************************************
* @brief Strip 'len' chars from start of string 
*/
function substr_cut(&$SMS, $len)
{
	if( $len < 0 )
	{
		$cut_str = substr($SMS, 2*$len);
		$SMS = substr($SMS, 0, strlen($SMS) + 2*$len);	// $len je -
	}
	else
	{
		$cut_str = substr($SMS, 0, 2*$len);
		$SMS = substr($SMS, 2*$len, strlen($SMS) - 2*$len);
	}
	return $cut_str;
}

/********************************************************************
* @brief hex2bin in PHP < 5.4.0
*/
if (PHP_VERSION_ID < 50400) {
function hex2bin($hex_string)
{
	return pack("H*" , $hex_string);
}
}

/********************************************************************
* @brief Convert hex to string
*/
function hexToStr($hex)
{
	$string='';
	for ($i=0; $i < strlen($hex)-1; $i+=2){
		$string .= chr(hexdec($hex[$i].$hex[$i+1]));
	}
	return $string;
}

/********************************************************************
* @brief Modbus date presentation
*/
function modbus_date($DATI)
{
	$data_second = hexdec(substr_cut($DATI, 1));
	$data_minute = hexdec(substr_cut($DATI, 1));	
	$data_hour   = hexdec(substr_cut($DATI, 1));	
	$data_day    = hexdec(substr_cut($DATI, 1));	
	$data_month  = hexdec(substr_cut($DATI, 1));	
	$data_year   = hexdec(substr_cut($DATI, 1))+2000;	
	
	return sprintf("%04d-%02d-%02d %02d:%02d:%02d", $data_year, $data_month, $data_day, $data_hour, $data_minute, $data_second);
}

/********************************************************************
 * @brief Modbus date presentation as long
 */
function modbus_actTime(&$DATI)
{
    $C_MONTH  = array(0,31,59,90,120,151,181,212,243,273,304,334,365,366);
    $C_MONTHP = array(0,31,60,91,121,152,182,213,244,274,305,335,366,366);
    
    $actTime = hexdec(substr_cut($DATI,4,true)) - DATEOFFSET1970;
    
    $second = $actTime % 60;	$actTime = (int)($actTime / 60);		// je to v min
    $minute = $actTime % 60;	$actTime = (int)($actTime / 60);		// je to v hod
    $hour   = $actTime % 24;	$actTime = (int)($actTime / 24);		// je to v dnech
    $year   = (int)($actTime / 365);
    
    $days = (int)((365 * $year + ($year / 4) + (($year % 4)? 1: 0)));
    if ( $actTime < $days )
    {
        $year -= 1;
        $days = (int)((365 * $year + ($year / 4) + (($year % 4)? 1: 0)));
    }
    $actTime -= $days;
    $j = 0;
    if ( $year % 4 )     // normalni roky
    {
        while ( $C_MONTH[$j] <= $actTime && $j < sizeof($C_MONTH)) {
            $j++;
        }
        $j--;
        $month = $j;
        $day = $actTime - $C_MONTH[$j];
    }
    else                            // prestupny rok
    {
        while ( $C_MONTHP[$j] <= $actTime && $j < sizeof($C_MONTHP)) {
            $j++;
        }
        $j--;
        $month = $j;
        $day = $actTime - $C_MONTHP[$j];
    }
    $day += 1;
    $month += 1;
    
    return sprintf("20%02d-%02d-%02d %02d:%02d:%02d", $year, $month, $day, $hour, $minute, $second);
}

/********************************************************************
* @brief When need value to one line
*/
function array_val_line($value)
{
	if(!is_array($value))
		return $value[0];

	$answer = "";
	foreach ($value[0] as $value_line)
	{
		$answer .= (empty($answer)? "": ", "). $value_line;
	}
	return $answer;
}

/********************************************************************
 * @brief Hex convert to float format
 */
function hexFloat(&$DATI, $deci = 9)
{
    $fnumber = unpack("f", pack('H*',str_pad($DATI,8,"0")));
    return round($fnumber[1], $deci);
}

/********************************************************************
 * @brief Hex convert to double
 */
function hexDouble(&$DATI)
{
    $fnumber = unpack("d", pack('H*',str_pad($DATI,16,"0")));
    return $fnumber[1];
}

/*----------------------------------------------------------------------------*/
/* END OF FILE */
