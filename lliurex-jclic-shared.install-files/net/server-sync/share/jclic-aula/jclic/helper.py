#!/usr/bin/env python
import zipfile
import shutil
import os
import re
import unicodedata
from tempfile import mkstemp
from os import remove, close
import sys
from time import sleep

name_zip=str(sys.argv[1])
#name_zip="/home/lliurex/essent.jclic.zip"
extract_dir="/net/server-sync/share/jclic-aula/jclic_uploads"
dir_jclic_orig="/net/server-sync/share/jclic-aula/jclic_uploads/0rig_jclic"
#extract_dir="/tmp/jclic-aula"
index="index.html"
jclic_js="http://server/jclic-aula/jclic/jclic.min.js"
jclic_plugin="http://server/jclic-aula/jclic/jclicplugin.js"
jar_path_lib="http://server/jclic-aula/jars"
sufix=".lock"

#sys.exit()

def title_file(line):
	try:
		line = unicodedata.normalize('NFKD', unicode(line, 'utf8'))
		line = line.encode('ascii', 'ignore')
		line = line.decode("utf-8")
		clean=re.compile('<.*?>')
		title=re.sub(clean, '', line)
		title=title.lstrip()
		title=unicode(title.strip('\n'))
		title=re.sub('\W+',' ', title )
		print "Titulo: %s"%title
		return [True, title]
		
	except Exception as e:
			return [False,str(e)]
	
#def title_file


def remove_utf8(s):
	
	try:
		char=""
		
		if type(s) == str:
			s=s.decode("utf-8")
		
		for c in unicodedata.normalize("NFD",s):
			c = c.encode('ascii', 'ignore')
			c=c.lower()
			c=c.decode()
			c=c.replace("-", "_")
			
			#if len(c) > 0 and unicodedata.category(c) not in ['Mn','Ps','Pc',"Pe","Z","C","S"] :
			if len(c) > 0 and unicodedata.category(c) in ["Ll","Nd"] or c==".":
				char+=c
				
			else:
				if len(c)>0:
					char+="_"
		
		return char
		
	except Exception as e:
			print e
			return [False,str(e)]


def media_line (linea):
	try:
		linea = unicodedata.normalize('NFKD', unicode(linea, 'utf8'))
		linea = linea.encode('ascii', 'ignore')
		linea = linea.decode("utf-8")
		finded = re.search(r'(<media name=")([\s\S]*?)(")', linea)
		ruta=finded.group(2)
		head, tail = os.path.split(ruta)
		ruta=remove_utf8(tail)
		new_text = re.sub(r'(<media name=")([\s\S\d]*?)(")', r'\g<1>%s\3'%ruta, linea)
		new_text2 = re.sub(r'(file=")([\s\S\d]*?)(")', r'\g<1>%s\3'%ruta, new_text)
		return [True, new_text2]
		
	except Exception as e:
			print e
			return [False,str(e)]
	
#def media_line

def image_line (linea):
	try:
		linea = unicodedata.normalize('NFKD', unicode(linea, 'utf8'))
		linea = linea.encode('ascii', 'ignore')
		linea = linea.decode("utf-8")
		finded = re.search(r'(image=")([\s\S]*?)(")', linea)
		ruta=finded.group(2)
		head, tail = os.path.split(ruta)
		ruta=remove_utf8(tail)
		new_text = re.sub(r'(image=")([\s\S]*?)(")', r'\g<1>%s\3'%ruta, linea)
		return [True, new_text]
		
	except Exception as e:
			return [False,str(e)]
	
#def media_line

def sound_line (linea):
	try:
		linea = unicodedata.normalize('NFKD', unicode(linea, 'utf8'))
		linea = linea.encode('ascii', 'ignore')
		linea = linea.decode("utf-8")
		finded = re.search(r'(file=")([\s\S]*?)(")', linea)
		ruta=finded.group(2)
		head, tail = os.path.split(ruta)
		ruta=remove_utf8(tail)
		new_text = re.sub(r'(file=")([\s\S]*?)(")', r'\g<1>%s\3'%ruta, linea)
		return [True, new_text]
		
	except Exception as e:
			return [False,str(e)]
	
#def sound_line




def index_write(extract_dira,indexa,title_in,jclic_plugin_path,jar_path,name_jclic_used):
	try:
		index_path=os.path.join(extract_dira, indexa)
		print index_path
		print "dentro con %s %s %s %s"%(title_in,jclic_plugin_path,jar_path,name_jclic_used)
		
		html_str="""
		<!DOCTYPE HTML PUBLIC>
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<title>%s</title>
				<script language="JavaScript" SRC="%s" type="text/javascript"></script>
			</head>
			<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="window.focus();">
			<script language="JavaScript">
			<!--
			setJarBase('%s');
			setLanguage('null');
			writePlugin('%s', '100%%', '100%%');
			-->
			</script>
			</body>
		</html>
		"""%(title_in,jclic_plugin_path,jar_path,name_jclic_used)
		
		print "fin"
		print "abriendo index path para escribir fichero en %s"%index_path

		html_index=open(index_path, "w")
		html_index.write(html_str)
		html_index.close()
		print "Created index.html in: %s"%index_path
	
	except Exception as e:
		print e
		return [False,str(e)]
	
