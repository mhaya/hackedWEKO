import sys, logging, logging.config

argv = sys.argv
argc = len(argv)

if (argc != 4):
    print "Usage: python %s service_document_url user_id user_password" % argv[0]
    print "       python %s http://weko/nc2/weko/sword/servicedocument.php admin admin" % argc[0]
    quit()

service_document_url = argv[1]
user_name = argv[2]
user_pass = argv[3]

SWORD2_LOGGING_CONFIG = "./sword2_logging.conf" 
logging.config.fileConfig(SWORD2_LOGGING_CONFIG)

from sword2 import Connection

c = Connection(service_document_url,user_name=user_name,user_pass=user_pass);
c.get_service_document()
print c.history

