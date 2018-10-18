<?php

       
function getNameFromZip($dir, $resource){
	$rsc_name="";
	try{
		$zip = zip_open("$dir/$resource");                
		if ($zip)
		{
		  while ($zip_entry = zip_read($zip))
			{
			if(substr(zip_entry_name($zip_entry),-6)==".jclic"){
				
				if (zip_entry_open($zip, $zip_entry, "r")) {
					 
					 $data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					 $zip_entry_xml=new SimpleXMLElement($data);
					 $rsc_name=$zip_entry_xml->settings->title;                            
				}
				zip_entry_close($zip_entry);                            
			}
		}
		
		zip_close($zip);
		}
		return $rsc_name;
	} // try
	catch(Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		return $dir;
	}

}

function replaceAccents($str) {

	$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");

	$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");

	return str_replace($search, $replace, $str);
}
// end function replaceAccents


function adding_resource_json($file_json, $library, $file_name, $id, $title){
				
	$temp_array=$file_json;
	$path_json="/net/server-sync/share/jclic-aula/jclic_uploads/0rig_jclic/jclic_files.json";
	if ( isset($temp_array[$library])){
		$temp_array[$library]+=array($file_name=>array('id'=>$id, 'title'=> $title));
	}else {
		$temp_array+=array($library=>array($file_name=>array('id'=>$id, 'title'=> $title)));
	}
	$jclic_json=fopen($path_json,'w');
	fwrite($jclic_json, json_encode($temp_array));
	fclose($jclic_json);
	//echo "se añadieron cosas";
	return $temp_array;
}
// end function adding_resource_json

function replace_text($lookfor, $newtext, $filename){
	try{
		$filedata = file ($filename);
		$newdata = array();
		foreach ($filedata as $filerow){
			if (strstr($filerow, $lookfor) !== false){
				$filerow = $newtext;
			}
			$newdata[] = $filerow;
		}
		file_put_contents($filename, $newdata);
		return TRUE;
	} // try
	catch(Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		return FALSE;
	}
}
// end function replace_text




try{
    $target_dir = "jclic_uploads/";
    $idrsc=basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $idrsc;	
    $type = $_FILES["file"]["type"];
    $uploadOk = 1;
      
    $accepted_types = array('application/zip',
                            'application/x-zip-compressed',
                            'multipart/x-zip',
                            'application/x-compressed');
    
	
    
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    
    // Check file type
    $check = false;
    foreach($accepted_types as $mime_type) {
		if($mime_type == $type) {
			$check = true;
			break;
		} }
        
    if($check !== false) {
		
		
        if(move_uploaded_file($_FILES['file']['tmp_name'], $target_file)){
            // All right, nothing to do
            $uploadOk = 1;
			
			$index_base="http://server/jclic-aula/jclic/index_base.html";
			$resource = basename($_FILES["file"]["name"]);
			$main_directory="/net/server-sync/share/jclic-aula/jclic_uploads";
			$dir = $main_directory;
			$file_name="$main_directory/$idrsc";
			$file_extension=$file_name;
			$folder_name=$file_name;
			//Necesito generar un nombre para la carpeta
			while ($file_extension != ""){
				$path_parts = pathinfo($folder_name);
				$folder_name = $path_parts['filename'];
				$file_extension = $path_parts['extension'];
			}
			 //Ya tengo el nombre de la carpeta, la creo
			$folder_name = preg_replace("/[^a-zA-Z 0-9]+/", "", $folder_name );
			while(is_dir("$main_directory/$folder_name")){
				$folder_name="$folder_name.$folder_name";
			}
			mkdir("$main_directory/$folder_name");
			
			
			// Obtengo nombre de la Actividad
			$rsc_name=getNameFromZip($target_dir, $resource);
			$title=$rsc_name;
			$title=(string)$title;
			$title=replaceAccents($title);
			
			$path_parts = pathinfo($resource);
			$indent=$path_parts['filename'];
			$cool_extensions = Array('jclic','zip','inst');
			if (in_array(pathinfo($indent, PATHINFO_EXTENSION), $cool_extensions)){
				$indent = pathinfo($indent, PATHINFO_FILENAME);
			}
			if ( (strcasecmp( $title, 'Sin nombre' ) == 0) || (strcasecmp( $title, 'sense nom' ) == 0)){
				$title=$indent;
			}
			$title = preg_replace("/[^a-zA-Z 0-9]+/", "", $title );
			
			//Ya tenemos el titulo, ahora vamos a ponerlo en la ruta que toca antes de almacenarlo
			rename("$file_name","$dir/$folder_name/$resource");
			$file_name="$dir/$folder_name";
			
			//comprobamos que existe el directorio para el json
			if (!is_dir(dirname($file_json))){
				mkdir(dirname($file_json));
			}
			
			if (file_exists($file_json)) {
				$str = file_get_contents($file_json);
				$temp_array = json_decode($str, true);
			}else{
				$temp_array=array();
			}
			
			$library="jclic_uploads";
			
			$temp_array=adding_resource_json($temp_array, $library, $file_name, $indent, $title);
						
			//debo generarle el index si no lo posee ya
			if(!is_file("$file_name/index.html")){
				copy($index_base,"$file_name/index.html" );
				$filename = "$file_name/index.html";
				$lookfor = "library_title";
				$newtext = '<title>'.$title.'</title>';
				replace_text($lookfor, $newtext, $filename);
				$lookfor = "library_file";
				$newtext = '<div class ="JClic" data-project="'.$resource.'"></div>';
				replace_text($lookfor, $newtext, $filename);
				//<title>Biblioteca d'activitats JClic 2005</title>
				
			}
			echo("<div class='click_banner5' id=$indent onClick='document.location=\"$target_dir/$folder_name\"'>\n");
			echo("<div class='rsc_name_new'><span>$title </span></div>");
			echo("</div>");
			
			
				
				
				
				
			//$zipname=getNameFromZip($target_dir, basename($_FILES["file"]["name"]));
			//$id=str_replace(".", "_", $idrsc);
			//echo('<div class="click_banner" id="'.$id.'" onclick="carga(&quot;'.$target_file.'&quot;)"><div class="rsc_name">'.$zipname.'</div></div>');
			} else {
                $uploadOk = 0;
            }
    } else {
        echo "File is not a valid zip/jclic resource.";
        $uploadOk = 0;
    }    
    
} catch(Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
 }
?> 
