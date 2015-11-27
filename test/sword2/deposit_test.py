import sys,sword2,urllib2, StringIO, hashlib, base64

argv = sys.argv
argc = len(argv)

if (argc != 5):
    print "Usage: python %s service_document_url user_id user_password filename" % argv[0]
    print "       python %s http://weko/nc2/weko/sword/servicedocument.php admin admin export.zip" % argv[0]
    quit()

service_document_url = argv[1]
user_name = argv[2]
user_pass = argv[3]
filename = sys.argv[4]


url = 'http://weko/nc2/weko/sword/servicedocument.php'
username = 'admin'
password = 'admin'

md5 = hashlib.md5(open(filename,'rb').read()).hexdigest()
c = sword2.Connection(url,user_name=username,user_pass=password);
c.get_service_document()
workspace_1_title, workspace_1_collections = c.workspaces[0]
collection = workspace_1_collections[0]

url = collection.href
request = urllib2.Request(url)
base64string = base64.encodestring('%s:%s' % (username, password)).replace('\n', '')
request.add_header("Authorization", "Basic %s" % base64string)
request.add_header("insert_index",1)
request.add_header("Content_MD5",md5)
request.add_header("Content_Disposition","filename="+filename.replace(".zip",""))
request.add_header("X_Verbose",1)
request.add_header("X_No_Op",0)
request.add_header("X_Format_Namespace","WEKO")

sio = StringIO.StringIO(open(filename,"rb").read())
request.add_data(sio.getvalue()) 
    
result = urllib2.urlopen(request)
print result.info()
print result.read()
