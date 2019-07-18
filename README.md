# CSRF Detector

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=3.9.3](https://img.shields.io/badge/WordPress-%3E%3D3.9.3-brightgreen.svg)](https://wordpress.org/)

![バナー](https://raw.githubusercontent.com/technote-space/csrf-detector/images/assets/banner-772x250.png)

CSRFを検知するプラグインです。

## 要件

- PHP 5.6 以上
- WordPress 3.9.3 以上

## スクリーンショット
### 検出時
![detected](https://raw.githubusercontent.com/technote-space/csrf-detector/images/assets/screenshot-1.png)
### ログ
![log](https://raw.githubusercontent.com/technote-space/csrf-detector/images/assets/screenshot-2.png)

## 導入手順
1. [ZIPをダウンロード](https://github.com/technote-space/csrf-detector/archive/master.zip)
2. wp-content/plugins に展開
3. プラグインを有効化

## 機能
**SQLの実行** 及び **nonceのチェック** を監視します。  
**nonceのチェック** が行われる前に **create** **insert** **update** **delete** などの **SQLコマンドが実行** されたときに
- ログに保存  
- メールで通知  
- 処理を停止  

を行います。

## 設定
### 検知したときに処理を終了させるかどうか
CSRFと思われる動作を検知したときに処理を終了させてSQLが実行されないようにするかどうかを設定します。  
**\[default = true]**

### 検知したときのメール送信先
CSRFと思われる動作を検知したときに送信するメールの送信先を設定します。  
カンマ区切りで複数指定可能です。  
**\[default = '']**

### 監視するSQLコマンド
実行の監視を行うSQLコマンドをカンマ区切りで設定します。  
**\[default = 'create,alter,truncate,drop,insert,delete,update,replace']**

### 除外するオプションのパターン
監視から除外するオプションの検索パターンを正規表現で設定します。  
キャッシュ等のために使用されるオプション値の保存などを除外するために使用します。  
**\[default = '/^(\_transient\_|\_site_transient\_)/']**

### GETメソッドを除外するかどうか
通常、値の追加・更新・削除等に使用しない送信メソッド **GET** **HEAD** **TRACE** **OPTIONS** を対象から除外するかどうかを設定します。  
**\[default = false]**

### 管理画面以外を除外するかどうか
WordPressへのログイン権限が必要な管理画面のみを対象とするかどうかを設定します。  
CSRFは権限を持った人に意図しない操作をさせてその人の権限を悪用する攻撃であるため、権限が不要な操作はCSRFではありません。  
フロント側でも何か権限を使用して更新するようなことがある場合は監視したほうがよいですが通常は不要です。  
またプラグインやテーマが追加したページのみが監視対象である管理画面と異なり管理画面以外は対象が広いためCSRFではないにもかかわらず検知してしまう可能性が高いです。  
**\[default = true]**

### 同じホストからの送信を除外するかどうか
通常CSRFは攻撃者の用意した外部の罠ページから行われます。  
ただしGETメソッドで攻撃可能な状態である場合、コメント欄等からでも攻撃可能な場合があるためデフォルトでは除外しない設定になっています。  
またリファラを送信しないブラウザを使用している場合は常に異なるホストと判定されます。  
**\[default = false]**

### 管理画面からの送信を除外するかどうか
CSRFは攻撃者が攻撃用のコードを埋め込むことが可能な場所から行われます。  
したがって通常は管理画面経由で行われることはありません。  
プラグインやテーマの開発者はこの設定を `false` にすることで脆弱性の確認を手軽に行うことができます。  
**\[default = true]**

### 管理画面以外 かつ GETメソッドを除外するかどうか (詳細設定)
管理画面以外 かつ GETメソッドを対象から除外するかどうかを設定します。  
CSRFによる攻撃にほとんど使用されないパターンであるため、デフォルトで除外する設定になっています。  
また『管理画面以外を除外するかどうか』が `true` の場合、この設定は意味がありません。  
**\[default = true]**

## アクションの実行
CSRFを検知した際に **csrf_detector/csrf_detected** アクションが発行されます。  
以下のようなプログラムをテーマの **functions.php** などに追加すると検知時の動作を追加することができます。  
```
add_action( 'csrf_detector/csrf_detected', function ( $query, $backtrace, $target ) {
	var_dump( $query ); // 実行されたSQL
	var_dump( $backtrace ); // プログラムの実行履歴
	var_dump( $target ); // 検出した対象のプラグインまたはテーマ
	
	// exit;
}, 10, 3 );
```

## 注意事項
* このプラグインで検知された場合でもCSRFではない場合があります。  
  * 例：プラグイン内部での更新動作（アクセスするユーザの権限に関係なくサイトへのアクセスを起点に何か更新を行う場合）
* 全てのCSRFを検知することはできません。
  * init アクションより後が監視対象であるため、プラグインの読み込みと同時に更新するようなプラグインは検出できません
* 管理画面側の監視対象は `add_submenu_page` や `add_options_page` などを使用して追加されたページ（プラグインやテーマによって追加されたページ）です。

# Author
[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)

# プラグイン作成用フレームワーク
[WP Content Framework](https://github.com/wp-content-framework/core)
