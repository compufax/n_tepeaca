<?php
require_once('cnx_db.php');
require_once('globales.php'); 
if($_POST['cmd']==100){
	include("imp_factura.php");
	generaFacturaPdfXml($_POST['reg'],1);
	exit();
}


require_once('validarloging.php');
if($_POST['cmd']==0){
?>

<div class="row justify-content-center">
	<div class="col-xl-10 col-lg-10 col-md-10">
		<div class="form-group row">
			<label class="col-sm-2 col-form-label">Fecha Inicial</label>
			<div class="col-sm-4">
            	<input type="date" class="form-control" id="busquedafechaini" name="busquedafechaini" value="<?php echo date('Y-m');?>-01" placeholder="Fecha Inicial">
        	</div>
			<label class="col-sm-2 col-form-label">Fecha Final</label>
			<div class="col-sm-4">
            	<input type="date" class="form-control" id="busquedafechafin" name="busquedafechafin" value="<?php echo date('Y-m-d');?>" placeholder="Fecha Final">
        	</div>
        </div>
        <div class="form-group row">
        	<label class="col-sm-2 col-form-label">Folio</label>
			<div class="col-sm-4">
            	<input type="number" class="form-control" id="busquedafolio" name="busquedafolio" placeholder="Folio">
        	</div>
			<label class="col-sm-2 col-form-label">RFC Emisor</label>
			<div class="col-sm-4">
            	<input type="text" class="form-control" id="busquedarfce" name="busquedarfce" placeholder="RFC Emisor">
        	</div>
        </div>
        <div class="form-group row">
        	<label class="col-sm-2 col-form-label">Plaza</label>
			<div class="col-sm-4">
            	<select class="form-control" id="busquedaplaza" name="busquedaplaza"><option value="">Todas</option>
            	<?php
            	$res = mysql_query("SELECT cve, numero FROM plazas ORDER BY numero");
            	while($row = mysql_fetch_assoc($res)){
            		echo '<option value="'.$row['cve'].'">'.$row['numero'].'</option>';
            	}
            	?>
            	</select>
        	</div>
        	<label class="col-sm-2 col-form-label">RFC Receptor</label>
			<div class="col-sm-4">
            	<input type="text" class="form-control" id="busquedarfcr" name="busquedarfcr" placeholder="RFC Receptor">
        	</div>
        </div>
        <div class="form-group row">
        	<div class="col-sm-12" align="center">
        		<div class="btn-group">
		        	<button type="button" class="btn btn-primary" onClick="buscar();">
		            	Buscar
		        	</button>
		        </div>
		        	&nbsp;&nbsp;
		        <div class="btn-group">
		        	<button type="button" class="btn btn-primary" onClick="atcr('subir_xml.php', '', 1, 0);">
		            	Subir Archivo
		        	</button>
		        </div>
		        <?php if($_POST['cveusuario']==1){ ?>
		        <div class="btn-group">
							<button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    Eliminar
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							    <a class="dropdown-item" href="javascript: eliminar(0);">Eliminar Listado</a>
							    <a class="dropdown-item" href="javascript: eliminar(1);">Eliminar Seleccionados</a>
							</div>
						</div>
						<?php } ?>
        	</div>
        </div>
    </div>

</div>
<div class="table-responsive">
	<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    	<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Plaza</th>
				<th>Folio</th>
				<th>Fecha</th>
				<th>RFC Emisor</th>
				<th>Nombre Emisor</th>
				<th>RFC Receptor</th>
				<th>Nombre Receptor</th>
				<th>UUID</th>
				<th>Fecha Timbrado</th>
				<th>Total</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>&nbsp;</th>
				<th>Plaza</th>
				<th>Folio</th>
				<th>Fecha</th>
				<th>RFC Emisor</th>
				<th>Nombre Emisor</th>
				<th>RFC Receptor</th>
				<th>Nombre Receptor</th>
				<th>UUID</th>
				<th>Fecha Timbrado</th>
				<th>Total<br><span id="ttotal" style="text-align: right;"></span></th>
			</tr>
		</tfoot>
	</table>
</div>
<script>

	var tablalistado = $('#dataTable').DataTable( {
        "ajax": {
        	url: 'subir_xml.php',
        	type: "POST",
        	"data": {
        		"cmd": 10,
        		"busquedaplaza": $("#busquedaplaza").val(),
        		"busquedafolio": $("#busquedafolio").val(),
        		"busquedarfce": $("#busquedarfce").val(),
        		"busquedafechaini": $("#busquedafechaini").val(),
        		"busquedafechafin": $("#busquedafechafin").val(),
        		"busquedarfcr": $("#busquedarfcr").val(),
        		'cveusuario': $('#cveusuario').val(),
        		'cveplaza': $('#cveplaza').val(),
        		'cvemenu': $('#cvemenu').val()
        	},
        	fncallback: function(json){
        		$('#ttotal').html(json.total);
        	}
        },
        "processing": true,
        "serverSide": true,
        "bFilter": false,
        "order": [[2, "DESC"]],
        "bPaginate": true,
        "columnDefs": [
        	{ className: "dt-head-center dt-body-center", "targets": 0 },
        	{ className: "dt-head-center dt-body-left", "targets": 1 },
        	{ className: "dt-head-center dt-body-left", "targets": 2 },
        	{ className: "dt-head-center dt-body-center", "targets": 3 },
        	{ className: "dt-head-center dt-body-left", "targets": 4 },
        	{ className: "dt-head-center dt-body-left", "targets": 5 },
        	{ className: "dt-head-center dt-body-left", "targets": 6 },
        	{ className: "dt-head-center dt-body-left", "targets": 7 },
        	{ className: "dt-head-center dt-body-center", "targets": 8 },
        	{ className: "dt-head-center dt-body-center", "targets": 9 },
        	{ className: "dt-head-center dt-body-right", "targets": 10 },
        	{ orderable: false, "targets": 0 }
		  ]
    } );
	function buscar(){
		tablalistado.ajax.data({
    		"cmd": 10,
    		"busquedaplaza": $("#busquedaplaza").val(),
    		"busquedafolio": $("#busquedafolio").val(),
    		"busquedarfce": $("#busquedarfce").val(),
    		"busquedafechaini": $("#busquedafechaini").val(),
    		"busquedafechafin": $("#busquedafechafin").val(),
    		"busquedarfcr": $("#busquedarfcr").val(),
    		'cveusuario': $('#cveusuario').val(),
    		'cveplaza': $('#cveplaza').val(),
    		'cvemenu': $('#cvemenu').val()
        });
        tablalistado.ajax.reload();
	}

	function eliminar(tipo){
		var error = 0;
		if(tipo==1){
			if(!$('.chks').is(':checked')){
				sweetAlert('', 'Necesita seleccionar al menos una factura', 'warning');
				error=1;
			}
		}
		if(error == 0){
			atcr("subir_xml.php", "", 3, tipo);
		}
	}

	
</script>
<?php
}

