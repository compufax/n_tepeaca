<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');



if($_GET['cmd']==101){

	require_once("numlet.php");
	$res=mysql_query("SELECT a.* FROM venta_copias a WHERE a.cve='{$_GET['cveticket']}'");
	$row=mysql_fetch_array($res);


	$texto=chr(27)."@";
	$texto.=chr(10).chr(13);
	/*if(file_exists('img/logo.TMB')){
		$texto.=chr(27).'a'.chr(1);
		$texto.=file_get_contents('img/logo.TMB');
		$texto.=chr(10).chr(13);
		$texto.=chr(27).'a0';
	}*/
	$Usuario = mysql_fetch_assoc(mysql_query("SELECT usuario FROM usuarios WHERE cve='{$_GET['cveusuario']}'"));
	$resPlaza = mysql_query("SELECT numero,nombre,bloqueada_sat FROM plazas WHERE cve='{$row['plaza']}'");
	$rowPlaza = mysql_fetch_array($resPlaza);
	$resPlaza2 = mysql_query("SELECT rfc FROM datosempresas WHERE plaza='{$row['plaza']}'");
	$rowPlaza2 = mysql_fetch_array($resPlaza2);
	$texto.=chr(27).'!'.chr(30)." {$rowPlaza['numero']}".chr(10).chr(13)."{$rowPlaza['nombre']}";
	$texto.=chr(10).chr(13).' RFC: '.$rowPlaza2['rfc'];
	//$texto.='|AV. CONGRESO DE LA UNION 6607,|COL. GRANJAS MODERNAS|CP 07460 DELG. GUSTAVO A MADERO';
	$texto.=''.chr(10).chr(13).chr(10).chr(13);
	if($_GET['reimpresion'] == 1){
		$texto.="     REIMPRESION ".chr(10).chr(13).chr(10).chr(13);
		$row['monto'] = 0;
	}

	$texto.=chr(27).'!'.chr(8)." ORIGINAL CLIENTE";
	$texto.=''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FOLIO: ".sprintf("%05s", $row['cve']);
	$texto.=''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." VENTA DE COPIAS";
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FECHA: ".$row['fecha']."   ".substr($row['fecha_creacion'], -8).''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FEC.IMP.: ".date('Y-m-d H:i:s').''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." USUARIO: ".$Usuario['usuario'].''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." PRECIO: ".$row['precio'];
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." COPIAS: ".$row['copias'];
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." TOTAL: ".($row['monto']);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." ".numlet(($row['monto']));
	$texto.=''.chr(10).chr(13);

	$texto.=chr(10).chr(13).'SI EL IMPORTE COBRADO ES DIFERENTE AL DEL TICKET FAVOR DE REPORTARLO'.chr(10).chr(13);
	
	
	$texto.=chr(10).chr(13).chr(29).chr(86).chr(66).chr(0);


	$texto.=chr(10).chr(13);
	/*if(file_exists('img/logo.TMB')){
		$texto.=chr(27).'a'.chr(1);
		$texto.=file_get_contents('img/logo.TMB');
		$texto.=chr(10).chr(13);
		$texto.=chr(27).'a0';
	}*/
	if($row['tipo_venta']==1){
		$texto.='USTED POR ESTE TICKS NO PAGO'.chr(10).chr(13).'Y  NO SE PODRA FACTURAR'.chr(10).chr(13).chr(10).chr(13).'SI LE COBRARON FAVOR DE REPORTAR'.chr(10).chr(13).'AL GERENTE DEL CENTRO'.chr(10).chr(13);
	}
	$resPlaza = mysql_query("SELECT numero,nombre,bloqueada_sat FROM plazas WHERE cve='{$row['plaza']}'");
	$rowPlaza = mysql_fetch_array($resPlaza);
	$resPlaza2 = mysql_query("SELECT rfc FROM datosempresas WHERE plaza='{$row['plaza']}'");
	$rowPlaza2 = mysql_fetch_array($resPlaza2);
	$texto.=chr(27).'!'.chr(30)." {$rowPlaza['numero']}".chr(10).chr(13)."{$rowPlaza['nombre']}";
	$texto.=chr(10).chr(13).' RFC: '.$rowPlaza2['rfc'];
	//$texto.='|AV. CONGRESO DE LA UNION 6607,|COL. GRANJAS MODERNAS|CP 07460 DELG. GUSTAVO A MADERO';
	$texto.=''.chr(10).chr(13).chr(10).chr(13);
	if($_GET['reimpresion'] == 1){
		$texto.="     REIMPRESION ".chr(10).chr(13).chr(10).chr(13);
		$row['monto'] = 0;
	}
	$texto.=chr(27).'!'.chr(8)." COPIA ARCHIVO";
	$texto.=''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FOLIO: ".sprintf("%05s", $row['cve']);
	$texto.=''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." VENTA DE COPIAS";
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FECHA: ".$row['fecha']."   ".substr($row['fecha_creacion'], -8).''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FEC.IMP.: ".date('Y-m-d H:i:s').''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." USUARIO: ".$Usuario['usuario'].''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." PRECIO: ".$row['precio'];
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." COPIAS: ".$row['copias'];
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." TOTAL: ".($row['monto']);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." ".numlet(($row['monto']));
	$texto.=''.chr(10).chr(13);

	$texto.=chr(10).chr(13).'SI EL IMPORTE COBRADO ES DIFERENTE AL DEL TICKET FAVOR DE REPORTARLO'.chr(10).chr(13);
	

	$texto.=chr(10).chr(13).chr(29).chr(86).chr(66).chr(0);


	$texto.=chr(10).chr(13);

	
	$resPlaza = mysql_query("SELECT numero,nombre,bloqueada_sat FROM plazas WHERE cve='{$row['plaza']}'");
	$rowPlaza = mysql_fetch_array($resPlaza);
	$resPlaza2 = mysql_query("SELECT rfc FROM datosempresas WHERE plaza='{$row['plaza']}'");
	$rowPlaza2 = mysql_fetch_array($resPlaza2);
	$texto.=chr(27).'!'.chr(30)." {$rowPlaza['numero']}".chr(10).chr(13)."{$rowPlaza['nombre']}";
	$texto.=chr(10).chr(13).' RFC: '.$rowPlaza2['rfc'];
	//$texto.='|AV. CONGRESO DE LA UNION 6607,|COL. GRANJAS MODERNAS|CP 07460 DELG. GUSTAVO A MADERO';
	$texto.=''.chr(10).chr(13).chr(10).chr(13);
	if($_GET['reimpresion'] == 1){
		$texto.="     REIMPRESION ".chr(10).chr(13).chr(10).chr(13);
		$row['monto'] = 0;
	}
	$texto.=chr(27).'!'.chr(8)." COPIA SECRETARIA";
	$texto.=''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FOLIO: ".sprintf("%05s", $row['cve']);
	$texto.=''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." VENTA DE COPIAS";
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FECHA: ".$row['fecha']."   ".substr($row['fecha_creacion'], -8).''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." FEC.IMP.: ".date('Y-m-d H:i:s').''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." USUARIO: ".$Usuario['usuario'].''.chr(10).chr(13);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." PRECIO: ".$row['precio'];
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." COPIAS: ".$row['copias'];
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." TOTAL: ".($row['monto']);
	$texto.=''.chr(10).chr(13);
	$texto.=chr(27).'!'.chr(8)." ".numlet(($row['monto']));
	$texto.=''.chr(10).chr(13);

	$texto.=chr(10).chr(13).'SI EL IMPORTE COBRADO ES DIFERENTE AL DEL TICKET FAVOR DE REPORTARLO'.chr(10).chr(13);
	
	$texto.=chr(10).chr(13).chr(29).chr(86).chr(66).chr(0);

	echo $texto;
	exit();
}


