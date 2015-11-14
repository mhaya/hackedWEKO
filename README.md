hackedWEKO
==========

[WEKO](http://weko.at.nii.ac.jp/)の改造版

改造の仕方も残していけるといいな。


##本家(ver.2.2.3)からの変更点
色々試したいけど、いまのところしょぼい改造しかできていない。

パッチは以下

patch/fromWeko2.2.3.patch

###アイテム詳細画面
- リンク属性のURLの末尾がjpgだったらURLをimgタグで表示
	- sizeはcss指定。
- リンク属性の表示名に「iframe」がある場合はURLをiframeで表示
	- sizeはcss指定。

repository/templates/default/repository\_item\_detail.html
repository/templates/default/repository\_mobile\_item\_detail.html
repository/templates/default/smartphone/repository\_item\_detail.html
repository/files/css以下のstyle.css
	
###OpenSearch
- OpenSearch(Atom形式)でファイルコンテンツのURLを提供
	- link rel="enclosure" で

repository/opensearch/Opensearch.class.php
repository/opensearch/format/Atom.class.php
repository/opensearch/format/FormatAbstract.class.php

###SWORDv2
- [python-client-sword2](https://github.com/swordapp/python-client-sword2)の妥当性検証を通過できるよう修正

repository/action/main/sword/SwordUpdate.class.php
repository/action/main/sword/SwordUtility.class.php
repository/action/main/sword/import/Import.class.php
repository/action/main/sword/servicedocument/Servicedocument.class.php


###LIDO

- 作業中

repository/oaipmh/format/Lido.class.php

