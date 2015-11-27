#python-client-sword2を使ったSWORD2接続テスト

pythonとvirtualenvのバージョン

```
$ python --version
Python 2.7.10
$ virtualenv --version
13.1.0
```

virtualenvで環境を構築する。

```
$ virtualenv weko
New python executable in weko/bin/python2.7
Also creating executable in weko/bin/python
Installing setuptools, pip, wheel...done.
```

環境を有効にする。

```
$ source weko/bin/activate
```

python-client-sword2をインストールする。

```
$ git clone https://github.com/swordapp/python-client-sword2.git
Cloning into 'python-client-sword2'...
remote: Counting objects: 535, done.
remote: Total 535 (delta 0), reused 0 (delta 0), pack-reused 535
Receiving objects: 100% (535/535), 180.07 KiB | 183.00 KiB/s, done.
Resolving deltas: 100% (348/348), done.
Checking connectivity... done.
$ cd python-client-sword2/
$ python setup.py install
```

wekoにsword2経由で接続する。

```
$ python connect_test.py http://weko/nc2/weko/sword/servicedocument.php admin admin
2015-11-28 00:23:06,644 - sword2.connection - INFO - Loading default HTTP layer
2015-11-28 00:23:06,644 - sword2.connection - INFO - keep_history=True--> This instance will keep a JSON-compatible transaction log of all (SWORD/APP) activities in 'self.history'
2015-11-28 00:23:06,644 - sword2.connection - INFO - Adding username/password credentials for the client to use.
2015-11-28 00:23:07,366 - sword2.connection - INFO - Received a document for http://weko/nc2/weko/sword/servicedocument.php
2015-11-28 00:23:07,370 - sword2.service_document - INFO - Initial SWORD2 validation checks on service document - Valid document? True
--------------------
Type: 'init' [2015-11-28T00:23:06.644502]
Data:
user_name:   admin
on_behalf_of:   None
sd_iri:   http://weko/nc2/weko/sword/servicedocument.php
--------------------
Type: 'SD_IRI GET' [2015-11-28T00:23:07.366413]
Data:
sd_iri:   http://weko/nc2/weko/sword/servicedocument.php
response:   {
 "status": 200, 
 "resp": {
  "status": "200", 
  "content-length": "1648", 
  "content-location": "http://weko/nc2/weko/sword/servicedocument.php", 
  "x-powered-by": "PHP/5.3.3", 
  "set-cookie": "PHPSESSID=cfen89oj2s09s9jgmuo35nlg76; path=/", 
  "expires": "Thu, 19 Nov 1981 08:52:00 GMT", 
  "server": "Apache/2.2.15 (CentOS)", 
  "connection": "close", 
  "pragma": "no-cache", 
  "cache-control": "no-store, no-cache, must-revalidate, post-check=0, pre-check=0", 
  "date": "Fri, 27 Nov 2015 15:23:06 GMT", 
  "content-type": "application/atomsvc+xml;charset=\"utf-8\""
 }
}
process_duration:   0.721804141998
--------------------
Type: 'SD Parse' [2015-11-28T00:23:07.371229]
Data:
maxUploadSize:   512000
sd_iri:   http://weko/nc2/weko/sword/servicedocument.php
valid:   True
sword_version:   2.0
workspaces_found:   ['WEKO']
process_duration:   0.00460696220398
```

depositしてみる。

```
$ python deposit_test.py http://weko/nc2/weko/sword/servicedocument.php admin admin export.zip
Date: Fri, 27 Nov 2015 15:26:26 GMT
Server: Apache/2.2.15 (CentOS)
X-Powered-By: PHP/5.3.3
Set-Cookie: PHPSESSID=7rkmoo73bk6gsjlqs31ju8pgp0; path=/
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Pragma: no-cache
Location: http://weko/nc2/weko/sword/deposit.php
Content-Length: 812
Connection: close
Content-Type: application/atom+xml;type=entry;charset="utf-8"

<?xml version="1.0" encoding="UTF-8" ?>
<entry xmlns="http://www.w3.org/2005/Atom" xmlns:sword="http://purl.org/net/sword/terms/">
<title>Repository Review</title>
<version>2.0</version>
<id>5-5</id>
<updated>2013-08-20JST16:46:0432400</updated>
<author>
<name>06b49b49da52ce7b2d1732ba7508fca3e7f7a1a6</name>
<email></email>
</author>
<content type="text/html" src="http://weko/nc2/?action=repository_uri&amp;item_id=5" message=""/>
<source>
<generator uri="http://weko/nc2/weko/sword/deposit.php" version="2"/>
</source>
<sword:treatment>Deposited items(zip) will be treated as WEKO import file which contains any WEKO contents information, and will be imported to WEKO.</sword:treatment>
<sword:formatNamespace>WEKO</sword:formatNamespace>
<sword:userAgent>SWORD Client for WEKO V2.0</sword:userAgent>
</entry>
```

環境を無効にする。

```
$ deactivate
```
