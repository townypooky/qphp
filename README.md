# PHPドキュメントのオフライン版に検索機能をつけてみる
PHPドキュメントは[http://php.net/download-docs.php](http://php.net/download-docs.php "開く")よりダウンロード可能。

検索機能をつけてパッと調べられるようにしておきたい。

## 概ねの方針
- Chromeのユーザースクリプトでどのページでも有効
- 検索は`Ctrl + F`で
- ローカルにXAMPPを入れとく
- 検索は説明文も対象
- キーを押すごとに検索をかけたい
- jQueryで簡単にHTML各要素にアクセス
- できたトコまでをGitHubへアップ

## 環境
- Ubuntu
- PHP 5.4
- MySQL 5
- Google Chrome 44
- Tampermonkey 3

## ルール
- TABではなくSPACE 4つ
- 適時コメント
- 小さいのでフレームワークなし
- OPP
- クラスのブランケットは改行後
- メソッドのブランケットは改行前
- 名前空間を Naps (-Na-goya -P-HP Java-S-cript)
- クラス名はアッパーキャメル
- メソッド名はロワーキャメルかスネークケース
- コメントは日本語

## License
名古屋 PHP・JavaScript 勉強会に属します。
(でも第2回があるかなー)
