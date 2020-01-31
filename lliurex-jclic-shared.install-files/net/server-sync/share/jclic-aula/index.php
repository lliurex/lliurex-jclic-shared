<!DOCTYPE HTML>
<html>
<head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="css/dropzone.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="css/lliurex.css" media="screen" />
        <title>Servidor LliureX</title>
	<script language="JavaScript" src="./jclic/jclicplugin.js" type="text/javascript"></script>
	<script type="text/javascript" src="./lib/jquery-3.2.0.min.js"></script>
	<script type="text/javascript" src="./lib/jquery.form.min.js"></script>
	<script type="text/javascript" src="./js/lliurex.js"></script>
	<script type="text/javascript" src="./lib/dropzone.js"></script>
	

	<script type="text/javascript">
		Dropzone.autoDiscover = false;
		//var mydata = new FormData();
		var index=0;

		function doesFileExist(urlToFile)
		{
		    console.log(urlToFile);
		    var xhr = new XMLHttpRequest();
		    xhr.open('HEAD', urlToFile, false);
		    xhr.send();
		    console.log(xhr.status);
		    if (xhr.status == "404") {
			console.log("File doesn't exist");
			return false;
		    } else {
			console.log("File exists");
			return true;
		    }
		}
		
		function execute(jclicpath, alert_lang, ident)
		{
			//console.log(jclicpath);
			var datos = {"argumento":jclicpath};
			document.body.style.cursor = "progress";
			var path_server="http://jclic-aula/";
			var banner = document.getElementById(ident);
			var path1 ="document.location=";
			var path_base=jclicpath.match(/(.*)[\/\\]/)[1]||'';
			//console.log(path_base);
			var filename=jclicpath.replace(/^.*(\\|\/|\:)/, '');
			path_lock="jclic_uploads/0rig_jclic/";
			path_base=path_server.concat(path_base);
			//console.log(path_base);
			path_lock=path_server.concat(path_lock);
			path_lock=path_lock.concat(filename);
			path_lock=path_lock.concat(".lock");
			//console.log(path_lock);
			var result=doesFileExist(path_lock);
			//console.log(result);
			if (result == true){
				alert(alert_lang);
				document.body.style.cursor = "";
				window.location.reload();
				return
			}
			console.log(datos);
			$.ajax({beforeSend: function(){
				document.getElementById(ident).classList.toggle("click_bannerw");
				},"url":"http://server/jclic-aula/jclic_html5.php",type: "POST","data":datos, complete: function() {
				document.body.style.cursor = "";
				banner.className += " click_banner5";
				//banner.className = "click_banner5";
				console.log(path_base);
				console.log(filename);
				/*if (path_base == "/net/server-sync/share/jclic-aula" || path_base == "/var/www/jclic-aula"){
					path_base="jclic_uploads/";
					path_base=path_base.concat(filename);
					jclicpath=path_base;
				}*/
				path_base="jclic_uploads/";
				path_base=path_base.concat(filename);
				jclicpath=path_base;
				jclicpath=jclicpath.split(".jclic.zip").slice(0, -1).join(".")
				path=path1.concat('"');
				path=path.concat(jclicpath);
				path=path.concat('"');
				banner.setAttribute('onClick',path)
			}});
			

			//alert(alert_lang)
			//window.location.reload()
			
		}
		
		
	$(document).ready(function(){
			
		
		// Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument		
		/*var previewNode = $("#template");
		previewNode.id = "";
		var previewTemplate = previewNode.parent().innerHTML;
		previewNode.remove();
		//previewNode.parent().remove(previewNode);*/
			
		var previewTemplate=$("#template").prop('outerHTML');
		$("#template").remove();
		
		$("div#files_upload").dropzone({
			paramName: "file",
			previewTemplate: previewTemplate,
			url: "upload.php",
			acceptedFiles: ".zip, .jclic",
			addRemoveLinks: true,
			success: function(file, response) {
				$(".dz-preview.dz-processing").hide(500, function(){
					//$(".dz-message").css("display", "block");
					$(".dz-message").css("opacity", "50");
					var rscname=($(response).attr("id"));
					var rsc_div=$("#"+rscname);
					// Only add if is not in GUI
					if (rsc_div.length==0){
						$("#custom_links").append(response);	
						$('#rscpersonal').fadeOut();
					}
					//var rsc_div=$("div#"+rscname);
					//var rsc_div=$("#"+rsc_div);
				});
			}
		});
         
            
        });

    
    
    
			
		
	</script>
        <style type="text/css">
        </style>
</head>


<body>

