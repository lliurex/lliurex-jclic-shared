import xmlrpc.client as x
import sys
import ssl

# returns if user given by cmd line is teacher or admin

try:
    context=ssl._create_unverified_context()
    c=x.ServerProxy("https://127.0.0.1:9779",allow_none=True,context=context)
    username=sys.argv[1]
    password=sys.argv[2]
    
    ret=c.validate_user(username, password)
    
    if (('teachers' in ret[1])or('adm' in ret[1])):
        print('true')
    else:
        print('false')
    
except Exception as e:
    print("Exception: "+str(e))