if($_POST['cmd']==10){
	$columnas=array('',"b.numero", "CONCAT(a.serie, ' ', a.folio)", "CONCAT(a.fecha)", 'a.rfc_emisor', "a.nombre_emisor", "a.rfc_receptor", 'a.nombre_receptor', "a.uuid", "a.fecha_timbrado");

	$orderby = "";
	foreach($_POST['order'] as $dato){
		$orderby .= ",{$columnas[$dato['column']]} {$dato['dir']}";
	}

	if($orderby == ""){
		$orderby = " ORDER BY a.fecha DESC";
	}
	else{
		$orderby = " ORDER BY ".substr($orderby, 1);
	}

	$where = "";
	if($_POST['busquedafolio']>0){
		$where .= " AND a.folio = '{$_POST['busquedafolio']}'";
	}
	else{
		if($_POST['busquedaplaza'] != ''){
			$where .= " AND a.plaza LIKE '%{$_POST['busquedaplaza']}%'";
		}

		if($_POST['busquedarfce'] != ''){
			$where .= " AND a.rfc_emisor LIKE '%{$_POST['busquedarfce']}%'";
		}

		if($_POST['busquedafechaini'] != ''){
			$where .= " AND a.fecha_timbrado >= '{$_POST['busquedafechaini']} 00:00:00'";
		}

		if($_POST['busquedafechafin'] != ''){
			$where .= " AND a.fecha_timbrado <= '{$_POST['busquedafechafin']} 23:59:59'";
		}
		if($_POST['busquedarfcr'] != ''){
			$where .= " AND a.rfc_receptor LIKE '%{$_POST['busquedarfcr']}%'";
		}
	}

	if ($where != "") $where = " WHERE ".substr($where, 5);

	$nivelUsuario = nivelUsuario();
	$res = mysql_query("SELECT COUNT(a.cve) as registros, SUM(a.total) as total FROM sat_xml a{$where}");
	$registros = mysql_fetch_assoc($res);
	$resultado = array(
		'data' => array(),
		'draw'=> $_POST['draw'],
		'recordsTotal'=> $registros['registros'],
		'recordsFiltered'=> $registros['registros'],
		'total' => $registros['total'],
		'existencia_timbres' => existencia_timbres($_POST['cveplaza'])
	);
	$res = mysql_query("SELECT a.cve, b.numero as nomplaza, a.cve, a.serie, a.folio, a.fecha, a.rfc_emisor, a.nombre_emisor, a.rfc_receptor, a.nombre_receptor, a.uuid, a.fecha_timbrado, a.total FROM sat_xml a INNER JOIN plazas b ON b.cve = a.plaza{$where}{$orderby} LIMIT {$_POST['start']},{$_POST['length']}");
	$tmonto = 0;
	while($row = mysql_fetch_assoc($res)){
		$extras = '';
		$extras2='';
	

		$dropmenu = '<button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton_'.$row['cve'].'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Acci&oacute;n
                    </button><div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton_'.$row['cve'].'" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 38px, 0px);">
                      <a class="dropdown-item" href="#" onClick="atcr(\'subir_xml.php\',\'_blank\',100,'.$row['cve'].')">Imprimir</a>
                      '.$extras2.'
                    </div>';

		

    $dropmenu = '<input type="checkbox" class="form-control chks" name="feliminar[]" value="'.$row['cve'].'">';
		$datos_renglon = array(
			$dropmenu,
			utf8_encode($row['nomplaza']),
			$row['serie'].' '.$row['folio'],
			mostrar_fechas($row['fecha']),
			utf8_encode($row['rfc_emisor']),
			utf8_encode($row['nombre_emisor']),
			utf8_encode($row['rfc_receptor']),
			utf8_encode($row['nombre_receptor']),
			$row['uuid'],
			mostrar_fechas($row['fecha_timbrado']),
			number_format($row['total'],2),
		);

		$resultado['data'][] = $datos_renglon;
	}
	echo json_encode($resultado);

}