if($_POST['cmd']==33){
	$resultado = array('mensaje' => 'Se cancelo exitosamente', 'tipo'=>'success');

	mysql_query("UPDATE venta_copias SET estatus='C', usucan='{$_POST['cveusuario']}', fechacan=NOW(), obscan='{$_POST['motivocancelacion']}' WHERE cve='{$_POST['ticket']}'");
	
	echo json_encode($resultado);
	exit();
}
require_once('validarloging.php');

if($_POST['cmd']==0){
	$nivelUsuario = nivelUsuario();
?>
<input type="hidden" id="ticketcancelar" value="">
<div id="modalCancelacion" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">Cancelación</h5>
		        <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>-->
			</div>
			<div class="modal-body" id="bodypago">
				<div class="row">
					<div class="col-xl-12 col-lg-12 col-md-12">
						<div class="form-row">
					        <div class="form-group col-sm-12">
								<label for="total">Motivo</label>
					            <textarea type="text" class="form-control" rows="3" id="motivocancelacion"></textarea>
					        </div>
					    </div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" onClick="cancelarventa();">Cancelar</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		     </div>
		</div>
	</div>
</div>
<div class="row justify-content-center">
	<div class="col-xl-12 col-lg-12 col-md-12">
		<div class="form-group row">
			<label class="col-sm-2 col-form-label">Fecha Inicio</label>
			<div class="col-sm-4">
            	<input type="date" class="form-control" id="busquedafechaini" name="busquedafechaini" placeholder="Fecha Inicio" value="<?php echo date('Y-m-d');?>">
        	</div>
			<label class="col-sm-2 col-form-label">Fecha Fin</label>
			<div class="col-sm-4">
            	<input type="date" class="form-control" id="busquedafechafin" name="busquedafechafin" placeholder="Fecha Fin" value="<?php echo date('Y-m-d');?>">
        	</div>
        </div>
        <div class="form-group row">
			
        	<label class="col-sm-2 col-form-label">Usuario</label>
			<div class="col-sm-4">
            	<select name="busquedausuario" id="busquedausuario" class="form-control" data-container="body" data-live-search="true" title="Usuario" data-hide-disabled="true" data-actions-box="true" data-virtual-scroll="false"><option value="">Todos</option>
            	<?php
            	$res1 = mysql_query("SELECT b.cve, b.usuario FROM (SELECT usuario FROM cobro_engomado WHERE plaza='{$_POST['cveplaza']}' GROUP BY usuario) a INNER JOIN usuarios b ON b.cve = a.usuario ORDER BY b.usuario");
				while($row1=mysql_fetch_array($res1)){
					echo '<option value="'.$row1['cve'].'">'.$row1['usuario'].'</option>';
				}
				?>
            	</select>
            	<script>
					$("#busquedausuario").selectpicker();	
				</script>
        	</div>
        </div>
        
        <div class="form-group row">
        	<div class="col-sm-12" align="center">
        		<div class="btn-group">
	        		<button type="button" class="btn btn-primary" onClick="buscar();">
		            	Buscar
		        	</button>&nbsp;&nbsp;
		        </div>
		        <div class="btn-group">
		        	<button type="button" class="btn btn-success" onClick="atcr('venta_copias.php','',1,0);">
		            	Nuevo
		        	</button>&nbsp;&nbsp;
		        </div>
	
        	</div>
        </div>
    </div>
    
</div>

<div class="table-responsive">
	<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    	<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Folio</th>
				<th>Fecha</th>
				<th>Precio</th>
				<th>Cantidad</th>
				<th>Total</th>
				<th>Usuario</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>&nbsp;</th>
				<th>Folio</th>
				<th>Fecha</th>
				<th>Precio</th>
				<th>Cantidad<br><span id="tcant" style="text-align: right;"></span></th>
				<th>Total<br><span id="ttotal" style="text-align: right;"></span></th>
				<th>Usuario</th>
			</tr>
		</tfoot>
	</table>
</div>
<script>
	var tablalistado = $('#dataTable').DataTable( {
        "ajax": {
        	url: 'venta_copias.php',
        	type: "POST",
        	"data": {
        		"cmd": 10,
        		"busquedafechaini": $("#busquedafechaini").val(),
        		"busquedafechafin": $("#busquedafechafin").val(),
        		"busquedausuario": $("#busquedausuario").val(),
        		"cvemenu": $('#cvemenu').val(),
        		"cveplaza": $('#cveplaza').val(),
        		"cveusuario": $('#cveusuario').val()
        	},
        	fncallback: function(json){
        		$('#tcant').html(json.cant);
        		$('#ttotal').html(json.total);
        	}
        },
        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "order": [[0, "DESC"]],
        "columnDefs": [
        	{ className: "dt-head-center dt-body-center", "targets": 0 },
        	{ className: "dt-head-center dt-body-right", "targets": 1 },
        	{ className: "dt-head-center dt-body-center", "targets": 2 },
        	{ className: "dt-head-center dt-body-right", "targets": 3 },
        	{ className: "dt-head-center dt-body-right", "targets": 4 },
        	{ className: "dt-head-center dt-body-right", "targets": 5 },
        	{ className: "dt-head-center dt-body-left", "targets": 6 },
        	{ orderable: false, "targets": 0 }
		  ]
    } );
	function buscar(){
		tablalistado.ajax.data({
    		"cmd": 10,
    		"busquedafechaini": $("#busquedafechaini").val(),
    		"busquedafechafin": $("#busquedafechafin").val(),
    		"busquedausuario": $("#busquedausuario").val(),
    		"cvemenu": $('#cvemenu').val(),
    		"cveplaza": $('#cveplaza').val(),
    		"cveusuario": $('#cveusuario').val()
        });
        tablalistado.ajax.reload();
	}

	function cancelarventa(){
		if ($("#motivocancelacion").val() == ""){
			alert("Necesita seleccionar un motivo de cancelacion");
		}
		else{
			$('#modalCancelacion').modal('hide');
			waitingDialog.show();
			$.ajax({
				url: 'venta_copias.php',
				type: "POST",
				dataType: 'json',
				data: {
					cmd: 33,
					ticket: $('#ticketcancelar').val(),
					motivocancelacion: $("#motivocancelacion").val(),
					cveplaza: $('#cveplaza').val(),
					cveusuario: $('#cveusuario').val()
				},
				success: function(data) {
					waitingDialog.hide();
					sweetAlert('', data.mensaje, data.tipo);
					buscar();
				}
			});
		}
	}

	function precancelarventa(ticket){
		$('#ticketcancelar').val(ticket);
		$("#motivocancelacion").val('');
		$('#modalCancelacion').modal('show');
	}


	$("#modalCancelacion").modal({
		backdrop: false,
		keyboard: false,
		show: false
	});
</script>
<?php
}

