# CSRF Detector

[![Build Status](https://travis-ci.com/technote-space/csrf-detector.svg?branch=master)](https://travis-ci.com/technote-space/csrf-detector)
[![CodeFactor](https://www.codefactor.io/repository/github/technote-space/csrf-detector/badge)](https://www.codefactor.io/repository/github/technote-space/csrf-detector)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=3.9.3](https://img.shields.io/badge/WordPress-%3E%3D3.9.3-brightgreen.svg)](https://wordpress.org/)

![バナー](https://raw.githubusercontent.com/technote-space/csrf-detector/images/assets/banner-772x250.png)

CSRFを検知するプラグインです。

[最新バージョン](https://github.com/technote-space/csrf-detector/releases/latest/download/release.zip)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [要件](#%E8%A6%81%E4%BB%B6)
- [スクリーンショット](#%E3%82%B9%E3%82%AF%E3%83%AA%E3%83%BC%E3%83%B3%E3%82%B7%E3%83%A7%E3%83%83%E3%83%88)
  - [検出時](#%E6%A4%9C%E5%87%BA%E6%99%82)
  - [ログ](#%E3%83%AD%E3%82%B0)
- [導入手順](#%E5%B0%8E%E5%85%A5%E6%89%8B%E9%A0%86)
- [機能](#%E6%A9%9F%E8%83%BD)
- [設定](#%E8%A8%AD%E5%AE%9A)
  - [検知したときに処理を終了させるかどうか](#%E6%A4%9C%E7%9F%A5%E3%81%97%E3%81%9F%E3%81%A8%E3%81%8D%E3%81%AB%E5%87%A6%E7%90%86%E3%82%92%E7%B5%82%E4%BA%86%E3%81%95%E3%81%9B%E3%82%8B%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [検知したときのメール送信先](#%E6%A4%9C%E7%9F%A5%E3%81%97%E3%81%9F%E3%81%A8%E3%81%8D%E3%81%AE%E3%83%A1%E3%83%BC%E3%83%AB%E9%80%81%E4%BF%A1%E5%85%88)
  - [監視するSQLコマンド](#%E7%9B%A3%E8%A6%96%E3%81%99%E3%82%8Bsql%E3%82%B3%E3%83%9E%E3%83%B3%E3%83%89)
  - [除外するオプションのパターン](#%E9%99%A4%E5%A4%96%E3%81%99%E3%82%8B%E3%82%AA%E3%83%97%E3%82%B7%E3%83%A7%E3%83%B3%E3%81%AE%E3%83%91%E3%82%BF%E3%83%BC%E3%83%B3)
  - [GETメソッドを除外するかどうか](#get%E3%83%A1%E3%82%BD%E3%83%83%E3%83%89%E3%82%92%E9%99%A4%E5%A4%96%E3%81%99%E3%82%8B%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [管理画面以外を除外するかどうか](#%E7%AE%A1%E7%90%86%E7%94%BB%E9%9D%A2%E4%BB%A5%E5%A4%96%E3%82%92%E9%99%A4%E5%A4%96%E3%81%99%E3%82%8B%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [同じホストからの送信を除外するかどうか](#%E5%90%8C%E3%81%98%E3%83%9B%E3%82%B9%E3%83%88%E3%81%8B%E3%82%89%E3%81%AE%E9%80%81%E4%BF%A1%E3%82%92%E9%99%A4%E5%A4%96%E3%81%99%E3%82%8B%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [管理画面からの送信を除外するかどうか](#%E7%AE%A1%E7%90%86%E7%94%BB%E9%9D%A2%E3%81%8B%E3%82%89%E3%81%AE%E9%80%81%E4%BF%A1%E3%82%92%E9%99%A4%E5%A4%96%E3%81%99%E3%82%8B%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [管理画面以外 かつ GETメソッドを除外するかどうか (詳細設定)](#%E7%AE%A1%E7%90%86%E7%94%BB%E9%9D%A2%E4%BB%A5%E5%A4%96-%E3%81%8B%E3%81%A4-get%E3%83%A1%E3%82%BD%E3%83%83%E3%83%89%E3%82%92%E9%99%A4%E5%A4%96%E3%81%99%E3%82%8B%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B-%E8%A9%B3%E7%B4%B0%E8%A8%AD%E5%AE%9A)
- [アクションの実行](#%E3%82%A2%E3%82%AF%E3%82%B7%E3%83%A7%E3%83%B3%E3%81%AE%E5%AE%9F%E8%A1%8C)
- [注意事項](#%E6%B3%A8%E6%84%8F%E4%BA%8B%E9%A0%85)
- [Author](#author)
- [プラグイン作成用フレームワーク](#%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3%E4%BD%9C%E6%88%90%E7%94%A8%E3%83%95%E3%83%AC%E3%83%BC%E3%83%A0%E3%83%AF%E3%83%BC%E3%82%AF)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## 要件
- PHP 5.6 以上
- WordPress 3.9.3 以上

## スクリーンショット
### 検出時
![detected](https://raw.githubusercontent.com/technote-space/csrf-detector/images/assets/screenshot-1.png)
### ログ
![log](https://raw.githubusercontent.com/technote-space/csrf-detector/images/assets/screenshot-2.png)

## 導入手順
1. 最新版をGitHubからダウンロード  
[release.zip](https://github.com/technote-space/csrf-detector/releases/latest/download/release.zip)
2. 「プラグインのアップロード」からインストール
![install](https://raw.githubusercontent.com/technote-space/screenshots/master/misc/install-wp-plugin.png)
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

## Author
[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)

## プラグイン作成用フレームワーク
[WP Content Framework](https://github.com/wp-content-framework/core)
