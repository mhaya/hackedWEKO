from sword2 import Connection

c = Connection("http://weko/nc2/weko/sword/servicedocument.php",user_name="admin",user_pass="admin");
c.get_service_document()
print c.history