if($_POST['cmd']==10){
	$columnas=array("a.folio", "a.fecha", 'a.preio', 'a.cant', 'a.monto', 'b.usuario');

	$orderby = "";
	foreach($_POST['order'] as $dato){
		$orderby .= ",{$columnas[$dato['column']]} {$dato['dir']}";
	}

	if($orderby == ""){
		$orderby = " ORDER BY a.folio";
	}
	else{
		$orderby = " ORDER BY ".substr($orderby, 1);
	}

	$condicionmonto = " AND a.tipo_pago=1";

	$where = " WHERE a.plaza='{$_POST['cveplaza']}'";
		if($_POST['busquedafechaini']!=''){
			$where .= " AND a.fecha >= '{$_POST['busquedafechaini']}'";
		}

		if($_POST['busquedafechafin']!=''){
			$where .= " AND a.fecha <= '{$_POST['busquedafechafin']}'";
		}

		
		if($_POST['busquedausuario']!=''){
			$where .= " AND a.usuario = '{$_POST['busquedausuario']}'";
		}

		
	

	$res = mysql_query("SELECT COUNT(a.cve) as registros, SUM(IF(a.estatus!='C', a.cant, 0)) as cant, SUM(IF(a.estatus!='C', a.monto, 0)) as total FROM venta_copias a {$where}");
	$registros = mysql_fetch_assoc($res);
	$resultado = array(
		'data' => array(),
		'draw'=> $_POST['draw'],
		'recordsTotal'=> $registros['registros'],
		'recordsFiltered'=> $registros['registros'],
		'cant' => number_format($registros['cant'],2),
		'total' => number_format($registros['total'],2)
	);
	$res = mysql_query("SELECT a.cve, a.folio, a.fecha,  RIGHT(a.fecha_creacion, 8) as hora, a.precio, a.cant, a.monto, a.estatus, b.usuario FROM venta_copias a INNER JOIN usuarios b ON b.cve = a.usuario{$where}{$orderby} LIMIT {$_POST['start']},{$_POST['length']}");
	$tmonto = 0;
	$nivelUsuario = nivelUsuario();
	while($row = mysql_fetch_assoc($res)){
		
		$extras2 = '';
		if ($row['estatus'] == 'A' && $nivelUsuario >= 3 && $row['fecha']==date('Y-m-d')) {
			$extras2 .= '<a class="dropdown-item" href="#" onClick="precancelarventa('.$row['cve'].')">Cancelar</a>';
		}
		

		$dropmenu = '<button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton_'.$row['cve'].'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Acci&oacute;n
                    </button><div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton_'.$row['cve'].'" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 38px, 0px);">
                      <a class="dropdown-item" href="#" onClick="atcr(\'venta_copias.php\',\'_blank\',101,'.$row['cve'].')">Imprimir</a>
                      '.$extras2.'
                    </div>';
    if($row['estatus']=='C'){
    	$dropmenu='CANCELADO<br>'.$row['fechacan'].'<br>';
    	$Usuario=mysql_fetch_assoc(mysql_query("SELECT usuario FROM usuarios WHERE cve='{$row['usucan']}'"));
    	$dropmenu.=$Usuario['usuario'];

    }

		$resultado['data'][] = array(
			$dropmenu,
			($row['folio']),
			mostrar_fechas($row['fecha']).' '.$row['hora'],
			number_format($row['precio'],2),
			number_format($row['cant'],0),
			number_format($row['monto'],2),
			utf8_encode($row['usuario'])
		);
	}
	echo json_encode($resultado);

}