<!-- TEMPLATE FOR LOGIN PANEL -->
<div id="wrapper">
		<?php
			function validate($user, $pass){
				try{

				//$ret=system("python ./validate_user.py $user $pass");
					
					$ret=exec("python3 ./validate_user.py $user $pass");
					
					//echo "RET IS:$ret\n";
					if ($ret=="true") {
						//echo "starting session";
						session_start();  
						$_SESSION['user'] = $user;
						$_SESSION['role'] ="admin";
						return true;
					}
					else return false;
					
				}
				catch(Exception $e) {
					 echo ('Caught exception: '.$e->getMessage()."\n");
				}
			}; // End function validate
			
			
			//Clear session
			session_start();
			$_SESSION = array();
			session_destroy();		
			
			try{
				$user = $_POST["user"];
				$pass = $_POST["pass"];
			} catch(Exception $e){
				$user=false;
			}
			
			if($user){
				if (validate($user, $pass)) include ("logout.php");
				else include ("login.php");
			} else include ("login.php");
		

		?>
<!-- END TEMPLATE FOR LOGIN PANEL -->		



<!-- TEMPLATE FOR HEADER LLIUREX BANNER PANEL -->
	<div id="header">
		<div id="lpanel">
			<ul>
				<div class="hdrbutton"><a href="http://lliurex.net"><img id="llxbt" class="btimg" src="imagen/lliurex.jpg"></a></div>
				<div class="hdrbutton"><a href="http://mestreacasa.gva.es"><img id="mestrebt" class="btimg" src="imagen/mestreacasa.jpg"></a></div>
				<div class="hdrbutton"><a href="http://sai.edu.gva.es"><img id="saibt" class="btimg" src="imagen/sai.gif"></a></div>					
			</ul>
		</div><!-- lpanel -->

	</div><!-- header -->
	
<!-- END TEMPLATE FOR HEADER LLIUREX BANNER PANEL -->


