hackedWEKO
==========

[WEKO](http://weko.at.nii.ac.jp/)の改造版

改造の仕方も残していけるといいな。

##本家(ver.2.3.1)からの変更点
色々試したいけど、いまのところしょぼい改造しかできていない。
	

###JuNII2
- JuNII2マッピングによる意味欠落を要素名で補ってみる
- \_REPOSITORY\_REPON\_JUNII2\_EXDESCRIPTION = trueのとき、decriptionタグのテキスト出力を「要素名：値」となるよう変更

編集箇所：

- repository/config/define.inc.php
- repository/oaipmh/format/JuNii2.class.php

###IIIFマニフェストファイル生成機能
- IIIFマニフェストファイルのエキスポート機能を簡易的に実装(2016/07/25)．
	- 機能を有効にする場合は，「\_REPOSITORY\_REPON\_IIIF\_SUPPORT」をtrueにする．
	- WEKOのマッピング機能で「junii2」の「title」, 「description」, 「author」, 「fullTextURL」と対応する要素をマッピングする．
	- repository/iiif/config.ini でイメージサーバの設定をする．
		- IIIF\_IMG\_SRV\_BASE\_URL[] = イメージサーバのベースURL
		- IIIF\_IMG\_SRV\_PROFILE[] = イメージサーバのプロファイル
	- fullTextURLとして http://weko/iiif/test.tif/full/full/0/default.jpg を指定する場合は config.ini は以下の設定とする．
		- IIIF\_IMG\_SRV\_BASE\_URL[]= http://weko/iiif/
		- IIIF\_IMG\_SRV\_PROFILE[] = http://iiif.io/api/image/2/level1.json

編集箇所：

- repository/config/define.inc.php
- repository/templates/default/repository\_item\_detail.html
- repository/view/common/item/detail/Detail.class.php
- repository/view/main/item/detail/Detail.class.php

追加：

- repository/iiif/


###OpenSearch
- OpenSearch(Atom形式)でファイルコンテンツのURLを提供
	- link rel="enclosure" で
- countパラメータが無効になっているのを修正
- ファイル履歴との整合性確認が必要

編集箇所：

- repository/opensearch/Opensearch.class.php
- repository/opensearch/format/Atom.class.php
- repository/opensearch/format/FormatAbstract.class.php


###アイテム詳細画面

- imgタグで表示されたリンク属性をクリックするとリンク先画像を別ウィンドウで表示

編集箇所：

- repository/templates/default/repository\_item\_detail.html
- repository/templates/default/repository\_mobile\_item\_detail.html
- repository/templates/default/smartphone/repository\_item\_detail.html

##履歴

- 20161001 ベースをWEKO 2.3.1に変更．以下，改造箇所は不要となる．

###アイテム詳細画面
- リンク属性のURLの末尾がjpgだったらURLをimgタグで表示
	- sizeはcss指定（mobile, smartphoneは上記cssを読み込まないようなのでPC版のみ）
	- hyper linkも有効にする (2016/07/25)

- リンク属性の表示名に「iframe」がある場合はURLをiframeで表示
	- sizeはcss指定（mobile, smartphoneは上記cssを読み込まないようなのでPC版のみ）

編集箇所：

- repository/templates/default/repository\_item\_detail.html
- repository/templates/default/repository\_mobile\_item\_detail.html
- repository/templates/default/smartphone/repository\_item\_detail.html
- repository/files/css以下のstyle.css

###SWORDv2
- [python-client-sword2](https://github.com/swordapp/python-client-sword2)の妥当性検証を通過できるよう修正

編集箇所：

- repository/action/main/sword/SwordUpdate.class.php
- repository/action/main/sword/SwordUtility.class.php
- repository/action/main/sword/import/Import.class.php
- repository/action/main/sword/servicedocument/Servicedocument.class.php