<?php 
$form_name="upload_file";
$sql_end="";
//verification if this field exist in the table and type like 'blob'
if (isset($protectedGet["tab"]) and $protectedGet["tab"]!=''){
	$table=$protectedGet["tab"];
	$sql_end=" and (type='longblob' or type='blob') ";	
}else{
	$table='downloadwk_pack';
}

$sql="show fields from ".$table." 
	where (field='".$protectedGet["n"]."' 
		or field='fields_".$protectedGet["n"]."')";

$result = @mysql_query($sql, $_SESSION['OCS']["readServer"]);
$item = @mysql_fetch_object($result);
$field=$item->Field;
if (isset($field) and $field != ''){
	echo "<script language='javascript'>
			function verif()
			 {
				var msg='';
				if (document.getElementById(\"file_upload\").value == ''){
					 document.getElementById(\"file_upload\").style.backgroundColor = 'RED';
					 var msg='1';
				}
	
				
	
				if (msg != ''){
				alert ('".mysql_real_escape_string($l->g(920))."');
				return false;
				}else
				return true;			
			}
		</script>";
	if ($protectedPost['GO']){
		$filename = $_FILES['file_upload']['tmp_name'];
		$fd = fopen($filename, "r");
		$contents = fread($fd, filesize ($filename));
		fclose($fd);
		$binary = addslashes($contents);
		$sql_insert="insert into temp_files (TABLE_NAME,FIELDS_NAME,FILE,AUTHOR,FILE_NAME,FILE_TYPE,FILE_SIZE)
			values ('".$table."','".$field."','".$binary."','".$_SESSION['OCS']['loggeduser']."','".$_FILES['file_upload']['name']."',
					'".$_FILES['file_upload']['type']."','".$_FILES['file_upload']['size']."')";
		mysql_query($sql_insert, $_SESSION['OCS']["writeServer"]) or mysql_error($_SESSION['OCS']["readServer"]);
		$tab_options['CACHE']='RESET';
		
	}
	
	if (isset($protectedPost['SUP_PROF']) and is_numeric($protectedPost['SUP_PROF'])){
		$sql_delete='delete from temp_files where id ="'.$protectedPost['SUP_PROF'].'"';
		mysql_query($sql_delete, $_SESSION['OCS']["writeServer"]) or mysql_error($_SESSION['OCS']["readServer"]);
		$tab_options['CACHE']='RESET';	
	}
	
	//ouverture du formulaire
	echo "<form name='".$form_name."' id='".$form_name."' method='POST' action='' enctype='multipart/form-data'>";
	echo $l->g(1048).":<input id='file_upload' name='file_upload' type='file' accept=''>";
	echo "<br><br><input name='GO' id='GO' type='submit' value='".$l->g(13)."' OnClick='return verif();window.close();'>&nbsp;&nbsp;<input type=button value='annuler' Onclick='window.close();'>";
	echo "</form>";
	echo "<br>";
	
	
	
	//print_item_header($l->g(92));
			if (!isset($protectedPost['SHOW']))
			$protectedPost['SHOW'] = 'NOSHOW';
		$form_name2="affich_files";
		$table_name=$form_name2;
		echo "<form name='".$form_name2."' id='".$form_name2."' method='POST' action=''>";
		$list_fields=array('id'=>'id','Fichier'=>'file_name','Type'=>'file_type','Poids'=>'file_size','SUP'=>'id');
		$list_col_cant_del=array('Fichier'=>'Fichier','SUP'=>'SUP');
		$default_fields= $list_fields;
		$queryDetails  = "SELECT ";
		foreach ($list_fields as $key=>$value){
			if ($key != 'SUP'){
				$queryDetails .= $value.",";			
			}
		}
		$queryDetails=substr($queryDetails,0,-1);
		$queryDetails .= " FROM temp_files where fields_name = '".$field."' and author='".$_SESSION['OCS']['loggeduser']."' 
							and (id_dde is null or id_dde='".$protectedGet["dde"]."')";
		$tab_options['LIEN_LBL']['Fichier']='index.php?'.PAG_INDEX.'='.$pages_refs['ms_view_file'].'&prov=dde_wk&no_header=1&value=';
		$tab_options['LIEN_CHAMP']['Fichier']='id';
		$tab_options['LIEN_TYPE']['Fichier']='POPUP';
		$tab_options['POPUP_SIZE']['Fichier']="width=900,height=600";
		
		
		tab_req($table_name,$list_fields,$default_fields,$list_col_cant_del,$queryDetails,$form_name2,80,$tab_options);
		echo "</form>";
}else
echo "<font color=red><b>".$l->g(1049)."</b></font>";

?>