<!-- TEMPLATE FOR BODY JCLIC APPS PANEL -->
	<div id="notice">			
		<?php
			include_once('get_locale.php');
			include_once 'preg_find.php';
			
			function getNameFromZip($dir, $resource){
				$rsc_name="";
				//echo "getname**$resource- -     \n";
				try{
					$zip = zip_open("$dir/$resource");                
					if ($zip){
						while ($zip_entry = zip_read($zip)){
							if(substr(zip_entry_name($zip_entry),-6)==".jclic"){
							//echo "dentrooooo";
					
					
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
					echo 'Caught exception: '.$e->getMessage()."\n";
					return $dir;
				}

			}
			// end function getNameFromZip
			
			
			
			
			function replaceAccents($str) {

				$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");

				$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");

				return str_replace($search, $replace, $str);
			}
			// end function getNameFromZip
			
			
			
			
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
			
			
			
			
			
			function resource_not_in_json($file_json, $library, $file_name){
				$finded=false;
				$temp_array = $file_json;
				if ( isset($temp_array[$library][$file_name])){
					$identificador=$temp_array[$library][$file_name]['id'];
					$titulo=$temp_array[$library][$file_name]['title'];
					$finded=true;
					return array($file_json, $finded, $identificador, $titulo);
				}else{
					$identificador=false;
					$titulo=false;
				}
			return array($file_json, $finded, $identificador, $titulo);
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
			
			
			
			
			
			function CreateButton($dir, $resource, $class, $locale, $activities_orig, $library, $temp_array){
				//echo "$resource\n";
				//print_r ($activities_orig);
				//$file_json="/net/server-sync/share/jclic-aula/jclic_uploads/0rig_jclic/jclic_files.json";
				$index_base="http://server/jclic-aula/jclic/index_base.html";
				$resource_finded=array(false,false);
				//var_dump($temp_array);
				if ($class=="library"){
					//echo "library";
					$indexfile="$dir/$resource";
					$library_file=basename($indexfile);
					//var_dump("$library_file");
					$added=false;
					$file_name=dirname($indexfile);
					$resource_finded=resource_not_in_json($temp_array, $library, $file_name);
					if ($resource_finded[1]){
						$indent=$resource_finded[2];
						$title=$resource_finded[3];
						//echo "encontrado index";
					}else{
						//No existe en el json el fichero debo anyadirlo con titulo
						$added=true;
						//obtengo nombre de la actividad
						if(is_file($indexfile)){
						      $file=fopen($indexfile,r);
						      while (!feof($file)){
							 $read = fgets($file,255);
							 if (stristr($read,"<title>")){
							    $title=strip_tags($read);
							 }
						      }
						      fclose($file);
						     // echo "falta este html";
						}else{
						      $title=""; 
						}
						//echo $title;
						$title=trim($title);
						
						if (in_array($indent,$activities_orig)){
							return($indent);
						}else{
						//print ($title);
						
						if ((strcasecmp( $title, 'sin nombre' ) == 0) || (strcasecmp( $rsc_name, 'sense nom' ) == 0)){
							$title=$resource_dir;
						}
						$indent=$resource;
						if ($added) {
							$temp_array=adding_resource_json($temp_array, $library, $file_name, $indent, $title);
						}
						}
					}
					//var_dump($title);
					//debo generarle el index si no lo posee ya
					if(!is_file("$file_name/index.html")){
						copy($index_base,"$file_name/index.html" );
						$filedata = file ("$file_name/index.html");
						$newdata = array();
						$lookfor = "library_file";
						$newtext = '<div class ="JClic" data-project="'.$library_file.'"></div>';
						foreach ($filedata as $filerow){
							if (strstr($filerow, $lookfor) !== false){
								$filerow = $newtext;
							}
							$newdata[] = $filerow;
						}
						file_put_contents("$file_name/index.html", $newdata);
					}
					print("<div class='click_banner5' id=$indent onClick='document.location=\"$file_name\"'>\n");
					print("<div class='rsc_name_new'><span>$title </span></div>");
					print("</div>");
				
				}else{
				
						
					if ($class=="html"){
						//echo "HTML";
						$resource_dir=$resource;
						$indexfile="$dir/$resource_dir/index.html";
						$file_name="$dir/$resource_dir";
						$added=false;
						//print("$indexfile");
						$resource_finded=resource_not_in_json($temp_array, $library, $file_name);
						//var_dump($resource_finded);
						if ($resource_finded[1]){
							$indent=$resource_finded[2];
							$title=$resource_finded[3];
							//echo "encontrado html";
						}else{
							$added=true;
							if(is_file($indexfile)){
							      $file=fopen($indexfile,r);
							      while (!feof($file)){
								 $read = fgets($file,255);
								 if (stristr($read,"<title>")){
								    $title=strip_tags($read);
								 }
							      }
							      fclose($file);
							     // echo "falta este html";
							}else{
							      $title=""; 
							}
							//echo $title;
							$title=trim($title);
						
							if (in_array($indent,$activities_orig)){
								return($indent);
							}else{
							//print ($title);
							
							if ((strcasecmp( $title, 'sin nombre' ) == 0) || (strcasecmp( $rsc_name, 'sense nom' ) == 0)){
								$title=$resource_dir;
							}
							$indent=$resource;
							if ($added) {
								$temp_array=adding_resource_json($temp_array, $library, $file_name, $indent, $title);
							}
							}
						}
						print("<div class='click_banner5' id=$indent onClick='document.location=\"$dir/$resource_dir\"'>\n");
						print("<div class='rsc_name_new'><span>$title </span></div>");
						print("</div>");
							
					} else{
						//Es un fichero jclic y necesita de un index para ser lanzado, le crearemos una carpeta y le haremos un index
						//echo "JCLIC";
						$file_name="$dir/$resource";
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
						while(is_dir("$dir/$folder_name")){
							$folder_name="$folder_name.$folder_name";
						}
						mkdir("$dir/$folder_name");
						
						//Sacamos el titulo del banner
						$rsc_name=getNameFromZip($dir, $resource);
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
						}
						print("<div class='click_banner5' id=$indent onClick='document.location=\"$file_name\"'>\n");
						print("<div class='rsc_name_new'><span>$title </span></div>");
						print("</div>");
					}
				}
				//var_dump($temp_array);
				return array($indent, $temp_array);
			}
			// end function LoadResourcesFromDir
			
			
			
			
			
			function LoadResourcesFromDir($jclicdirectory, $locale,$activities_orig, $library, $temp_array){

				$rscfound=0; // Resources found
				
				$jclicconten = opendir($jclicdirectory);
				//print_r ($activities_orig);
				$activities=array();
				$lista = array();
				
				//READING DIRECTORY
				while($jclicfile = readdir($jclicconten)){
					//print ("leyendo");
					if (substr("$jclicfile", 0, 1) != "."){
						$ext_path=pathinfo($jclicfile, PATHINFO_EXTENSION);
						if (strcasecmp( $ext_path, 'inst' ) !== 0){
							//No necesito la carpeta 0rig
							if(!strstr($jclicfile, '0rig_jclic')){
								$lista[] = $jclicfile;
							}
						}
					}
				}
				//print_r($lista);
				
				if(count($lista)>0){
					sort($lista);
					$title2="";
					$resource="";
					
					foreach($lista as $item){
						if((strpos($item, '.jclic')) || (strpos($item, '.scorm'))){
							//echo "FOUND JCLIC LISTS!!!!!";
							if ($item!=""){
								// Button for .jclic.zip
								list ($title2, $temp_array)=CreateButton($jclicdirectory, $item, "jclic",$locale ,$activities_orig, $library, $temp_array);
								$activities[] = trim($title2);
								// Increasing number of elements
								$rscfound++;
							}
						}else{
							$itemdir="$jclicdirectory/$item";
							//var_dump($itemdir);
							if(is_dir($itemdir) && (strstr($jclicdirectory,'jclic_upload'))){
								$index_file="$jclicdirectory/$item/index.html";
								if (!strstr($itemdir,'0rig')){
									if(is_file($index_file)){
										//echo "posee index";
										if ($item!=""){
											// Button for .jclic.zip
											list ($title2, $temp_array)=CreateButton($jclicdirectory, $item, "html",$locale, $activities_orig, $library, $temp_array);
											$activities[] = trim($title2);
											//echo "$title2";
											//echo "222222222222222";
											// Increasing number of elements
											$rscfound++;
										}
									} else {
										//lista directorio que no posee Index.html y lo creara
										//echo "Crear INDEX excepto 0rig";
										//echo "es un directorio";
										$item_library="$jclicdirectory/$item/$item.jclic";
										$item_library2="$jclicdirectory/$item/library.jclic";
										//var_dump($item_library);
										if (file_exists($item_library)){
											$item_extended="$item/$item.jclic";
											//echo "existe";
										}else{
											if (file_exists($item_library2)){
												$item_extended="$item/library.jclic";
												//echo "existe2";
											}else{
												$item_extended="$item/$item.jclic.zip";
												//echo "existe3";
											}
										}
										//echo "directorio";
										list ($title2, $temp_array)=CreateButton($jclicdirectory, $item_extended, "library",$locale, $activities_orig, $library, $temp_array);
										$activities[] = trim($title2);
										// Increasing number of elements
										$rscfound++;
									}
								}
								//break;
							}								
						}
					}

								/*if ($resource!=""){ -------> MOVED UP
								// Button for .jclic.zip
								CreateButton($jclicdirectory, $resource, "jclic");
								}*/
							
						

					
				}
				//print_r($activities);
				//var_dump($temp_array);
				return array($rscfound,$activities,$temp_array);


			}
			// end function LoadResourcesFromDir
		
			
		?>
	


		<!-- DETECTING APPS IN JCLIC FOLDER -->
		<div id="custom_links" class="links">
			<?php		
				
				//<!-- VARIABLES -->
				//json de actividades ya instaladas
				$file_json="/net/server-sync/share/jclic-aula/jclic_uploads/0rig_jclic/jclic_files.json";
				$main_directory="/net/server-sync/share/jclic-aula";
				$main_directory=$main_directory . "/";
				$files=glob($main_directory . "*");
				$numrsc=0;
				
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
				
				//Leemos el vector de actividades, que posee ya los datos del titulo y su index creado
				$activities=array();
				$library="jclic_uploads";
				list ($numrsc, $activities, $temp_array)=LoadResourcesFromDir("jclic_uploads", $locale,$activities, $library,$temp_array);
				
				
				if($numrsc==0){
					if ($locale=="valencia") echo "<div class='rscinfo' id='rscpersonal'>No hi ha recursos disponibles en esta secció</div>";
					else echo "<div class='rscinfo' id='rscpersonal'>No hay recursos disponibles en esta sección</div>";
				}

			?>
		</div> <!-- custom_links -->
		
		
		<!-- Template for up jclic applications -->
		<?php
			 if (isset($_SESSION['role'])&&($_SESSION['role']="admin")) {
			?>
				<div id="files_upload" class="dropzone upload">
					
					<div class="dz-message">
					<?php
					 if ($locale=="valencia") echo "Arrossega nous recursos fins aci";
					 else echo "Arrastra nuevos recursos hasta aquí";
					?>
					</div>
					
				</div>
				
			<?php
			 }
		?>
		<!-- END Template for up jclic applications -->
	
	
	</div> <!-- notice -->
	
<!-- TEMPLATE FOR BODY JCLIC APPS PANEL -->


	
<!-- Template for LliureX version -->
	
	<div id="foot">
		<div class="vers">Running Lliurex <?php $vers = shell_exec('lliurex-version'); echo $vers; ?></div>
	</div> <!-- foot -->
	
<!-- End for  LliureX Version	-->
	
	
	
	
	
<!-- Template for Dropzone items -->
<
	<div id="template" class="dz-preview dz-file-preview">
		<div class="dz-details">
			<div class="dz-filename"><span data-dz-name></span></div>

			<img data-dz-thumbnail />
		</div>
		<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
		<div class="dz-success-mark"><span>✔</span></div>
		<div class="dz-error-mark"><span>✘</span></div>
		<div class="dz-error-message"><span data-dz-errormessage></span></div>
	</div> <!-- template -->

<!-- End for  Dropzone template -->

</div> <!-- wrapper -->

</body>
</html>