if($_POST['cmd']==1){
	$res = mysql_query("SELECT * FROM costos_copias_impresiones ORDER BY cve DESC");
	$row = mysql_fetch_assoc($res);
	$costo_copias = $row['copias'];
	

?>
<div class="row justify-content-center">
	<div class="col-sm-12" align="center">
	<?php
		if(nivelUsuario() > 1){
	?>
		<button type="button" class="btn btn-success" onClick="atcr('venta_copias.php','',2,'0');">Guardar</button>
	&nbsp;&nbsp;&nbsp;
	<?php
		}
	?>
		<button type="button" class="btn btn-primary" onClick="$('#contenedorprincipal').html('');atcr('venta_copias.php','',0,0);">Volver</button>
	</div>
</div><br>
<div class="row">
	<div class="col-xl-12 col-lg-12 col-md-12">
		<div class="card shadow">
			<div class="card-header">
				<h6 class="m-0 font-weight-bold text-secondary">Venta</h6>
			</div>
		  <div class="card-body">
		  	
	      <div class="form-row">
	      	<div class="form-group col-sm-4">
						<label for="monto">Precio</label>
	          <input type="number" class="form-control" id="precio" value="<?php echo $costo_copias;?>" name="precio" readOnly>
	        </div>
	      </div>
	      <div class="form-row">
	        <div class="form-group col-sm-4">
						<label for="copias">Cantidad</label>
	          <input type="number" class="form-control" id="cant" value="" name="cant" onKeyUp="calcular()">
	        </div>
	      </div>
	      <div class="form-row">
	        <div class="form-group col-sm-4">
						<label for="total">Total</label>
	          <input type="number" class="form-control" id="monto" value="" name="monto" readOnly>
	        </div>
	      </div>
	      
	    </div>
	  </div>
	</div>
	
</div>


<script>

function calcular(){
	var total = 0;
	total += $('#precio').val()*$('#cant').val();
	$('#monto').val(total.toFixed(2));
}


</script>

<?php
}


