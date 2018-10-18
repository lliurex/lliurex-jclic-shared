import xmlrpclib as x
import sys

# returns if user given by cmd line is teacher or admin

try:
    c=x.ServerProxy("https://127.0.0.1:9779")
    username=sys.argv[1]
    password=sys.argv[2]
    
    ret=c.validate_user(username, password)
    
    if (('teachers' in ret[1])or('adm' in ret[1])):
        print 'true'
    else:
        print 'false'
    
except Exception as e:
    print "Exception: "+str(e)

