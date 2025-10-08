<?php
include("cnx_db.php");
$res = mysql_query("SELECT cvepersonal, date(fechahora) as fecha FROM checada_lector GROUP BY cvepersonal, date(fechahora)");
while($row = mysql_fetch_assoc($res)){
	$checadas=0;
	$res1 = mysql_query("SELECT cve FROM checada_lector WHERE cvepersonal={$row['cvepersonal']} AND fechahora>='{$row['fecha']} 00:00:00' AND '{$row['fecha']} 23:59:59'");
	while($row1 = mysql_fetch_assoc($res1)){
		if($checadas == 0) $tipo=1;
		elseif($checadas == 1) $tipo=4;
		elseif($checadas == 2) $tipo=3;
		elseif($checadas == 1) $tipo=2;
		mysql_query("UPDATE checada_lector SET tipo={$tipo} WHERE cve={$row1['cve']}");
		$checadas++;
	}
}