if($_POST['cmd']==2){
	$resultado = array('error' => 0, 'mensaje' => '');
	if($_POST['cant']<=0){
		$resultado = array('error' => 1, 'mensaje' => 'Necesita ingresar la cantidad');
	}
	elseif($_POST['monto']<=0){
		$resultado = array('error' => 1, 'mensaje' => 'El total debe de ser mayor a cero');
	}
	
	if($resultado['error']==1){
		echo json_encode($resultado);
	}
	else{	
		$row = mysql_fetch_assoc(mysql_query("SELECT IFNULL(MAX(folio)+1,1) as siguiente FROM venta_copias WHERE plaza='{$_POST['cveplaza']}'"));
		$folio = $row['siguiente'];
		while(!$res = mysql_query("INSERT venta_copias SET plaza = '{$_POST['cveplaza']}', folio = '{$folio}'")) {
			$folio++;
		}
		$cvecobro = mysql_insert_id();

		$insert = " UPDATE venta_copias 
								SET 
									fecha=CURDATE(), precio='{$_POST['precio']}', cant='{$_POST['cant']}', monto='{$_POST['monto']}', usuario='{$_POST['cveusuario']}', estatus='A' WHERE cve = '{$cvecobro}'";
		mysql_query($insert) or die(mysql_error());
		
		
		echo '<script>$("#contenedorprincipal").html("");atcr("venta_copias.php","",0,"");atcr("venta_copias.php","_blank",101,"'.$cvecobro.'");</script>';
	}
}

if($_POST['cmd']==101){
	$variables = array(
		'server' => '',
		'printer' => 'impresoratermica',
		'url' => $url_impresion.'/venta_copias.php?cmd=101&cveplaza='.$_POST['cveplaza'].'&cveticket='.$_POST['reg'].'&cveusuario='.$_POST['cveusuario'].'&reimpresion='.$_GET['reimpresion']
	);
	$impresion='<iframe src="http://localhost:8020/?'.http_build_query($variables).'" width=200 height=200></iframe>';
	echo '<html><body>'.$impresion.'</body></html>';
	echo '<script>setTimeout("window.close()",5000);</script>';
}


?>