# def index_write





def index_create (jclic_file):
	try:
		tilte="No Exist edit index.html"
		with open(jclic_file) as f:
			for line in f:
				#obtengo el titulo del index
				if "<title>" in line or "<Title>" in line:
					title=title_file(line)[1]
		return [True,title]
		
	except Exception as e:
			return [False,str(e)]
			
#def index_create	





def jclic_review (jclic_filer):
	
	try:
		fh, jclic_tmp=mkstemp()
		with open(jclic_tmp,'w') as new_file:
			with open(jclic_filer) as f:
				for line in f:			
					#Modifico las rutas de los ficheros...
					if "<media name" in line:
						line=media_line(line)[1]
					if "image=" in line:
						line=image_line(line)[1]
					if "sound id=" in line:
						line=sound_line(line)[1]
					new_file.write(line)
		close(fh)
		remove(jclic_filer)
		shutil.move(jclic_tmp, jclic_filer)
		print "New jclic file created in: %s"%jclic_filer
		return [True]
		
	except Exception as e:
			return [False,str(e)]



def isNotEmpty(s):
    return bool(s and s.strip())
#def_isNotEmpty





# ########################################################

# ###################MAIN PROGRAM######################
try:
	print name_zip
	if not os.path.exists(dir_jclic_orig):
		os.makedirs(dir_jclic_orig)
		
	if not os.path.exists(dir_jclic_orig):
		print
		print "Your system cannot permit to www-data write in: %s"%extract_dir
		print
		sys.exit()


	#Comprueba que el fichero que se le pasa existe y no es un fantasma
	if not os.path.exists(name_zip):
		print
		print "File not find in: %s"%name_zip
		print
		sys.exit()

	#Creo un lock y bloqueo la transformacion para que solo un usuario la haga, sino saldra sin hacer nada.
	orig_name_file=os.path.basename(name_zip)
	name_zip_lock=os.path.join(dir_jclic_orig,orig_name_file+sufix)
	print name_zip_lock

	if os.path.exists(name_zip_lock):
		print
		print "File %s is blocked because other user is transforming it, please wait"%name_zip
		print
		sys.exit()
	else:
		os.mknod(name_zip_lock)

	#Inicializo unas variables

	html5=False
	orig_name_file=os.path.basename(name_zip)

	if orig_name_file.endswith(".jclic"):
		#es un jclic_solito, le genero un index sin mas y obtengo el titulo
		tilte="Untiled"
		with open(name_zip) as f:
			for line in f:
				#obtengo el titulo del index
				if "<title>" in line or "<Title>" in line:
					title=title_file(line)[1]
		
		extract_dir=os.path.dirname(name_zip)
		
	else:
		
		dir_name=os.path.splitext(os.path.basename(name_zip))[0]
		#orig_name_file


		if dir_name.endswith(".jclic"):
			dir_name=os.path.splitext(dir_name)[0]
		extract_dir=os.path.join(extract_dir, dir_name)
		

		print "-----------------------------------"
		print "Generating index.html for %s"%name_zip
		print "-----------------------------------"
		
		#Test directory to extract and directory to save older file
		if not os.path.exists (extract_dir):
			os.makedirs(extract_dir)
		
		os.system("setfacl -m group:www-data:rwx %s"%extract_dir)
		os.system("setfacl -m default:group:www-data:rwx %s"%extract_dir)

		if not os.path.exists (dir_jclic_orig):
			os.makedirs(dir_jclic_orig)
		
		#Busco el ficero jclic para saber el nombre real
		with zipfile.ZipFile(name_zip,"r") as zip_file:
			for member in zip_file.namelist():
				filename=os.path.basename(member)
				#Me aseguro que no es una linea en blanco
				if filename.endswith(".jclic"):
					#print "tengo el fichero jclic: %s"%filename
					tilte="Untiled"
					with zip_file.open(filename) as f:
						for line in f:
							#obtengo el titulo del index
							if "<title>" in line or "<Title>" in line:
								title=title_file(line)[1]

	
	
	
	if (title.lower()).strip() in ["","sin nombre", "sense nom"]:
		title=dir_name
		print "Titulo sin nombre"
		
	print "Jclic Titulo: %s"%title

	#Creacion del index.html
	jclic_filename=os.path.basename(name_zip)
	print "genero el index con extract_dir: %s -index: %s - title: %s - jclic_js: %s - jar_path_lib: %s - name_zip: %s "%(extract_dir,index,title,jclic_plugin,jar_path_lib,jclic_filename)
	index_write(extract_dir,index,title,jclic_plugin,jar_path_lib,jclic_filename)
	
	new_name=os.path.join(extract_dir, jclic_filename)
	shutil.move(name_zip,new_name)

		
	os.remove(name_zip_lock)
	#sleep(4)
	
	sys.exit()
	
except Exception as e:
		print e
		sys.exit()

				
	
