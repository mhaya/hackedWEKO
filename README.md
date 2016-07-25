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
	- sizeはcss指定（mobile, smartphoneは上記cssを読み込まないようなのでPC版のみ）
	- hyper linkも有効にする (2016/07/25)

- リンク属性の表示名に「iframe」がある場合はURLをiframeで表示
	- sizeはcss指定（mobile, smartphoneは上記cssを読み込まないようなのでPC版のみ）

修正箇所：

- repository/templates/default/repository\_item\_detail.html
- repository/templates/default/repository\_mobile\_item\_detail.html
- repository/templates/default/smartphone/repository\_item\_detail.html
- repository/files/css以下のstyle.css


	
###OpenSearch
- OpenSearch(Atom形式)でファイルコンテンツのURLを提供
	- link rel="enclosure" で
- countパラメータが無効になっているのを修正

修正箇所：

- repository/opensearch/Opensearch.class.php
- repository/opensearch/format/Atom.class.php
- repository/opensearch/format/FormatAbstract.class.php


###SWORDv2
- [python-client-sword2](https://github.com/swordapp/python-client-sword2)の妥当性検証を通過できるよう修正

修正箇所：

- repository/action/main/sword/SwordUpdate.class.php
- repository/action/main/sword/SwordUtility.class.php
- repository/action/main/sword/import/Import.class.php
- repository/action/main/sword/servicedocument/Servicedocument.class.php


###LIDO

- 作業中

修正箇所：

- repository/oaipmh/format/Lido.class.php

###JuNII2
- JuNII2マッピングによる意味欠落を要素名で補ってみる
- \_REPOSITORY\_REPON\_JUNII2\_EXDESCRIPTION = trueのとき、decriptionタグのテキスト出力を「要素名：値」となるよう変更

修正箇所：

- repository/config/define.inc.php
- repository/oaipmh/format/JuNii2.class.php

###IIIFマニフェストファイル生成機能
- IIIFマニフェストファイルのエキスポート機能を実装してみる(2016/07/25)．
	- 機能を有効にする場合は，「\_REPOSITORY\_REPON\_IIIF\_SUPPORT」をtrueにする．
	- junii2マッピングのtitle, description, author, fullTextURLを使ってmanifestを生成する．
	- fullTextURLに指定可能なのはImage API Level 1で提供されている画像URL．いまのところ，URLパターンは「xxxx/full/full/0/default.jpg」のみに限定している．
	- もう少し汎用的な処理が必要．ImageProfile を参照するようにする？

修正箇所：

- repository/config/define.inc.php
- repository/iiif/
- repository/templates/default/repository\_item\_detail.html
- repository/view/common/item/detail/Detail.class.php
- repository/view/main/item/detail/Detail.class.php