if($_POST['cmd']==1){

?>
<div class="row justify-content-center">
	<div class="col-sm-12" align="center">
		<button type="button" class="btn btn-success" onClick="atcr('subir_xml.php','',2,0);">Subir</button>
	&nbsp;&nbsp;&nbsp;
		<button type="button" class="btn btn-primary" onClick="atcr('subir_xml.php','',0,0);">Volver</button>
	</div>
</div><br>
<div class="row">
	<div class="col-xl-12 col-lg-12 col-md-12">
		<div class="card shadow">
			<div class="card-header">
				<h6 class="m-0 font-weight-bold text-secondary">Archivo</h6>
			</div>
			<div class="card-body">
				
			    <div class="form-row">
					<div class="form-group col-sm-6">
						<label for="cliente">Archivo</label>
			             <input name="archivo" id="archivo" class="form-control" multiple="" type="file" accept=".zip">
			        </div>
			    </div>
			    
		    </div>
		</div>
		
    </div>
</div>

<?php
}



if($_POST['cmd']==2){
	$resultado = array('error' => 0, 'mensaje' => '');
	if(is_uploaded_file ($_FILES['archivo']['tmp_name'])){
		if(substr($_FILES['archivo']['name'],-3)=='zip'){
			$zip = new ZipArchive;
			if ($zip->open($_FILES['archivo']['tmp_name']) === TRUE){
				function buscar_impuesto($arreglo, $impuesto, $campo='Importe'){
					$valor = 0;
					foreach($arreglo as $datos){
						if($datos['@Impuesto'] == $impuesto){
							$valor = $datos['@'.$campo];
						}
					}
					return $valor;
				}

				function eliminarDir($carpeta){
					foreach(glob($carpeta . "/*") as $archivos_carpeta){
						if (is_dir($archivos_carpeta)){
							eliminarDir($archivos_carpeta);
						}
						else{
							@unlink($archivos_carpeta);
						}
					}
					@rmdir($carpeta);
				}

				$nombre_carpeta = 'zips_'.date('Y_m_d_H_i_s');
				if(!mkdir('cfdi/'.$nombre_carpeta, 0777, true)){
					$resultado = array('error' => 1, 'mensaje' => "no se pudo {$nombre_carpeta}");
				}
				if ($resultado['error'] != 1){
					for($i = 0; $i < $zip->numFiles; $i++)
					{
						$filename = $zip->getNameIndex($i);
						$zip->extractTo('cfdi/'.$nombre_carpeta.'/',$filename);
					}
					
					$nombres_nuevos=array();
					for($i = 0; $i < $zip->numFiles; $i++)
					{
						$filename = $zip->getNameIndex($i);
						if(substr($filename,-3)=='xml'){
							$zip->extractTo('cfdi/'.$nombre_carpeta.'/',$filename);
							$arch = 'cfdi/'.$nombre_carpeta.'/'.$filename;
							$informacion_archivo= file_get_contents($arch);
							$dom = new DOMDocument;
							$dom->loadXML($informacion_archivo);
							$arreglo = _xmlToArray($dom);
							if(!isset($arreglo['cfdi:Comprobante'][0]['cfdi:Complemento'][0]['nomina12:Nomina'])){
								$datos = array();
								$datos['serie'] = $arreglo['cfdi:Comprobante'][0]['@Serie'];
								$plaza = mysql_fetch_assoc(mysql_query("SELECT plaza FROM foliosiniciales WHERE serie='{$datos['serie']}' AND tipodocumento IN (1, 2) AND serie != ''"));
								$datos['uuid'] = $arreglo['cfdi:Comprobante'][0]['cfdi:Complemento'][0]['tfd:TimbreFiscalDigital'][0]['@UUID'];
								if($plaza['plaza'] > 0){
									
									$res = mysql_query("SELECT * FROM sat_xml WHERE uuid='{$datos['uuid']}'");
									if(mysql_num_rows($res)==0){
										$datos['folio'] = $arreglo['cfdi:Comprobante'][0]['@Folio'];
										$datos['forma_pago'] = $arreglo['cfdi:Comprobante'][0]['@FormaPago'];
										$datos['metodo_pago'] = $arreglo['cfdi:Comprobante'][0]['@MetodoPago'];
										$datos['total'] = $arreglo['cfdi:Comprobante'][0]['@Total'];
										$datos['subtotal'] = $arreglo['cfdi:Comprobante'][0]['@SubTotal'];
										$datos['tipo_comprobante'] = $arreglo['cfdi:Comprobante'][0]['@TipoDeComprobante'];
										$datos['exportacion'] = $arreglo['cfdi:Comprobante'][0]['@Exportacion'];
										$datos['lugar_expedicion'] = $arreglo['cfdi:Comprobante'][0]['@LugarExpedicion'];
										$datos['subtotal'] = $arreglo['cfdi:Comprobante'][0]['@SubTotal'];
										$datos['total'] = $arreglo['cfdi:Comprobante'][0]['@Total'];

										$datos['iva'] = buscar_impuesto($arreglo['cfdi:Comprobante'][0]['cfdi:Impuestos'][0]['cfdi:Traslados'][0]['cfdi:Traslado'],'002');
										$datos['retencion_iva'] = buscar_impuesto($arreglo['cfdi:Comprobante'][0]['cfdi:Impuestos'][0]['cfdi:Retenciones'][0]['cfdi:Retencion'],'002');
										$datos['retencion_isr'] = buscar_impuesto($arreglo['cfdi:Comprobante'][0]['cfdi:Impuestos'][0]['cfdi:Retenciones'][0]['cfdi:Retencion'],'001');
										$datos['sello'] = $arreglo['cfdi:Comprobante'][0]['@Sello'];
										$datos['certificado'] = $arreglo['cfdi:Comprobante'][0]['@Certificado'];
										$datos['no_certificado'] = $arreglo['cfdi:Comprobante'][0]['@NoCertificado'];
										$datos['no_certificado_sat'] = $arreglo['cfdi:Comprobante'][0]['cfdi:Complemento'][0]['tfd:TimbreFiscalDigital'][0]['@NoCertificadoSAT'];
										$datos['sello_sat'] = $arreglo['cfdi:Comprobante'][0]['cfdi:Complemento'][0]['tfd:TimbreFiscalDigital'][0]['@SelloSAT'];
										$datos['sello_cfd'] = $arreglo['cfdi:Comprobante'][0]['cfdi:Complemento'][0]['tfd:TimbreFiscalDigital'][0]['@SelloCFD'];
										$datos['fecha_timbrado'] = str_replace('T',' ',$arreglo['cfdi:Comprobante'][0]['cfdi:Complemento'][0]['tfd:TimbreFiscalDigital'][0]['@FechaTimbrado']);
										$datos['rfc_prov_certif'] = $arreglo['cfdi:Comprobante'][0]['cfdi:Complemento'][0]['tfd:TimbreFiscalDigital'][0]['@RfcProvCertif'];
										$datos['version'] = $arreglo['cfdi:Comprobante'][0]['cfdi:Complemento'][0]['tfd:TimbreFiscalDigital'][0]['@Version'];
										//$datos['cadenaoriginal'] = '||'.$version.'|'.$datos['uuid'].'|'.$datos['fecha_timbrado'].'|'.$datos['sello'].'|'.$datos['no_certificado_sat'].'||';
										$datos['fecha']= str_replace("T", ' ',$arreglo['cfdi:Comprobante'][0]['@Fecha']);
										$datos['nombre_emisor']= utf8_decode($arreglo['cfdi:Comprobante'][0]['cfdi:Emisor'][0]['@Nombre']);
										$datos['rfc_emisor']= $arreglo['cfdi:Comprobante'][0]['cfdi:Emisor'][0]['@Rfc'];
										$datos['regimen_emisor']= $arreglo['cfdi:Comprobante'][0]['cfdi:Emisor'][0]['@RegimenFiscal'];
										$datos['nombre_receptor']= utf8_decode($arreglo['cfdi:Comprobante'][0]['cfdi:Receptor'][0]['@Nombre']);
										$datos['rfc_receptor']= $arreglo['cfdi:Comprobante'][0]['cfdi:Receptor'][0]['@Rfc'];
										$datos['uso_cfdi']= $arreglo['cfdi:Comprobante'][0]['cfdi:Receptor'][0]['@UsoCFDI'];
										$datos['regimen_receptor']= $arreglo['cfdi:Comprobante'][0]['cfdi:RegimenFiscalReceptor'][0]['@UsoCFDI'];
										$datos['domicilio_receptor']= $arreglo['cfdi:Comprobante'][0]['cfdi:Receptor'][0]['@DomicilioFiscalReceptor'];
										
										$conceptos = array();
										/*echo '<pre>';
										print_r($arreglo);
										echo '</pre>';*/
										foreach($arreglo['cfdi:Comprobante'][0]['cfdi:Conceptos'][0]['cfdi:Concepto'] as $indice => $valores){
											$impuestos = array();
											foreach($valores['cfdi:Impuestos'][0]['cfdi:Traslados'][0]['cfdi:Traslado'] as $traslados){
												$impuestos[] = array(
													'base' => $traslados['@Base'],
													'impuesto' => $traslados['@Impuesto'],
													'tipo_factor' => $traslados['@TipoFactor'],
													'tasa_cuota' => $traslados['@TasaOCuota'],
													'importe' => $traslados['@Importe'],
													'tipo' => 1
												);
											}
											foreach($valores['cfdi:Impuestos'][0]['cfdi:Retenciones'][0]['cfdi:Retencion'] as $traslados){
												$impuestos[] = array(
													'base' => $traslados['@Base'],
													'impuesto' => $traslados['@Impuesto'],
													'tipo_factor' => $traslados['@TipoFactor'],
													'tasa_cuota' => $traslados['@TasaOCuota'],
													'importe' => $traslados['@Importe'],
													'tipo' => 2
												);
											}
											$conceptos[]=array(
												'cantidad' => $valores['@Cantidad'],
												'descripcion' => $valores['@Descripcion'],
												'unidad' => $valores['@Unidad'],
												'valor_unitario' => $valores['@ValorUnitario'],
												'importe' => $valores['@Importe'],
												'claveprodserv' => $valores['@ClaveProdServ'],
												'claveunidad' => $valores['@ClaveUnidad'],
												'no_identificacion' => $valores['@NoIdentificacion'],
												'objeto_imp' => $valores['@NoIdentificacion'],
												'impuestos' => $impuestos,
											);
										}
				
										$campos="";
										foreach($datos as $campo=>$valor){
											$campos .= ", ".$campo."='".addslashes($valor)."'";
										}
										mysql_query("INSERT sat_xml SET plaza={$plaza['plaza']}, usuario='{$_POST['cveusuario']}'{$campos}");
										$cvefact=mysql_insert_id();
										foreach($conceptos as $concepto){
											$campos = "";
											foreach($concepto as $campo=>$valor){
												if($campo!='impuestos'){
													$campos.= ", ".$campo."='".addslashes($valor)."'";
												}
											}
											mysql_query("INSERT satmov_xml SET plaza={$plaza['plaza']},cvefact={$cvefact}{$campos}");
											$cvefactmov = mysql_insert_id();
											foreach ($concepto['impuestos'] as $impuesto) {
												$campos = "";
												foreach($impuesto as $campo=>$valor){
													$campos.= ", ".$campo."='".addslashes($valor)."'";
												}
												mysql_query("INSERT satmovimpuestos_xml SET plaza={$plaza['plaza']}, cvefact={$cvefact}, cvefactmov={$cvefactmov}{$campos}");
											} 
										}

										copy($arch,"cfdi/comprobantes/cfdix_{$plaza['plaza']}_{$cvefact}.xml");
										chmod("cfdi/comprobantes/cfdix_{$plaza['plaza']}_{$cvefact}.xml", 0777);
									}
									
								}
							}
						}
					}
				
					/*for($i = 0; $i < $zip->numFiles; $i++)
					{
						$filename = $zip->getNameIndex($i);
						if(substr($filename,-3)=='pdf'){
							$arch = 'xmls/'.$nombre_carpeta.'/'.$filename;
							if($nombres_nuevos[$arch]!=""){
								copy($arch,$nombres_nuevos[$arch].".pdf");
								chmod($nombres_nuevos[$arch].".xml", 0777);
							}
						}
					}*/
					eliminarDir('cfdi/'.$nombre_carpeta);
				}
			}
			else{
				$resultado = array('error' => 1, 'mensaje' => 'No se pudo abrir');
			}
		}
		else{
			$resultado = array('error' => 1, 'mensaje' => 'El archivo tiene que ser un zip el cual contenga los xml a subir');
		}
	}
	else{
		$resultado = array('error' => 1, 'mensaje' => 'No se pudo cargar el archivo');
	}
	

	if($resultado['error']==1){
		echo json_encode($resultado);
	}
	else{
		echo '<script>atcr("subir_xml.php","",0,0);</script>';
	}
	

	exit();
}

if($_POST['cmd']==3) {
	$where = "";
	if($_POST['reg'] == 0){
		if($_POST['busquedafolio']>0){
			$where .= " AND a.folio = '{$_POST['busquedafolio']}'";
		}
		else{
			if($_POST['busquedaplaza'] != ''){
				$where .= " AND a.plaza LIKE '%{$_POST['busquedaplaza']}%'";
			}

			if($_POST['busquedarfce'] != ''){
				$where .= " AND a.rfc_emisor LIKE '%{$_POST['busquedarfce']}%'";
			}

			if($_POST['busquedafechaini'] != ''){
				$where .= " AND a.fecha_timbrado >= '{$_POST['busquedafechaini']} 00:00:00'";
			}

			if($_POST['busquedafechafin'] != ''){
				$where .= " AND a.fecha_timbrado <= '{$_POST['busquedafechafin']} 23:59:59'";
			}
			if($_POST['busquedarfcr'] != ''){
				$where .= " AND a.rfc_receptor LIKE '%{$_POST['busquedarfcr']}%'";
			}
		}
	}
	else {
		$where = " AND a.cve IN (".implode(',', $_POST['feliminar']).")";
	}

	if ($where != "") $where = " WHERE ".substr($where, 5);
	mysql_query("DELETE b FROM sat_xml a INNER JOIN satmov_xml b ON a.cve = b.cvefact{$where}");
	mysql_query("DELETE b FROM sat_xml a INNER JOIN satmovimpuestos_xml b ON a.cve = b.cvefact{$where}");
	mysql_query("DELETE a FROM sat_xml a{$where}");
}


?>