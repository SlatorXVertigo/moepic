<?php
/* 画像掲示板

                                moepic.php v2.08
                      (gazou.php + futaba.php + moepic.php)

この掲示板は萌え連さん製作の moepic に futaba の変更点を適用したものです。
このスクリプトに関するバグやご質問は配布元の掲示板にて行います。
くれぐれも、萌え連さん、ふたばさん、レッツPHP!さんにこのスクリプトに関する
質問などをしないでください。他サイト様に迷惑をかけないようお願いします。

・moepic.php v2.08 [04/10/24]  URL:萌え連<http://moepic.dip.jp/gazo/>
・futaba.php v0.8  lot.051031  URL:ふたば<http://www.2chan.net/script/>
・gazou.php                    URL:レッツPHP!<http://php.s3.to/>

配布条件はレッツPHP!及びふたば及び萌え連に準じます。
改造、再配布は自由にどうぞ。

設置法：
１，サイトがPHP対応かどうかを調べる。
２，サイトの設置したい場所にディレクトリを作りパーミッションを777に設定する。
３，moepic.php（このファイル）の設定内容を編集する。
４，配布されている２ファイルをサイトに転送する。(moepic.php, moeta.gif)
５，設置場所内に [src] [src_d] [thumb] [redirecthtm] ディレクトリを作り
　　パーミッションを777に設定する。（スクリプトによる自動作成の場合，
　　不具合が出る恐れがあるため、必ず手動で作成してください。）
６，moepic.phpをブラウザから読み込むと自動的に必要なファイルが作成されます。
※ moeta.gif は萌え連さんの画像をそのまま利用させていただきました。

gif2png<http://www.tuxedo.org/~esr/gif2png/>がある場合は、
gifでもサムネイルを作れます。付属のバイナリは linux-i386 用です。
※ Unisys の「LZW特許」が失効となったので、最新の GD では必要ありません。

--------------------------------------------------------------------------------
※このスクリプトは萌え連さんに配布許可を頂いています。

更新内容：
06/02/16
	futaba051031++ (futaba051031 fix.) の変更点を適用
	内容最適化
05/11/06
	futaba.php [05/10/31] までの変更点を適用
*/

// 以下2行は調整用･･･
ignore_user_abort(0);
//error_reporting(E_ALL);

// グローバル変数のセット
extract($_POST,   EXTR_SKIP);
extract($_GET,    EXTR_SKIP);
extract($_COOKIE, EXTR_SKIP);

$upfile_name = isset($_FILES["upfile"]["name"]) ? $_FILES["upfile"]["name"] : "";
$upfile = isset($_FILES["upfile"]["tmp_name"]) ? $_FILES["upfile"]["tmp_name"] : "";

// 掲示板設定-------------------------------------------------------------------

define("ADMIN_PASS", 'admin');		// 管理者パス

define("LOGFILE",  'img.log');		// ログファイル名
define("TREEFILE", 'tree.log');		// ログファイル名
define("IMG_DIR",  'src/');		// 画像保存ディレクトリ  moepic.php から見て
define("THUMB_DIR",'thumb/');		// サムネイル保存ディレクトリ
define("LOG_MAX",  500);			// ログ最大行数

define("TITLE", '画像掲示板');		// タイトル（<title>とTOP）
define("HOME",  '../');			// 「ホーム」へのリンク
define("MAX_KB", 500);			// 投稿容量制限 KB（phpの設定により2Mまで
define("MAX_W",  250);			// 投稿サイズ幅（これ以上はwidthを縮小
define("MAX_H",  250);			// 投稿サイズ高さ
define("PAGE_DEF", 5);			// 一ページに表示する記事

define("PHP_SELF",  'moepic.php');	// このスクリプト名
define("PHP_SELF2", 'moepic.htm');	// 入り口ファイル名
define("PHP_EXT", '.htm');		// 1ページ以降の拡張子
define("RENZOKU",   5);			// 連続投稿秒数
define("RENZOKU2",  3);			// 画像連続投稿秒数
define("MAX_RES",  30);			// 強制sageレス数
define("USE_THUMB", 1);			// サムネイルを作る  する:1 しない:0
define("PROXY_CHECK", 0);		// proxyの書込みを制限する  する:1 しない:0
define("DISP_ID",   0);			// IDを表示する  強制:2 する:1 しない:0
define("BR_CHECK", 15);			// 改行を抑制する行数 しない:0

define("IDSEED", 'idの種');		// idの種
define("RESIMG", 0);			// レスに画像を 貼る:1 貼らない:0

// カラーリング by 萌え連 ------------------------------------------------------

define("BG_COL",   '#FFFFFF');		// 背景色
define("TXT_COL",  '#0033AA');		// 文字色
define("LINK_COL", '#0000EE');		// リンク色
define("VLINK_COL",'#0000EE');		// 訪問済みリンク色
define("ALINK_COL",'#DD0000');		// 選択した時の色

define("TIT_COL",  '#5555FF');		// 掲示板タイトルカラー
define("BASE_COL", '#5555FF');		// ベースカラー

// ----- レス記事関係 ----- //
define("RE_COL",   '#992255');		// ＞が付いた時の色
define("RE_BGCOL", '#F7F7FE');		// 背景カラー
define("SUB_COL",  '#CC1105');		// タイトルカラー
define("NAME_COL", '#117743');		// 名前カラー

// 各種追加設定 by 萌え連 ------------------------------------------------------

define("MOE_LOG",  'moecount/');	// 萌えカウントログフォルダ
define("MOE_KAKU", '.log');		// カウントログ拡張子
define("MOE_DLOG", 'moeden.log');	// 殿堂ログ
define("MOE_IMG",  'src_d/');	// 殿堂画像保存フォルダ
define("MOE_DCNT", 100);		// 殿堂入りになるカウント数
define("MOE_DPG",    5);		// 殿堂ギャラリー１ページ表示数
define("MOE_DNAG",   1);		// 殿堂を一定枚数以上表示しない  y:1 n:0
define("MOE_DNAGM",  5);		// 殿堂に表示する枚数 (↑が1の場合)

define("MOE_TITLE", '殿堂ギャラリー');	// 殿堂ギャラリータイトル（<title>
define("MOE_TITLE2",'萌えカウント殿堂入り画像ギャラリー');	// 殿堂ギャラリータイトル（TOP
define("MOE_TLINK", '萌えカウント殿堂ギャラリー');	// 殿堂ギャラリーリンクメッセージ

define("DEN_MSG", ':*:･｡,☆ﾟ･:*:･｡,殿堂入り,｡･:*:･ﾟ☆,｡･:*: ');	// 殿堂入りメッセージ
define("MOE_MSG0",'ﾓｴﾀｰ');		// 萌え現在値表示文字 (0カウント)
define("MOE_MSG1",'ﾓｴﾀｰ');		// 萌え現在値表示文字 (1カウント～MOE_DCNTの20%)
define("MOE_MSG2",'ﾓｴﾀｰ');		// 萌え現在値表示文字 (MOE_DCNTの20%～40%)
define("MOE_MSG3",'ﾓｴﾀｰ');		// 萌え現在値表示文字 (MOE_DCNTの40%～60%)
define("MOE_MSG4",'ﾓｴﾀｰ');		// 萌え現在値表示文字 (MOE_DCNTの60%～80%)
define("MOE_MSG5",'ﾓｴﾀｰ');		// 萌え現在値表示文字 (MOE_DCNTの80%～100%)

define("MOE_BOT", 1);			// 萌えボタンに画像を使うか否か  y:1 n:0
define("MOE_BOTP", 'moeta.gif');		// 萌えボタンの画像
define("MOE_BOTT", '(・∀・)ﾓｴﾀ!!!');	// 萌えボタンの文字

define("MOE_IPC", 1);			// 投稿にIP規制をかけるか否か  y:1 n:0
// (ver2.07以降１画像１カウント規制）
define("MOE_IPO", 0);			// 規制は直前のIPのみにする  y:1 n:0
// (ver2.07以前の状態に戻す）

define("NO_TITLE",'無題');		// タイトル省略時のタイトル
define("NO_COM",  '(・∀・)ﾓｴﾓｴ');	// 本文省略時の本文
define("NO_NAME", 'もえたか');		// 名前省略時の名前

define("AUTOLNK", 1);			// 本文内URLのオートリンク可否  y:1 n:0

// --2.08追加
define("USE_RE_HTM", 1);			//画像ジャンプHTMLを利用するか否か  y:1 n:0
define("RE_HTM_DIR", 'redirecthtm/');	//画像ジャンプHTML保存ディレクトリ

// -----------------------------------------------------------------------------

// 正規表現によるマッチ
$badstring = array("dummy_string", "dummy_string2"); // 拒絶する文字列
$badip = array("..0.0", "addr2.dummy.com"); // 拒絶するホスト
$badfile = array("dummy", "dummy2"); // 拒絶するファイルのmd5

$addinfo = ''; // 投稿欄注意書きの追記事項

$path = realpath("./").'/'.IMG_DIR;
init();		// ←■■初期設定後は不要なので削除可■■

/* ヘッダ */
function head(&$dat) {
  $dat .= '<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<!-- meta http-equiv="Pragma" content="no-cache" -->
<style type="text/css">
<!--
body,tr,td,th { font-size:12pt; }
a:hover { color:'.ALINK_COL.'; }
span  { font-size:20pt; }
small { font-size:10pt; }
-->
</style>
<title>'.TITLE.'</title>
<script language="JavaScript">
<!--
  function l(e) {
    var P = getCookie("pwdc"), N = getCookie("namec"), i;
    with (document) {
      for (i = 0; i < forms.length; i++) {
        if (forms[i].pwd)  with (forms[i]) { pwd.value  = P; }
        if (forms[i].name) with (forms[i]) { name.value = N; }
  } } };
  onload = l;
  function getCookie(key, tmp1, tmp2, xx1, xx2, xx3) {
    tmp1 = " " + document.cookie + ";";
    xx1 = xx2 = 0;
    len = tmp1.length;
    while (xx1 < len) {
      xx2 = tmp1.indexOf(";", xx1);
      tmp2 = tmp1.substring(xx1 + 1, xx2);
      xx3 = tmp2.indexOf("=");
      if (tmp2.substring(0, xx3) == key) {
        return (unescape(tmp2.substring(xx3 + 1, xx2 - xx1 - 1)));
      }
      xx1 = xx2 + 1;
    }
    return("");
  }
//-->
</script>
</head>
<body bgcolor="'.BG_COL.'" text="'.TXT_COL.'" link="'.LINK_COL.'" vlink="'.VLINK_COL.'">
<p align=center>
<font color="'.TIT_COL.'" size=5><b><span>'.TITLE.'</span></b></font>
<br><br>
[<a href="'.PHP_SELF.'?denview=view"><font color="#cc1105"><b>'.MOE_TLINK.'</b></font></a>]
[<a href="'.HOME.'" target="_top">ホーム</a>]
[<a href="'.PHP_SELF.'?mode=admin">管理用</a>]
<hr width="90%" size=1>
';
}

/* 投稿フォーム */
function form(&$dat, $resno, $admin="") {
  global $addinfo; $msg=""; $hidden=""; $res="";
  $maxbyte = MAX_KB * 1024;

  if ($resno) {
    $msg .= "[<a href=\"".PHP_SELF2."\">掲示板に戻る</a>]\n";
    $msg .= "<table width=\"100%\"><tr><th bgcolor=\"#5555ff\">\n";
    $msg .= "<font color=\"#FFFFFF\">レス送信モード</font>\n";
    $msg .= "</th></tr></table>\n";
  }
  if ($admin) {
    $hidden = "\n<input type=\"hidden\" name=\"admin\" value=\"".ADMIN_PASS."\">";
    $res = "\n".'<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">スレNo. </font></b></td>'.
           '<td><input type="text" name="resto" size=14 maxlength=14></td></tr>'.
           "\n".'<tr><td colspan=2><small>※スレNo. にレスをしたい スレッドNo. を入力することでそのスレにレスできます。</small></td></tr>';
    $msg = "<ul>\n".
           "<li>タグがつかえます\n".
           "<li>「管理人」の名前が使えます\n".
           "<li>PROXYの規制がかかりません\n".
           "</ul>\n";
  }

  $dat .= $msg.'<center>
<form action="'.PHP_SELF.'" method="POST" enctype="multipart/form-data">
<input type="hidden" name="mode" value="regist">'.$hidden.'
<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxbyte.'">
';

  if ($resno) $dat .= "<input type=\"hidden\" name=\"resto\" value=\"".$resno."\">\n";

  $dat .= '<table cellpadding=1 cellspacing=1>'.$res.'
<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">おなまえ</font></b></td><td><input type="text" name="name" size=28></td></tr>
<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">E-mail</font></b></td><td><input type="text" name="email" size=28></td></tr>
<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">題　　名</font></b></td><td><input type="text" name="sub" size=35>
<input type="submit" value="送信する"></td></tr>
<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">コメント</font></b></td><td><textarea name="com" wrap="soft" cols=48 rows=4></textarea></td></tr>
';

  if (RESIMG || !$resno) {
    $dat .= '<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">添付File</font></b></td>
<td><input type="file" name="upfile" size=35>
';
    if (!$resno) $dat .= "[<label><input type=\"checkbox\" name=\"textonly\" value=\"on\">画像なし</label>]</td></tr>\n";
}

  $dat .= '<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">削除キー</font></b></td>
<td><input type="password" name="pwd" size=8 maxlength=8 value=""><small>(記事の削除用。英数字で8文字以内)</small></td></tr>
<tr><td colspan=2>
<small>
<li>添付可能ファイル：GIF, JPG, PNG ブラウザによっては正常に添付できないことがあります。</li>
<li>最大投稿データ量は '.MAX_KB.' KB までです。sage機能付き。</li>
<li>画像は横 '.MAX_W.'ピクセル、縦 '.MAX_H.'ピクセルを超えると縮小表示されます。</li>
<li>投票 '.MOE_DCNT.' カウントで殿堂入りをします。</li>
'.$addinfo.'</small>
</td></tr>
</table>
</form>
</center>
<hr>';
}

/* 記事部分 */
function updatelog($resno=0) {
  global $path; $p=0;

  $tree = @file(TREEFILE);
  $counttree = count($tree);
  $find = FALSE;
  if ($resno) {
    for ($i = 0; $i < $counttree; $i++) {
      $tline = explode(",", rtrim($tree[$i]));
      if ($resno == $tline[0] || array_search($resno, $tline)) { // レス先検索
        $resno = $tline[0];
        $st = $i;
        $find = TRUE;
        break;
      }
    }
    if (!$find) { error("該当記事がみつかりません　No.".$resno); }
  }

  $line = @file(LOGFILE);
  $countline = count($line);
  for ($i = 0; $i < $countline; $i++) {
    list($no,) = explode(",", $line[$i]);
    $lineindex[$no] = $i + 1; // 逆変換テーブル作成
  }

  for ($page = 0; $page < $counttree; $page += PAGE_DEF) {
    $dat = '';
    head($dat);
    form($dat, $resno);
    if (!$resno) { $st = $page; $dat .= "<p>\n"; }
    else { $dat .= "<form action=\"".PHP_SELF."\" method=\"POST\">\n"; }

  for ($i = $st; $i < $st + PAGE_DEF; $i++) {
    if (empty($tree[$i])) { continue; }
    $treeline = explode(",", rtrim($tree[$i]));

    $disptree = $treeline[0];
    $j = $lineindex[$disptree] - 1; // 該当記事を探して$jにセット
    if (empty($line[$j])) { continue; } // $jが範囲外なら次の行
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pwd,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
    // URLとメールにリンク
    if ($email) $name = "<a href=\"mailto:".$email."\">".$name."</a>";
    if (AUTOLNK) $com = auto_link($com);
    $com = eregi_replace("(^|>)(&gt;[^<]*)", "\\1<font color=\"".RE_COL."\">\\2</font>", $com);
    // 画像ファイル名
    $img = $path.$time.$ext;
    $src = IMG_DIR.$time.$ext;
    // <imgタグ作成
    $imgsrc = "";
    if ($ext && is_file($img)) {
      $size = filesize($img); // altにサイズ表示

      if (@is_file(RE_HTM_DIR.$time.".htm")) { // 表示部リダイレクトリンクする場合
        $re_htm = RE_HTM_DIR.$time.".htm";
        if ($w && $h) { // サイズがある時
          if (@is_file(THUMB_DIR.$time.'s.jpg')) {
            $imgsrc = "<small>Redirect:サムネイルを表示しています.クリックすると元のサイズを表示します.</small><br>".
                      "<a href=\"".$re_htm."\" target=\"_blank\"><img src=\"".THUMB_DIR.$time.'s.jpg'.
                      "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
          } else {
            $imgsrc = "<small>Redirect</small><a href=\"".$re_htm."\" target=\"_blank\"><img src=\"".$src.
                      "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
          }
        } else { // それ以外
          $imgsrc = "<a href=\"".$re_htm."\" target=\"_blank\"><img src=\"".$src.
                    "\" border=0 align=left hspace=20 alt=\"".$size." B\"></a>";
        }
        $dat .= "画像タイトル：<a href=\"".$re_htm."\" target=\"_blank\">".$time.$ext."</a>-(".$size." B)<br>".$imgsrc;

      } else { // 表示部リダイレクトリンクしない場合

        if ($w && $h) { // サイズがある時
          if (@is_file(THUMB_DIR.$time.'s.jpg')) {
            $imgsrc = "<small>サムネイルを表示しています.クリックすると元のサイズを表示します.</small><br>".
                      "<a href=\"".$src."\" target=\"_blank\"><img src=\"".THUMB_DIR.$time.'s.jpg'.
                      "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
          } else {
            $imgsrc = "<a href=\"".$src."\" target=\"_blank\"><img src=\"".$src.
                      "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
          }
        } else { // それ以外
          $imgsrc = "<a href=\"".$src."\" target=\"_blank\"><img src=\"".$src.
                    "\" border=0 align=left hspace=20 alt=\"".$size." B\"></a>";
        }
        $dat .= "画像タイトル：<a href=\"".$src."\" target=\"_blank\">".$time.$ext."</a>-(".$size." B)<br>".$imgsrc;
      }

// 萌えカウントシステム　by 萌え連 ---------------------------------------------

// 少々ログ管理が汚くなりますが１スレッドに対し１ログを作成する方法を
// 利用します。配列検索等の処理が全く要らないので負荷がかからない上安全です。
// 最低限の処理の負荷及びログの安全性の面から、ログ整理の美しさを無視します。

// レス返信時は表示しない
if (!$resno) {
  // 変数初期化（オートリロード対策
  $mcountlog = $denview = $moeta = NULL;
  $logmoe = MOE_LOG.$time.MOE_KAKU;

  if (!is_file($logmoe)) { // ログファイルが無いなら作成
    ignore_user_abort(1);
    $fp = fopen($logmoe, "w");
    flock($fp, LOCK_EX);
    set_file_buffer($fp, 0);
    fputs($fp, "0,0.0.0.0\n");
    fclose($fp);
    chmod($logmoe, 0666);
    ignore_user_abort(0);
  }

  $mp_data = file($logmoe);
  $countmp = count($mp_data); // 現在のカウント数を取得
//  for ($m = 0; $m < $countmp; $m++) {
//    list($mcountlog,) = explode(",", $mp_data[$m]);
//  }
  list($mcountlog,) = explode(",", $mp_data[$countmp - 1]);

  if ($mcountlog == 'DEN') { // もし殿堂入りなら
    $dat .= "\n<div align=center><b><font color=\"#ff0000\" size=\"+2\">".DEN_MSG."</font></b></div>\n";

    // 殿堂ログ読み込み
    ignore_user_abort(1);
    $fp = fopen(MOE_DLOG, "r+") or error("ERROR! load denlog");
    set_file_buffer($fp, 0);
    flock($fp, LOCK_EX);
    $buf = fread($fp, 1000000);
    $dendat = explode("\n", $buf);
    array_pop($dendat);
    $countden = count($dendat);

    // すでに殿堂入りしているかどうか調べる
    $denflag = FALSE;
    for ($m = 0; $m < $countden; $m++) { // ファイル走査
      list($mno,) = explode(",", $dendat[$m]);
      if ($mno == $no) { $denflag = TRUE; break; }
      $dendat[$m] .= "\n";
    }
    if (!$denflag) { // 殿堂ログ内に無いなら、追加
      copy($src, MOE_IMG.$time.$ext); // 画像ファイルコピー
      // 配列に殿堂入りした画像の情報を格納
      $mnew = implode(",", array($no,$now,$name,$time,$ext,$w,$h,$chk,"\n"));
      array_unshift($dendat, $mnew);
      // ログ更新
      $remnew = implode('', $dendat);
      rewind($fp);
      fputs($fp, $remnew);
      ftruncate($fp, ftell($fp));
    }
    fclose($fp);
    ignore_user_abort(0);
  } else { // 殿堂入りじゃない
    $dat .= "\n<form action=\"".PHP_SELF."\" method=\"POST\">".
            "<input type=\"hidden\" name=\"moeta\" value=\"countup\">".
            "<input type=\"hidden\" name=\"moeno\" value=\"".$no."\">".
            "<input type=\"hidden\" name=\"mcount\" value=\"".$time."\">\n".
            "<div align=center><b>";
    // ボタン表示
    if (MOE_BOT) {
      $dat .= "<font color=\"#cc1105\" size=\"+1\">投票：</font><input type=\"image\" src=\"".MOE_BOTP."\" alt=\"\">";
    } else {
      $dat .= "<font color=\"#cc1105\" size=\"+1\">投票：</font><input type=\"submit\" value=\"".MOE_BOTT."\">";
    }
    // 現在のカウント数表示
    if ($mcountlog >= MOE_DCNT * 0.8) {
      $dat .= "　現在:<font color=\"#cc1105\" size=\"+5\">".$mcountlog."</font>".MOE_MSG5."</b></div></form>\n";
    } elseif ($mcountlog >= MOE_DCNT * 0.6) {
      $dat .= "　現在:<font color=\"#cc1105\" size=\"+4\">".$mcountlog."</font>".MOE_MSG4."</b></div></form>\n";
    } elseif ($mcountlog >= MOE_DCNT * 0.4) {
      $dat .= "　現在:<font color=\"#cc1105\" size=\"+3\">".$mcountlog."</font>".MOE_MSG3."</b></div></form>\n";
    } elseif ($mcountlog >= MOE_DCNT * 0.2) {
      $dat .= "　現在:<font color=\"#cc1105\" size=\"+2\">".$mcountlog."</font>".MOE_MSG2."</b></div></form>\n";
    } elseif ($mcountlog >= 1) {
      $dat .= "　現在:<font color=\"#cc1105\" size=\"+1\">".$mcountlog."</font>".MOE_MSG1."</b></div></form>\n";
    } else {
      $dat .= "　現在:<font color=\"#117783\" size=\"+1\">0</font>".MOE_MSG0."</b></div></form>\n";
    }
  }
}
// 萌えカウントシステム　by 萌え連 ---------------------------------------------

    } // if ($ext && is_file($img)) の終わり

    // メイン作成
    if ($resno) {
      $dat .= "\n<p><input type=\"checkbox\" name=\"".$no."\" value=\"delete\">";
    }

    $dat .= "<font color=\"".SUB_COL."\" size=\"+1\"><b>".$sub."</b></font> \n";
    $dat .= "Name <font color=\"".NAME_COL."\"><b>".$name."</b></font> ".$now." No.".$no." &nbsp; \n";
    if (!$resno) $dat .= "[<a href=\"".PHP_SELF."?res=".$no."\">返信 or 削除</a>]\n";
    $dat .= "<blockquote>".$com."</blockquote>\n";

    // そろそろ消える。
    if ($lineindex[$no] - 1 >= LOG_MAX * 0.95) {
      $dat .= "<font color=\"#f00000\"><b>このスレは古いので、もうすぐ消えます。</b></font><br>\n";
    }

    // レス作成
    $counttreeline = count($treeline);
    if (!$resno) {
      $s = $counttreeline - 10;
      if ($s < 1) { $s = 1; }
      elseif ($s > 1) {
        $dat .= "<font color=\"#707070\">レス".
                ($s - 1)."件省略。全て読むには返信ボタンを押してください。</font><br>\n";
      }
    } else {
      $s = 1;
    }
    for ($k = $s; $k < $counttreeline; $k++) {
      $disptree = $treeline[$k];
      $j = $lineindex[$disptree] - 1;
      if (empty($line[$j])) { continue; }
      list($no,$now,$name,$email,$sub,$com,$url,
           $host,$pwd,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
      // URLとメールにリンク
      if ($email) $name = "<a href=\"mailto:".$email."\">".$name."</a>";
      if (AUTOLNK) $com = auto_link($com);
      $com = eregi_replace("(^|>)(&gt;[^<]*)", "\\1<font color=\"".RE_COL."\">\\2</font>", $com);

    // 画像ファイル名
    $img = $path.$time.$ext;
    $src = IMG_DIR.$time.$ext;
    // <imgタグ作成
    $imgsrc = "";
    if ($ext && is_file($img)) {
      $size = filesize($img); // altにサイズ表示
      if ($w && $h) { // サイズがある時
        if (@is_file(THUMB_DIR.$time.'s.jpg')) {
          $imgsrc = "<small>サムネイル表示</small><br>".
                    "<a href=\"".$src."\" target=\"_blank\"><img src=\"".THUMB_DIR.$time.'s.jpg'.
                    "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
        } else {
          $imgsrc = "<a href=\"".$src."\" target=\"_blank\"><img src=\"".$src.
                    "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
        }
      } else { // それ以外
        $imgsrc = "<a href=\"".$src."\" target=\"_blank\"><img src=\"".$src.
                  "\" border=0 align=left hspace=20 alt=\"".$size." B\"></a>";
      }
      $imgsrc = "<br> &nbsp; &nbsp; <a href=\"".$src."\" target=\"_blank\">".$time.$ext."</a>-(".$size." B) ".$imgsrc;
    }

      // メイン作成
      $dat .= "<table border=0><tr><td align=right valign=top nowrap>…</td><td bgcolor=\"".RE_BGCOL."\">\n";

      if ($resno) { // レス時チェックボックス表示
        $dat .= "<input type=\"checkbox\" name=\"".$no."\" value=\"delete\">";
      }

      $dat .= "<font color=\"".SUB_COL."\" size=\"+1\"><b>".$sub."</b></font> \n";
      $dat .= "Name <font color=\"".NAME_COL."\"><b>".$name."</b></font> ".$now." No.".$no." &nbsp; \n";
      $dat .= $imgsrc."<blockquote>".$com."</blockquote>";
      $dat .= "</td></tr></table>\n";
    }
    $dat .= "<br clear=left><hr>\n";
    clearstatcache(); // ファイルのstatをクリア
    $p++;
    if ($resno) { break; } // res時はtree1行だけ
  }

  if ($resno) { // レス時に表示
    $dat .= '<table align=right><tr><td align=center nowrap><input type="hidden" name="mode" value="usrdel">
【記事削除】[<input type="checkbox" name="onlyimgdel" value="on">画像だけ消す]<br>
削除キー<input type="password" name="pwd" size=8 maxlength=8 value="">
<input type="submit" value="削除"></form></td></tr></table>
';
  }

    // 各ページへのリンク用テーブル
    if (!$resno) { // res時は表示しない
      $prev = $st - PAGE_DEF;
      $next = $st + PAGE_DEF;
      // 改ページ処理
      $dat .= '<table align=center border=1><tr>';
      if ($prev >= 0) {
        if ($prev == 0) {
          $dat .= '<form action="'.PHP_SELF2.'" method="GET">';
        } else {
          $dat .= '<form action="'.$prev / PAGE_DEF.PHP_EXT.'" method="GET">';
        }
        $dat .= '<td><input type="submit" value="前のページ"></td></form>';
      } else {
        $dat .= '<td>最初のページ</td>';
      }
      $dat .= '<td>';
      for ($i = 0; $i < $counttree; $i += PAGE_DEF) {
        if ($st == $i) { $dat .= '[<b>'.($i / PAGE_DEF).'</b>] '; }
        else {
          if ($i == 0) { $dat .= '[<a href="'.PHP_SELF2.'">0</a>] '; }
          else { $dat .= '[<a href="'.($i / PAGE_DEF).PHP_EXT.'">'.($i / PAGE_DEF).'</a>] '; }
        }
      }
      $dat .= '</td>';
      if ($p >= PAGE_DEF && $counttree > $next) {
        $dat .= '<form action="'.$next / PAGE_DEF.PHP_EXT.'" method="GET"><td>';
        $dat .= '<input type="submit" value="次のページ"></td></form>';
      } else {
        $dat .= '<td>最後のページ</td>';
      }
      $dat .= "</tr></table><br clear=all>\n";
    }

    foot($dat);
    if ($resno) { echo $dat; break; } // レス時は一行のみ

    if ($page == 0) { $logfilename = PHP_SELF2; }
    else { $logfilename = ($page / PAGE_DEF).PHP_EXT; }

    ignore_user_abort(1);
    $fp = fopen($logfilename, "w");
    flock($fp, LOCK_EX);
    set_file_buffer($fp, 0);
    fputs($fp, $dat);
    fclose($fp);
    chmod($logfilename, 0666);
    ignore_user_abort(0);
  }
  if (!$resno && is_file(($page / PAGE_DEF + 1).PHP_EXT)) { unlink(($page / PAGE_DEF + 1).PHP_EXT); } // 前ページ削除
}

/* フッタ */
function foot(&$dat) {
  $dat .= '
<div align=center>
<small><!-- GazouBBS v3.0 --><!-- ふたば改0.8 --><!-- 萌え連2.08 -->
- <a href="http://php.s3.to" target="_top">GazouBBS</a> + <a href="http://www.2chan.net/" target="_top">futaba</a> + <a href="http://moepic.dip.jp/gazo/" target="_top">moeren</a> -
</small>
</div>
</body></html>';
}

/* オートリンク */
function auto_link($proto) {
  $proto = ereg_replace("(https?|ftp|news)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", "<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>", $proto);
  return $proto;
}

/* エラー画面 */
function error($mes, $dest='', $flag=FALSE) {
  global $upfile_name, $path;
  if (is_file($dest)) unlink($dest);
  if (!$flag) head($dat);
  else $dat = "</form>\n";
  echo $dat;
  echo "<br><br><hr size=1><br><br>\n",
       "<div align=center><font color=\"red\" size=5><b>",$mes,"<br><br><a href=\"",PHP_SELF2,"\">リロード</a></b></font></div>\n",
       "<br><br><hr size=1>\n";
  die("</body></html>");
}

/* プロクシ接続チェック */
function proxy_connect($port) {
  $a=""; $b="";
  $fp = @fsockopen($_SERVER["REMOTE_ADDR"], $port, $a, $b, 2);
  if (!$fp) { return 0; } else { return 1; }
}

/* 記事書き込み */
function regist($name,$email,$sub,$com,$url,$pwd,$upfile,$upfile_name,$resto) {
  global $path,$badstring,$badfile,$badip,$pwdc,$textonly,$admin;
  $dest=""; $mes="";

  if ($_SERVER["REQUEST_METHOD"] != "POST") { error("不正な投稿をしないで下さい(post)"); }

  // 時間
  $time = time();
  $tim = $time.substr(microtime(), 2, 3);

  // アップロード処理
  if ($upfile && file_exists($upfile)) {
    $dest = $path.$tim.'.tmp';
    move_uploaded_file($upfile, $dest);
    // ↑でエラーなら↓に変更
    //copy($upfile, $dest);
    $upfile_name = CleanStr($upfile_name);
    if (!is_file($dest)) { error("アップロードに失敗しました<br>サーバがサポートしていない可能性があります", $dest); }
    $size = getimagesize($dest);
    if (!is_array($size)) { error("アップロードに失敗しました<br>画像ファイル以外は受け付けません", $dest); }
    $chk = md5_of_file($dest);
    foreach ($badfile as $value) {
      if (ereg("^$value", $chk)) {
        error("アップロードに失敗しました<br>禁止画像です", $dest); // 拒絶画像
    } }
    chmod($dest, 0666);
    $W = $size[0];
    $H = $size[1];

    switch ($size[2]) {
      case 2  : $ext = ".jpg"; break;
      case 3  : $ext = ".png"; break;
      case 1  : $ext = ".gif"; break;
//      case 4  : $ext = ".swf"; break;
//      case 5  : $ext = ".psd"; break;
      case 6  : $ext = ".bmp"; break;
//      case 13 : $ext = ".swf"; break;
      default : $ext = ".xxx"; error("対応しないフォーマットです。", $dest);
    }
    if ($ext == '.bmp') {
      error("アップロードに失敗しました<br>BMP形式はサポートしていません", $dest);
    }

    // 画像表示縮小
    if ($W > MAX_W || $H > MAX_H) {
      $W2 = MAX_W / $W;
      $H2 = MAX_H / $H;
      ($W2 < $H2) ? $key = $W2 : $key = $H2;
      $W = ceil($W * $key);
      $H = ceil($H * $key);
    }
    $mes = "画像 $upfile_name のアップロードが成功しました<br><br>";
  }

  // 拒絶する文字列
  foreach ($badstring as $value) {
    if(ereg($value, $com)
    || ereg($value, $sub)
    || ereg($value, $name)
    || ereg($value, $email)
    ) {
      error("拒絶されました(str)", $dest);
    };
  }

  // フォーム内容をチェック
  if (!$name || ereg("^[ |　|]*$", $name)) $name = "";
  if (!$com || ereg("^[ |　|\t]*$", $com)) $com = "";
  if (!$sub || ereg("^[ |　|]*$", $sub))   $sub = "";

  if (!$resto && !$textonly && !is_file($dest)) { error("画像がありません", $dest); }
  if (!$com && !is_file($dest)) { error("何か書いて下さい", $dest); }

  if ($admin != ADMIN_PASS) { // 管理人の場合はそのまま
    $name = str_replace("管理", "\"管理\"", $name);
    $name = str_replace("削除", "\"削除\"", $name);
  }

  if (strlen($com) > 1000)  { error("本文が長すぎますっ！", $dest); }
  if (strlen($name) > 100)  { error("名前が長すぎますっ！", $dest); }
  if (strlen($email) > 100) { error("メールが長すぎますっ！", $dest); }
  if (strlen($sub) > 100)   { error("題名が長すぎますっ！", $dest); }
  if (strlen($resto) > 10)  { error("レス番号が異常です", $dest); }
  if (strlen($url) > 10)    { error("異常です", $dest); }

  // ホスト取得
  $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);

  // 拒絶host
  foreach ($badip as $value) {
    if (eregi("$value$", $host)) {
      error("拒絶されました(host)", $dest);
  } }

  // プロクシチェック
  if (PROXY_CHECK && $admin != ADMIN_PASS) {
/*
    if(eregi("^mail", $host)
    || eregi("^ns", $host)
    || eregi("^dns", $host)
    || eregi("^ftp", $host)
    || eregi("^prox", $host)
    || eregi("^pc", $host)
    || eregi("^[^\.]\.[^\.]$", $host)
    ) {
      $pxck = "on";
    }
*/
    if(eregi("ne\\.jp$", $host)
    || eregi("ad\\.jp$", $host)
    || eregi("bbtec\\.net$", $host)
    || eregi("aol\\.com$", $host)
    || eregi("uu\\.net$", $host)
    || eregi("asahi-net\\.or\\.jp$", $host)
    || eregi("rim\\.or\\.jp$", $host)
    ) {
      $pxck = "off";
    }
    else {
      $pxck = "on";
    }
    // プロクシの疑いがある場合
    if ($pxck == "on") {
      if (proxy_connect('80') == 1) {
        error("ＥＲＲＯＲ！　公開ＰＲＯＸＹ規制中！！(80)", $dest);
      } elseif (proxy_connect('8080') == 1) {
        error("ＥＲＲＯＲ！　公開ＰＲＯＸＹ規制中！！(8080)", $dest);
      }
    }
  }

  // No.とパスと時間とURLフォーマット
  srand((double)microtime() * 1000000);
  if ($pwd == "") {
    if ($pwdc == "") {
      $pwd = rand(); $pwd = substr($pwd, 0, 8);
    } else {
      $pwd = $pwdc;
    }
  }

  $c_pass = $pwd;
  $pass = ($pwd) ? substr(md5($pwd), 2, 8) : "*";
  $youbi = array('日', '月', '火', '水', '木', '金', '土');
  $yd = $youbi[gmdate("w", $time + 9*60*60)];
  $now = gmdate("y/m/d", $time + 9*60*60)."(".(string)$yd.")".gmdate("H:i", $time + 9*60*60);
  // ID表示
  if (DISP_ID) {
    if ($email && DISP_ID == 1) {
      $now .= " ID:???";
    } else {
      $now .= " ID:".substr(crypt(md5($_SERVER["REMOTE_ADDR"].IDSEED.gmdate("Ymd", $time + 9*60*60)), 'id'), -8);
    }
  }
  // テキスト整形
  $email= CleanStr($email);  $email = ereg_replace("[\r\n]", "", $email);
  $sub  = CleanStr($sub);    $sub   = ereg_replace("[\r\n]", "", $sub);
  $url  = CleanStr($url);    $url   = ereg_replace("[\r\n]", "", $url);
  $resto= CleanStr($resto);  $resto = ereg_replace("[\r\n]", "", $resto);
  $com  = CleanStr($com);
  // 改行文字の統一
  $com = str_replace("\r\n", "\n", $com);
  $com = str_replace("\r", "\n", $com);
  // 連続する空行を一行
  $com = ereg_replace("\n((　| )*\n){3,}", "\n", $com);
  if (!BR_CHECK || substr_count($com, "\n") < BR_CHECK) {
    $com = nl2br($com); // 改行文字の前に<br>を代入する
  }
  $com = str_replace("\n", "", $com); // \nを文字列から消す

  $name = str_replace("◆", "◇", $name);
  $name = ereg_replace("[\r\n]", "", $name);
  $names = $name;
  $name = CleanStr($name);
  // トリップ
  if (ereg("(#|＃)(.*)", $names, $regs)) {
    $cap = $regs[2];
    $cap = strtr($cap, "&amp;", "&");
    $cap = strtr($cap, "&#44;", ",");
    $name = ereg_replace("(#|＃)(.*)", "", $name);
    $salt = substr($cap."H.", 1, 2);
    $salt = ereg_replace("[^\.-z]", ".", $salt);
    $salt = strtr($salt, ":;<=>?@[\\]^_`", "ABCDEFGabcdef");
    $name .= "</b>◆".substr(crypt($cap, $salt), -10)."<b>";
  }

  if (!$name) $name = NO_NAME;
  if (!$com)  $com  = NO_COM;
  if (!$sub)  $sub  = NO_TITLE;

  // ログ読み込み
  ignore_user_abort(1);
  $fp = fopen(LOGFILE, "r+") or error("ERROR! load log", $dest);
  set_file_buffer($fp, 0);
  flock($fp, LOCK_EX);
//  rewind($fp);
  $buf = fread($fp, 1000000);
  if ($buf == '') { error("error load log", $dest); }
  $line = explode("\n", $buf);
  $countline = count($line) - 1; // \nを数えるため
  for ($i = 0; $i < $countline; $i++) {
//    if (empty($line[$i])) { continue; }
    list($artno,) = explode(",", rtrim($line[$i])); // 逆変換テーブル作成
    $lineindex[$artno] = $i + 1;
    $line[$i] .= "\n";
  }

  // 二重投稿チェック
  $imax = ($countline > 20) ? 20 : $countline;
  for ($i = 0; $i < $imax; $i++) {
//    if (empty($line[$i])) { continue; }
    list($lastno,,$lname,,,$lcom,,$lhost,$lpwd,,,,$ltime,) = explode(",", $line[$i]);
    if (strlen($ltime) > 10) { $ltime = substr($ltime, 0, -3); }
    if ( ($host == $lhost) || (substr(md5($pwd), 2, 8) == $lpwd) || (substr(md5($pwdc), 2, 8) == $lpwd) ) { $pchk = 1; } else { $pchk = 0; }
    if (RENZOKU && $pchk && $time - $ltime < RENZOKU)
      error("連続投稿はもうしばらく時間を置いてからお願い致します", $dest);
    if (RENZOKU2 && $pchk && $time - $ltime < RENZOKU2 && $upfile_name)
      error("画像連続投稿はもうしばらく時間を置いてからお願い致します", $dest);
    if (RENZOKU && $pchk && $com == $lcom && !$upfile_name)
      error("本文が前回の投稿内容と同じです", $dest);
  }
  // ログ行数オーバー
  if ($countline > LOG_MAX) {
    for ($d = $countline - 1; $d >= LOG_MAX - 1; $d--) {
//      if (empty($line[$d])) { continue; }
      list($dno,,,,,,,,,$dext,,,$dtime,) = explode(",", $line[$d]);
      if (is_file($path.$dtime.$dext)) unlink($path.$dtime.$dext);
      if (is_file(THUMB_DIR.$dtime.'s.jpg')) unlink(THUMB_DIR.$dtime.'s.jpg');
// 萌えカウントログオーバー削除 -------------

$delmoecount = MOE_LOG.$dtime.MOE_KAKU;
if (is_file($delmoecount)) unlink($delmoecount);

// リダイレクトファイルログオーバー削除 -----

$delrehtm = RE_HTM_DIR.$dtime.".htm";
if (is_file($delrehtm)) unlink($delrehtm);

// ------------------------------------------
      $line[$d] = "";
      treedel($dno);
    }
  }
  // アップロード処理
  if ($dest && file_exists($dest)) {
    $imax = ($countline > 200) ? 200 : $countline;
    for ($i = 0; $i < $imax; $i++) { // 画像重複チェック
//      if (empty($line[$i])) { continue; }
      list(,,,,,,,,,$extp,,,$timep,$chkp,) = explode(",", $line[$i]);
      if ($chkp == $chk && file_exists($path.$timep.$extp)) {
        error("アップロードに失敗しました<br>同じ画像があります", $dest);
    } }
  }

  if (!$resto && !$textonly) {
    // 萌えカウント空ファイルをレス以外で画像が有る場合のみ作成　by 萌え連
    $logmoe = MOE_LOG.$tim.MOE_KAKU;
    $mfp = fopen($logmoe, "w");
    flock($mfp, LOCK_EX);
    set_file_buffer($mfp, 0);
    fputs($mfp, "0,0.0.0.0\n");
    fclose($mfp);
    chmod($logmoe, 0666);
    // 画像へのリダイレクト用HTMLの作成　by 萌え連
    if (USE_RE_HTM) {
      $rehtm = RE_HTM_DIR.$tim.".htm";
      $reimg = "../".IMG_DIR.$tim.$ext;
      $mfp = fopen($rehtm, "w");
      flock($mfp, LOCK_EX);
      set_file_buffer($mfp, 0);
      fputs($mfp, "<html><head><meta http-equiv=\"refresh\" content=\"0;URL=".$reimg."\"></head></html>");
      fclose($mfp);
      chmod($rehtm, 0666);
    }
  }

  list($lastno,) = explode(",", $line[0]);
  $no = $lastno + 1;
  isset($ext) ? 0 : $ext = "";
  isset($W)   ? 0 : $W   = "";
  isset($H)   ? 0 : $H   = "";
  isset($chk) ? 0 : $chk = "";
  $newline = "$no,$now,$name,$email,$sub,$com,$url,$host,$pass,$ext,$W,$H,$tim,$chk,\n";
  $newline .= implode('', $line);
//  set_file_buffer($fp, 0);
//  ftruncate($fp, 0);
  rewind($fp);
  fputs($fp, $newline);
  ftruncate($fp, ftell($fp));
  flock($fp, LOCK_UN);
  fclose($fp);
  ignore_user_abort(0);

  // ツリー更新
  $buf = ''; $line = ''; $newline = '';
  $find = FALSE;
  ignore_user_abort(1);
  $tp = fopen(TREEFILE, "r+") or error("ERROR! load tree", $dest);
  set_file_buffer($tp, 0);
  flock($tp, LOCK_EX);
//  rewind($tp);
  $buf = fread($tp, 1000000);
  if ($buf == '') { error("error tree update", $dest); }
  $line = explode("\n", $buf);
  $countline = count($line) - 1; // \nを数えるため
  for ($i = 0; $i < $countline; $i++) {
//    if (empty($line[$i])) { continue; }
    $line[$i] .= "\n";

    $j = explode(",", rtrim($line[$i]));
    if (is_null($lineindex[$j[0]])) {
      $line[$i] = ''; // ログに無ければ、空にする
    }

  }
  if ($resto) { // レス番号が指定されている場合
    for ($i = 0; $i < $countline; $i++) {
      $rtno = explode(",", rtrim($line[$i]));
      if ($rtno[0] == $resto) {
        $find = TRUE;
        $line[$i] = rtrim($line[$i]).','.$no."\n";
        $j = explode(",", rtrim($line[$i]));
        if (count($j) > MAX_RES) { $email = 'sage'; }
        if (!stristr($email, 'sage')) {
          $newline = $line[$i];
          $line[$i] = '';
        }
        break;
  } } }
  if (!$find) {
    if (!$resto) { $newline = "$no\n"; }
    else { error("スレッドがありません", $dest); }
  }
  $newline .= implode('', $line);
//  set_file_buffer($tp, 0);
//  ftruncate($tp, 0);
  rewind($tp);
  fputs($tp, $newline);
  ftruncate($tp, ftell($tp));
  flock($tp, LOCK_UN);
  fclose($tp);

  // クッキー保存
  setcookie("pwdc", $c_pass, time() + 7*24*3600); /* 1週間で期限切れ */
  if (function_exists("mb_internal_encoding") && function_exists("mb_convert_encoding")
    && function_exists("mb_substr")) {
    if (ereg("MSIE|Opera", $_SERVER["HTTP_USER_AGENT"])) {
      $i = 0; $c_name = '';
      mb_internal_encoding("SJIS");
      while ($j = mb_substr($names, $i, 1)) {
        $j = mb_convert_encoding($j, "UTF-16", "SJIS");
        $c_name .= "%u".bin2hex($j);
        $i++;
      }
      header("Set-Cookie: namec=$c_name; expires=".gmdate("D, d-M-Y H:i:s", time() + 7*24*3600)." GMT", false);
    } else {
      $c_name = $names;
      setcookie("namec", $c_name, time() + 7*24*3600); /* 1週間で期限切れ */
    }
  }

  if ($dest && file_exists($dest)) { // 画像ファイル処理
    rename($dest,$path.$tim.$ext);
    if (USE_THUMB) { thumb($path,$tim,$ext); }
  }
  updatelog();

  ignore_user_abort(0);

  echo "<html><head><meta http-equiv=\"refresh\" content=\"1;URL=",PHP_SELF2,"\"></head>",
       "<body>",$mes," 画面を切り替えます</body></html>";
}

/* サムネイル作成 */
function thumb($path, $tim, $ext) {
  if (!function_exists("ImageCreate") || !function_exists("ImageCreateFromJPEG")) { return; }
  $fname = $path.$tim.$ext; // ファイル名
  $thumb_dir = THUMB_DIR;   // サムネイル保存ディレクトリ
  $width     = MAX_W;       // 出力画像幅
  $height    = MAX_H;       // 出力画像高さ
  // 画像の幅と高さとタイプを取得
  $size = GetImageSize($fname);
  switch ($size[2]) {
    case 2 :
      $im_in = @ImageCreateFromJPEG($fname);
      if (!$im_in) { return; }
      break;
    case 3 :
      if (!function_exists("ImageCreateFromPNG")) { return; }
      $im_in = @ImageCreateFromPNG($fname);
      if (!$im_in) { return; }
      break;
    case 1 :
      if (function_exists("ImageCreateFromGIF")) {
        $im_in = @ImageCreateFromGIF($fname);
        if ($im_in) { break; }
      }
      if (!is_executable(realpath("./gif2png")) || !function_exists("ImageCreateFromPNG")) { return; }
      @exec(realpath("./gif2png")." $fname", $a);
      if (!file_exists($path.$tim.'.png')) { return; }
      $im_in = @ImageCreateFromPNG($path.$tim.'.png');
      unlink($path.$tim.'.png');
      if (!$im_in) { return; }
      break;
    default : return;
  }
  // リサイズ
  if ($size[0] > $width || $size[1] > $height) {
    $key_w = $width  / $size[0];
    $key_h = $height / $size[1];
    ($key_w < $key_h) ? $keys = $key_w : $keys = $key_h;
    $out_w = ceil($size[0] * $keys);
    $out_h = ceil($size[1] * $keys);
  } else {
    $out_w = $size[0];
    $out_h = $size[1];
  }
  // 出力画像（サムネイル）のイメージを作成   元画像を縦横とも コピー
  if (function_exists("ImageCreateTrueColor") && get_gd_ver() == "2") {
    $im_out = ImageCreateTrueColor($out_w, $out_h);
    ImageCopyResampled($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  } else {
    $im_out = ImageCreate($out_w, $out_h);
    ImageCopyResized($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  }
  // サムネイル画像を保存
  ImageJPEG($im_out, $thumb_dir.$tim.'s.jpg', 60);
  chmod($thumb_dir.$tim.'s.jpg', 0666);
  // 作成したイメージを破棄
  ImageDestroy($im_in);
  ImageDestroy($im_out);
}

/* gdのバージョンを調べる */
function get_gd_ver() {
  if (function_exists("gd_info")) {
    $gdver = gd_info();
    $phpinfo = $gdver["GD Version"];
  } else { // php4.3.0未満用
    ob_start();
    phpinfo(8);
    $phpinfo = ob_get_contents();
    ob_end_clean();
    $phpinfo = strip_tags($phpinfo);
    $phpinfo = stristr($phpinfo, "gd version");
    $phpinfo = stristr($phpinfo, "version");
  }
  $end = strpos($phpinfo, ".");
  $phpinfo = substr($phpinfo, 0, $end);
  $length = strlen($phpinfo) - 1;
  $phpinfo = substr($phpinfo, $length);
  return $phpinfo;
}

/* ファイルmd5計算 php4.2.0未満用 */
function md5_of_file($inFile) {
  if (file_exists($inFile)) {
    if (function_exists('md5_file')) {
      return md5_file($inFile);
    } else {
      $fd = fopen($inFile, 'r');
      $fileContents = fread($fd, filesize($inFile));
      fclose($fd);
      return md5($fileContents);
    }
  } else {
    return false;
} }

/* ツリー削除 */
function treedel($delno) {

  ignore_user_abort(1);
  $fp = fopen(TREEFILE, "r+") or error("ERROR! load tree");
  set_file_buffer($fp, 0);
  flock($fp, LOCK_EX);
//  rewind($fp);
  $buf = fread($fp, 1000000);
  if ($buf == '') { error("error tree del"); }
  $line = explode("\n", $buf);
  $countline = count($line) - 1;

  if ($countline > 1) {
    for ($i = 0; $i < $countline; $i++) {
//      if (empty($line[$i])) { continue; }
      $line[$i] .= "\n";
    }
    for ($i = 0; $i < $countline; $i++) {
      $treeline = explode(",", rtrim($line[$i]));
      $counttreeline = count($treeline);
      for ($j = 0; $j < $counttreeline; $j++) {
        if ($treeline[$j] == $delno) {
          $treeline[$j] = '';
          if ($j == 0) { $line[$i] = ''; }
          else {
            $line[$i] = implode(',', $treeline);
            $line[$i] = ereg_replace(",,", ",", $line[$i]);
            $line[$i] = ereg_replace(",$", "", $line[$i]);
            $line[$i] .= "\n";
          }
          break 2;
    } } }
    $renewline = implode('', $line);
//    set_file_buffer($fp, 0);
//    ftruncate($fp, 0);
    rewind($fp);
    fputs($fp, $renewline);
    ftruncate($fp, ftell($fp));
  }
  flock($fp, LOCK_UN);
  fclose($fp);
  ignore_user_abort(0);
}

/* テキスト整形 */
function CleanStr($str) {
  global $admin;

  $str = trim($str); // 先頭と末尾の空白除去
  if (get_magic_quotes_gpc()) { // ￥を削除
    $str = stripslashes($str);
  }
  if ($admin != ADMIN_PASS) { // 管理者はタグ可能
    $str = htmlspecialchars($str); // タグっ禁止
    $str = str_replace("&amp;", "&", $str); // 特殊文字
  }
  return str_replace(",", "&#44;", $str); // カンマを変換
}

/* ユーザー削除 */
function usrdel($no, $pwd) {
  global $path, $pwdc, $onlyimgdel;

  $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
  $delno = array("dummy");
  $delflag = FALSE;
  reset($_POST);
  while ($item = each($_POST)) {
    if ($item[1] == 'delete') {
      array_push($delno, $item[0]);
      $delflag = TRUE;
    }
  }
  if ($pwd == "" && $pwdc != "") $pwd = $pwdc;

  $fp = fopen(LOGFILE, "r");
//  set_file_buffer($fp, 0);
  flock($fp, LOCK_SH);
//  rewind($fp);
  $buf = fread($fp, 1000000);
  fclose($fp);
  if ($buf == '') { error("error user del"); }
  $line = explode("\n", $buf);
  $countline = count($line) - 1;

  for ($i = 0; $i < $countline; $i++) {
//    if (empty($line[$i])) { continue; }
    $line[$i] .= "\n";
  }
  $flag = FALSE;
  for ($i = 0; $i < $countline; $i++) {
    list($dno,,,,,,,$dhost,$pass,$dext,,,$dtim,) = explode(",", $line[$i]);
    if (array_search($dno, $delno) &&
      (substr(md5($pwd), 2, 8) == $pass || $dhost == $host || ADMIN_PASS == $pwd) ) {
      $flag = TRUE;
      $line[$i] = ""; // パスワードがマッチした行は空に
      $delfile = $path.$dtim.$dext; // 削除ファイル
      if (!$onlyimgdel) {
        treedel($dno);
      }
      if (is_file($delfile)) unlink($delfile); // 削除
      if (is_file(THUMB_DIR.$dtim.'s.jpg')) unlink(THUMB_DIR.$dtim.'s.jpg'); // 削除
// 萌えカウントログユーザー削除 -------------

$delmoecount = MOE_LOG.$dtim.MOE_KAKU;
if (is_file($delmoecount)) unlink($delmoecount);

// リダイレクトファイルログユーザー削除 -----

$delrehtm = RE_HTM_DIR.$dtim.".htm";
if (is_file($delrehtm)) unlink($delrehtm);

// ------------------------------------------
    }
  }
  if (!$flag) { error("該当記事が見つからないかパスワードが間違っています"); }
}

/* パス認証 */
function valid($pass) {
  if ($pass && $pass != ADMIN_PASS) error("パスワードが違います");

  head($dat);
  echo $dat,
       "[<a href=\"",PHP_SELF2,"\">掲示板に戻る</a>]\n",
       "[<a href=\"",PHP_SELF,"\">ログを更新する</a>]\n",
       "<table width=\"100%\"><tr><th bgcolor=\"",BASE_COL,"\">\n",
       "<font color=\"#FFFFFF\">管理モード</font>\n",
       "</th></tr></table>\n",
       "<form action=\"",PHP_SELF,"\" method=\"POST\">\n";
  // ログインフォーム
  if (!$pass) {
    echo <<<__EOD__
<div align=center>
<table border=0><tr><td>
<input type="radio" name="admin" value="del" checked>記事削除<br>
<input type="radio" name="admin" value="post">管理人投稿<br>
<input type="radio" name="admin" value="moecount">萌えカウント管理<br>
<input type="radio" name="admin" value="moeden">殿堂ギャラリー管理<br>
<input type="hidden" name="mode" value="admin">
</td></tr></table>
<input type="password" name="pass" size=8>
<input type="submit" value=" 認証 ">
</div>
</form>\n
__EOD__;
    die("</body></html>");
  }
}

/* 管理者削除 */
function admindel($pass) {
  global $path, $onlyimgdel;

  $delflag = FALSE;
  $delno = array("dummy");
  reset($_POST);
  while ($item = each($_POST)) {
    if ($item[1] == 'delete') {
      array_push($delno, $item[0]);
      $delflag = TRUE;
    }
  }
  if ($delflag) {
    ignore_user_abort(1);
    $fp = fopen(LOGFILE, "r+") or error("ERROR! load log", '', TRUE);
    set_file_buffer($fp, 0);
    flock($fp, LOCK_EX);
//    rewind($fp);
    $buf = fread($fp, 1000000);
    if ($buf == '') { error("error admin del", '', TRUE); }
    $line = explode("\n", $buf);
    $countline = count($line) - 1; // \nを数えるため

    for ($i = 0; $i < $countline; $i++) {
//      if (empty($line[$i])) { continue; }
      $line[$i] .= "\n";
    }
    $find = FALSE;
    for ($i = 0; $i < $countline; $i++) {
      list($no,,,,,,,,,$ext,,,$tim,) = explode(",", $line[$i]);
      if ($onlyimgdel == "on") {
        if (array_search($no, $delno)) { // 画像だけ削除
          $delfile = $path.$tim.$ext; // 削除ファイル
          if (is_file($delfile)) unlink($delfile); // 削除
          if (is_file(THUMB_DIR.$tim.'s.jpg')) unlink(THUMB_DIR.$tim.'s.jpg'); // 削除
// 萌えカウント管理人削除 -------------------

$delmoecount = MOE_LOG.$tim.MOE_KAKU;
if (is_file($delmoecount)) unlink($delmoecount);

// リダイレクトファイルログ管理人削除 -------

$delrehtm = RE_HTM_DIR.$tim.".htm";
if (is_file($delrehtm)) unlink($delrehtm);

// ------------------------------------------
        }
      } else {
        if (array_search($no, $delno)) { // 削除の時は空に
          $find = TRUE;
          $line[$i] = "";
          $delfile = $path.$tim.$ext; // 削除ファイル
          if (is_file($delfile)) unlink($delfile); // 削除
          if (is_file(THUMB_DIR.$tim.'s.jpg')) unlink(THUMB_DIR.$tim.'s.jpg'); // 削除
// 萌えカウント管理人削除 -------------------

$delmoecount = MOE_LOG.$tim.MOE_KAKU;
if (is_file($delmoecount)) unlink($delmoecount);

// リダイレクトファイルログ管理人削除 -------

$delrehtm = RE_HTM_DIR.$tim.".htm";
if (is_file($delrehtm)) unlink($delrehtm);

// ------------------------------------------
          treedel($no);
        }
      }
    }
    if ($find) { // ログ更新
      $renewline = implode('', $line);
//      set_file_buffer($fp, 0);
//      ftruncate($fp, 0);
      rewind($fp);
      fputs($fp, $renewline);
      ftruncate($fp, ftell($fp));
    }
    flock($fp, LOCK_UN);
    fclose($fp);
    ignore_user_abort(0);
  }

  // 削除画面を表示
  echo <<<__EOD__
<input type="hidden" name="mode" value="admin">
<input type="hidden" name="admin" value="del">
<input type="hidden" name="pass" value="$pass">
<div align=center>
・削除したい記事のチェックボックスにチェックを入れ、削除ボタンを押して下さい。
<p><input type="submit" value="削除する"><input type="reset" value="リセット">
[<input type="checkbox" name="onlyimgdel" value="on">画像だけ消す]</p>\n
__EOD__;

  echo "<table border=1 cellspacing=0>\n",
       "<tr bgcolor=\"#6080f6\"><th>削除</th><th>記事No</th><th>投稿日</th><th>題名</th>",
       "<th>投稿者</th><th>コメント</th><th>ホスト名</th><th>添付<br>(Bytes)</th><th>md5</th></tr>\n";

  // ログファイル読み込み
  $line = @file(LOGFILE);
  $countline = count($line);
  $all = 0;
  for ($j = 0; $j < $countline; $j++) {
    $img_flag = FALSE;
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pw,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
    // フォーマット
    $now = ereg_replace('.{2}/(.*)$', '\1', $now);
    $now = ereg_replace('\(.*\)', ' ', $now);
    if (strlen($name) > 10) $name = substr($name, 0, 9).".";
    if (strlen($sub) > 10)  $sub  = substr($sub,  0, 9).".";
    if ($email) $name = "<a href=\"mailto:".$email."\">".$name."</a>";
    $com = str_replace("<br />", " ", $com);
    $com = htmlspecialchars($com);
    if (strlen($com) > 20) $com = substr($com, 0, 18).".";
    // 画像があるときはリンク
    if ($ext && is_file($path.$time.$ext)) {
      $img_flag = TRUE;
      $clip = "<a href=\"".IMG_DIR.$time.$ext."\" target=\"_blank\">".$time.$ext."</a><br>";
      $size = filesize($path.$time.$ext);
      $chk  = substr($chk, 0, 10);
      $all += $size; // 合計計算
    } else {
      $clip = "";
      $chk  = "";
      $size = 0;
    }
    $bg = ($j % 2) ? "#d6d6f6" : "#f6f6f6"; // 背景色

    echo "<tr bgcolor=\"",$bg,"\">\n",
         "<th><input type=\"checkbox\" name=\"",$no,"\" value=\"delete\"></th>",
         "<th>",$no,"</th><td><small>",$now,"</small></td><td>",$sub,"</td>",
         "<td><b>",$name,"</b></td><td><small>",$com,"</small></td><td>",$host,"</td>",
         "<td align=\"center\">",$clip,"(",$size,")</td><td>",$chk,"</td>\n</tr>\n";
  }
  $all = (int)($all / 1024);

  echo "</table>\n<p><input type=\"submit\" value=\"削除する\">",
       "<input type=\"reset\" value=\"リセット\"></p>\n</div>\n</form>\n",
       "<div align=center>【 画像データ合計 : <b>",$all,"</b> KB 】</div>\n";
  die("</body></html>");
}

/* 初期設定 */
function init() {
  $err = "";
  $chkfile = array(LOGFILE, TREEFILE, MOE_DLOG);
  if (!is_writable(realpath("./"))) error("カレントディレクトリに書けません<br>");
  foreach ($chkfile as $value) {
    if (!file_exists(realpath($value))) {
      $fp = fopen($value, "w");
      set_file_buffer($fp, 0);
      if ($value == LOGFILE)  fputs($fp, "1,2002/01/01(月)00:00,名無し,,無題,本文なし,,,,,,,1009810800,,\n");
      if ($value == TREEFILE) fputs($fp, "1\n");
      if ($value == MOE_DLOG) fputs($fp, "");
      fclose($fp);
      if (file_exists(realpath($value))) @chmod($value, 0666);
    }
    if (!is_writable(realpath($value))) $err .= $value."を書けません<br>";
    if (!is_readable(realpath($value))) $err .= $value."を読めません<br>";
  }
  @mkdir(MOE_LOG, 0777); @chmod(MOE_LOG, 0777);
  if (!is_dir(realpath(MOE_LOG))) $err .= MOE_LOG."がありません<br>";
  if (!is_writable(realpath(MOE_LOG))) $err .= MOE_LOG."を書けません<br>";
  if (!is_readable(realpath(MOE_LOG))) $err .= MOE_LOG."を読めません<br>";

  @mkdir(RE_HTM_DIR, 0777); @chmod(RE_HTM_DIR, 0777);
  if (!is_dir(realpath(RE_HTM_DIR))) $err .= RE_HTM_DIR."がありません<br>";
  if (!is_writable(realpath(RE_HTM_DIR))) $err .= RE_HTM_DIR."を書けません<br>";
  if (!is_readable(realpath(RE_HTM_DIR))) $err .= RE_HTM_DIR."を読めません<br>";

  @mkdir(MOE_IMG, 0777); @chmod(MOE_IMG, 0777);
  if (!is_dir(realpath(MOE_IMG))) $err .= MOE_IMG."がありません<br>";
  if (!is_writable(realpath(MOE_IMG))) $err .= MOE_IMG."を書けません<br>";
  if (!is_readable(realpath(MOE_IMG))) $err .= MOE_IMG."を読めません<br>";

  @mkdir(IMG_DIR, 0777); @chmod(IMG_DIR, 0777);
  if (!is_dir(realpath(IMG_DIR))) $err .= IMG_DIR."がありません<br>";
  if (!is_writable(realpath(IMG_DIR))) $err .= IMG_DIR."を書けません<br>";
  if (!is_readable(realpath(IMG_DIR))) $err .= IMG_DIR."を読めません<br>";

  if (USE_THUMB) {
    @mkdir(THUMB_DIR, 0777); @chmod(THUMB_DIR, 0777);
    if (!is_dir(realpath(THUMB_DIR))) $err .= THUMB_DIR."がありません<br>";
    if (!is_writable(realpath(THUMB_DIR))) $err .= THUMB_DIR."を書けません<br>";
    if (!is_readable(realpath(THUMB_DIR))) $err .= THUMB_DIR."を読めません<br>";
  }
  if ($err) error($err);
}

/* 殿堂ギャラリー管理画面 by 萌え連 */
function adminden($pass) {

  $selfpath = PHP_SELF;
  echo "</form>\n";

  // 表示制限がある場合
  if (MOE_DNAG) {
    echo "<div align=center><table border=0><tr><td><ul>\n",
         "<li><small>ギャラリーには最新の",MOE_DNAGM,"件のみ画像を表示しています。</small>\n",
         "<li><small>",MOE_DNAGM,"件以降表示はされていませんが画像は存在します。</small>\n",
         "</ul></td></tr></table></div>\n";
  }

  echo <<<__TMP__
<div align=center>
<table border=0 cellspacing=1>
<tr bgcolor="#6080f6">
<th>No</th><th>投稿日</th><th>投稿者</th><th>画像</th><th>処理</th>
</tr>\n
__TMP__;

  // 殿堂ログ読み込み
  $mdlog = @file(MOE_DLOG);
  $countmd = count($mdlog);

  $all = 0;
  for ($i = 0; $i < $countmd; $i++) {
    $img_flag = FALSE;
    list($no,$now,$name,$time,$ext,$w,$h,$chk) = explode(",", $mdlog[$i]);

    // 画像サイズ調整
    $aw = ceil($w / 3);
    $ah = ceil($h / 3);

    $src = MOE_IMG.$time.$ext;
    // 画像があるときはリンク
    if ($ext && is_file($src)) {
      $img_flag = TRUE;
      $size = filesize($src);
      $all += $size; // 合計計算
      $clip = "<a href=\"".$src."\" target=\"_blank\">".
              "<img src=\"".$src."\" border=0 width=".$aw." height=".$ah." alt=\"".$size." B\"></a>";
    } else {
      $size = 0;
      $clip = "画像無し";
    }
    $bg = "#f6f6f6"; $bg2 = "#d6d6f6"; // 背景色

    // 記事選択画面を表示
    echo <<<__TMP__
<tr bgcolor="$bg">
<th bgcolor="$bg2">$no</th><td align=center>$now</td><td align=center><b>$name</b></td>
<td align=center>$clip</td>
<td><form action="$selfpath" method="POST">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="pass" value="$pass">
<input type="hidden" name="edenadmin" value="$time">
<input type="submit" value=" 削除 ">
</form></td>
</tr>\n
__TMP__;

  }
  $all = (int)($all / 1024);

  echo <<<__TMP__
</table><br>
【 画像データ合計 : <b>$all</b> KB 】</div>\n
__TMP__;
  die("</body></html>");
}

/* 萌え殿堂編集 by 萌え連 */
function editden($pass) {
  global $edenadmin;

  // 殿堂ログ読み込み
  $fp = fopen(MOE_DLOG, "r+") or error("ERROR! moeden log!", '', TRUE);
  set_file_buffer($fp, 0);
  flock($fp, LOCK_EX);
  $buf = fread($fp, 1000000);
  if ($buf == '') { error("error admin renew", '', TRUE); }
  $mdlog = explode("\n", $buf);
  $countmd = count($mdlog) - 1;

  // ログからデータを削除
  $renew = ""; $find = FALSE;
  for ($i = 0; $i < $countmd; $i++) {
    list($no,$now,$name,$time,$ext,$w,$h,$chk) = explode(",", $mdlog[$i]);
    if ($time == $edenadmin) {
      $dno = $no;
      $dext = $ext;
      $find = TRUE;
    } else {
      $renew .= $mdlog[$i]."\n";
    }
  }
  if ($find) {
    rewind($fp);
    fputs($fp, $renew);
    ftruncate($fp, ftell($fp));
    fclose($fp);
  } else {
    fclose($fp);
    error("削除対象が見つかりませんでした", '', TRUE);
  }

  // 殿堂入り画像削除
  $ddel = MOE_IMG.$edenadmin.$dext;
  if (is_file($ddel)) unlink($ddel);

  // カウントログ修正
  $logmoe = MOE_LOG.$edenadmin.MOE_KAKU;
  if (is_file($logmoe)) {
    $fp = fopen($logmoe, "w");
    flock($fp, LOCK_EX);
    set_file_buffer($fp, 0);
    fputs($fp, "0,0.0.0.0\n");
    fclose($fp);
    chmod($logmoe, 0666);
  }

  // アップデート
  updatelog();

  // 結果を表示
  echo <<<__TMP__
<br><br><hr size=1><br><br>
<div align=center>
<font color="red" size=5><b>殿堂から削除しました　No.{$dno}</b></font><p>
<input type="hidden" name="mode" value="admin">
<input type="hidden" name="admin" value="moeden">
<input type="hidden" name="pass" value="$pass">
<input type="submit" value=" 管理画面に戻る ">
</div>
</form><hr size=1>\n
__TMP__;
  die("</body></html>");
}

/* 萌えカウント管理画面 by 萌え連 */
function adminmoe($pass) {
  global $path;

  if (is_file(MOE_BOTP)) {
    $meter = "<th>ﾒｰﾀｰ<BR><img src=\"".MOE_BOTP."\" alt=\"\"></th>";
    $mflag = TRUE;
  } else {
    $meter = "";
    $mflag = FALSE;
  }

  echo <<<__TMP__
</form>
<ul><li>ラストカウントIPにカーソルを乗せると全カウントIPログが表示されます。</li></ul>
<div align=center>
<table border=1 cellspacing=0>
<tr bgcolor="#6080f6">
<th>No</th><th>投稿日</th><th>題名</th><th>投稿者</th><th>コメント</th>
<th>ﾗｽﾄｶｳﾝﾄIP</th><th>添付<br>(Bytes)</th>$meter<th>ｶｳﾝﾄ</th>
</tr>\n
__TMP__;

  // ログファイル読み込み
  $line = @file(LOGFILE);
  $countline = count($line);
  $all = 0; $bgcol = 0;
  for ($j = 0; $j < $countline; $j++) {
    $img_flag = FALSE; $datip = "";
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pw,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);

    // カウントログがあれば表示
    $logmoe = MOE_LOG.$time.MOE_KAKU;
    if ($ext && is_file($path.$time.$ext) && is_file($logmoe)) {

      // フォーマット
      $now = ereg_replace('.{2}/(.*)$', '\1', $now);
      $now = ereg_replace('\(.*\)', ' ', $now);
      if (strlen($name) > 10) $name = substr($name, 0, 9).".";
      if (strlen($sub) > 10)  $sub  = substr($sub,  0, 9).".";
      if ($email) $name = "<a href=\"mailto:".$email."\">".$name."</a>";
      $com = str_replace("<br />", " ", $com);
      $com = htmlspecialchars($com);
      if (strlen($com) > 20) $com = substr($com, 0, 18).".";
      // 画像があるときはリンク
      if ($ext && is_file($path.$time.$ext)) {
        $img_flag = TRUE;
        $clip = "<a href=\"".IMG_DIR.$time.$ext."\" target=\"_blank\">".$time.$ext."</a><br>";
        $size = filesize($path.$time.$ext);
        $all += $size; // 合計計算
      } else {
        $clip = "";
        $size = 0;
      }
      $bg = ($bgcol++ % 2) ? "#d6d6f6" : "#f6f6f6"; // 背景色

      // カウントログ読み込み
      $mp_data = file($logmoe);
      $countmp = count($mp_data);

      for ($i = 0; $i < $countmp; $i++) {
        list($mcountlog, $mcountip) = explode(",", $mp_data[$i]);
        $mcountip = trim($mcountip);
        $datip .= "[".$mcountlog."]".$mcountip." "; // &#13;&#10;
      }

      // 棒グラフ計算
      if ($mflag) {
        if ($mcountlog == 'DEN') {
          $bar = 100;
        } else {
          $bar = (int)(100 * $mcountlog / MOE_DCNT);
        }
        $bar = "<td><img src=\"".MOE_BOTP."\" width=\"".$bar."%\" height=20 alt=\"\"></td>";
      } else {
        $bar = "";
      }

      // 編集画面を表示
      echo <<<__TMP__
<tr bgcolor="$bg">
<th>$no</th><td><small>$now</small></td><td>$sub</td>
<td><b>$name</b></td><td><small>$com</small></td>
<td><a title="$datip">$mcountip</a></td>
<td align=center>$clip($size)</td>{$bar}\n
__TMP__;

      echo '<td align=center>',
           '<form action="',PHP_SELF,'" method="POST">',
           '<input type="text" name="newcount" value="',$mcountlog,'" size=4 maxlength=4>',
           '<input type="hidden" name="mode" value="edit">',
           '<input type="hidden" name="pass" value="',$pass,'">',
           '<input type="hidden" name="counttno" value="',$no,'">',
           '<input type="hidden" name="countedit" value="',$time,'">',
           '<input type="submit" name="ctedit" value="更新">',
           '<input type="submit" name="ctedit" value="リセット">',
           '</td></form>',
           "</tr>\n";
    }
  }
  $all = (int)($all / 1024);

  echo <<<__TMP__
</table><br>
【 画像データ合計 : <b>$all</b> KB 】</div>\n
__TMP__;
  die("</body></html>");
}

/* 萌えカウント編集 by 萌え連 */
function editcnt($pass) {
  global $counttno, $countedit, $ctedit, $newcount;

  $logmoe = MOE_LOG.$countedit.MOE_KAKU;
  if (!file_exists($logmoe)) {
    error("該当ログが見つかりませんでした", '', TRUE);
  }

  $mp_data = file($logmoe);
  $countmp = count($mp_data);
//  for ($i = 0; $i < $countmp; $i++) {
//    list($mcountlog,) = explode(",", $mp_data[$i]);
//  }
  list($mcountlog,) = explode(",", $mp_data[$countmp - 1]);

  if ($ctedit == 'リセット') { // カウント値を元に戻す

    $fp = fopen($logmoe, "w");
    flock($fp, LOCK_EX);
    set_file_buffer($fp, 0);
    fputs($fp, "0,0.0.0.0\n");
    fclose($fp);
    chmod($logmoe, 0666);
    $msg = "!!CountReset!!<br><br>".$logmoe."<br>".$mcountlog." count -&gt; 0 count";

  } else { // カウント値を更新

    if ($newcount != 'DEN') {
      if (!is_numeric($newcount)) {
        error("カウント値が不正です　No.".$counttno, '', TRUE);
      }
      if ($newcount >= MOE_DCNT) { // 殿堂イン
        $newcount = 'DEN';
      }
    } else {
      if ($mcountlog == 'DEN') {
        error("すでに殿堂入りしています　No.".$counttno, '', TRUE);
      }
    }

    $ip = "0.0.0.0"; // $_SERVER['REMOTE_ADDR']
    $wnew = implode(",", array($newcount, $ip, "\n"));
    $fp = fopen($logmoe, "a");
    set_file_buffer($fp, 0);
    flock($fp, LOCK_EX);
    fputs($fp, $wnew);
    fclose($fp);
    chmod($logmoe, 0666);
    $msg = "!!CountEdit!!<br><br>".$logmoe."<br>".$mcountlog." count -&gt; ".$newcount." count";
  }
  updatelog();

  // 結果を表示
  echo <<<__TMP__
<br><br><hr size=1><br><br>
<div align=center>
<font color="red" size=5><b>$msg</b></font><p>
<input type="hidden" name="mode" value="admin">
<input type="hidden" name="admin" value="moecount">
<input type="hidden" name="pass" value="$pass">
<input type="submit" value=" 管理画面に戻る ">
</div>
</form><hr size=1>\n
__TMP__;
  die("</body></html>");
}

/* 萌えカウントカウントアップシステム（旧moecount.php）by 萌え連 */
function votecount() {
  global $mcount, $moeno;

  $ip = $_SERVER['REMOTE_ADDR'];

  $mlogfile = MOE_LOG.$mcount.MOE_KAKU;
  if (!is_file($mlogfile)) {
    error("ログファイルが存在しません　No.".$moeno);
  }

  // カウントログ読み込み
  $logmoe = file($mlogfile);
  $countlogmoe = count($logmoe);
  $ipflag = FALSE;
  if (MOE_IPC) { // 投稿制限あり
    if (MOE_IPO) { // 直前のみ
      list($vcountlog, $vipck) = explode(",", $logmoe[$countlogmoe - 1]);
      if (trim($vipck) == $ip) { $ipflag = TRUE; }
    } else { // すべて
      for ($i = 0; $i < $countlogmoe; $i++) {
        list($vcountlog, $vipck) = explode(",", $logmoe[$i]);
        if (trim($vipck) == $ip) {
          $ipflag = TRUE;
          break;
    } } }
  } else { // 投稿制限なし
//    for ($i = 0; $i < $countlogmoe; $i++) {
//      list($vcountlog,) = explode(",", $logmoe[$i]);
//    }
    list($vcountlog,) = explode(",", $logmoe[$countlogmoe - 1]);
  }

  if (!$ipflag) { // 同一IPが無い場合
    if ($vcountlog != 'DEN') {
      $countm = $vcountlog + 1;
      if ($countm >= MOE_DCNT) { //殿堂イン
        $countm = 'DEN';
      }
    } else {
      $countm = 'DEN';
    }
    // カウントログ更新
    $wnew = implode(",", array($countm, $ip, "\n"));
    $fp = fopen($mlogfile, "a");
    set_file_buffer($fp, 0);
    flock($fp, LOCK_EX);
    fputs($fp, $wnew);
    fclose($fp);
    chmod($mlogfile, 0666);
    if (!strcmp($countm, 'DEN')) {
      $msg = "殿堂入りしました！";
    } else {
      $msg = "萌えカウントに投票しました。";
    }
  } else { // IP制限
    $msg = "ひとつの画像に複数回投票はできません。";
  }

  updatelog();
  $self2_path = PHP_SELF2;

  // スタイルシート
  $style_txt = '<style type="text/css">
<!--
body,tr,td,th {
  color: '.TXT_COL.';
  background-color: '.BG_COL.';
  font-size: 12pt;
}
div { text-align: center; }

a { text-decoration: none }
a:link    { color: '.LINK_COL.'; }
a:visited { color: '.VLINK_COL.'; }
a:hover   { color: '.ALINK_COL.'; }
span  { font-size:20pt; }
small { font-size:10pt; }
big   { font-size:18pt; }
-->
</style>';

  // 更新画面を表示
  echo <<<__EOD__
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="refresh" content="2;URL=$self2_path">
$style_txt
<title>MoeCountSystem</title>
</head>
<body>
<div>
<p><br><strong><big>$msg</big></strong></p>
<p><small>--- MoeCountSystem Ver 2.08 ---</small></p>
</div>
<div>
<span style="font-size: 8pt"><a href="$self2_path">リロード</a></span>
</div>
</body>
</html>\n
__EOD__;

  exit;
}

/* 萌えカウント殿堂ギャラリ（旧denview.php) by 萌え連 */
function denview() {
  global $denpage, $nowpage;

  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<style type="text/css">
<!--
body,tr,td,th {
  color: ',TXT_COL,';
  background-color: ',BG_COL,';
  font-size: 12pt;
}
div { text-align: center; }
div.back { text-align: right; }

a { text-decoration: none }
a:link    { color: ',LINK_COL,'; }
a:visited { color: ',VLINK_COL,'; }
a:hover   { color: ',ALINK_COL,'; }
span  { font-size:20pt; }
small { font-size:10pt; }
big   { font-size:18pt; }

table.garelly {
  width: 90%;
  border-style: solid;
  border-color: #d6d6f6;
  border-width: 1px;
  padding: 10px;
  margin:  10px;
  text-align: center;
  vertical-align: middle;
}

td.garelly {
  background-color: #ffffff;
  border-style: groove;
  border-width: 0px;
  padding: 5px;
  margin:  5px;
  text-align: center;
  vertical-align: middle;
}

td.gauther {
  background-color: #5555ff;
  color: #ffffff;
  border-style: groove;
  border-width: 1px;
  padding: 5px;
  margin:  5px;
  text-align: center;
  vertical-align: middle;
}

-->
</style>
<title>',MOE_TITLE,'</title>
</head>
<body>
';

  echo "<div class=\"back\">[<a href=\"",PHP_SELF2,"\">戻る</a>]</div>\n",
       "<div><font color=\"#cc1105\" size=\"+2\"><b><span>",MOE_TITLE2,"</span></b></font>\n";

  if (MOE_DNAG) {
    echo "<ul><li><small>最新の",MOE_DNAGM,"件のみ画像を表示、以下名前と日付のみとなります。</small></li></ul>\n";
  }
  echo "</div>\n";

  // 初期ポインタ
  $start = 0;
  $stop  = MOE_DPG;

  // 次へ, 前へ のポインタ
  if (!strcmp($denpage, 'next')) {
    $start = $nowpage + MOE_DPG;
    $stop  = $start   + MOE_DPG;
  } elseif (!strcmp($denpage, 'back')) {
    $stop  = $nowpage;
    $start = $stop - MOE_DPG;
  }

  // 殿堂ログ読み込み
  $mdlog = @file(MOE_DLOG);
  $countmd = count($mdlog);

  // 一定分繰り返し
  $imax = ($stop > $countmd) ? $countmd : $stop; // 上限の設定
  $imin = ($start < 0) ? 0 : $start;             // 下限の設定
  for ($i = $imin; $i < $imax; $i++) {
    $img_flag = FALSE;
    list($no,$now,$name,$time,$ext,$w,$h,$chk) = explode(",", $mdlog[$i]);

    $src = MOE_IMG.$time.$ext;
    // 画像があるときはリンク
    if ($ext && is_file($src)) {
      if (MOE_DNAG && $i >= MOE_DNAGM) {
        $clip = "画像の表示は出来ません";
      } else {
        $img_flag = TRUE;
        $size = filesize($src);
        $clip = "<a href=\"".$src."\" target=\"_blank\">".
                "<img src=\"".$src."\" border=0 width=".$w." height=".$h." alt=\"".$size." B\"></a>";
      }
    } else {
      $clip = "画像削除済み";
    }
    // 記事部分
    echo <<<__TMP__
<div>
<table class="garelly">
 <tr>
  <td class="garelly">$clip</td>
 </tr><tr>
  <td class="gauther">投稿者：<b>$name</b><br>投稿日：$now</td>
 </tr>
</table>
</div>\n
__TMP__;

  }

  // 移動ボタン部分
  echo "<br>\n<div>",
       "<form action=\"",PHP_SELF,"\" method=\"POST\">",
       "<input type=\"hidden\" name=\"nowpage\" value=\"",$start,"\">",
       "<input type=\"hidden\" name=\"denview\" value=\"view\">";

  if ($stop > MOE_DPG) {
    echo '<input type="submit" name="denpage" value="back">';
  }
  echo " 現在 $start ～ $stop ";
  if ($start < ($countmd - MOE_DPG)) {
    echo '<input type="submit" name="denpage" value="next">';
  }
  echo "</form></div>\n</body></html>";
  exit;
}

/*---------- Main ----------*/

$iniv=array('mode','name','email','sub','com','pwd','upfile','upfile_name','resto','pass','res','post','no','moeta','denview');
foreach ($iniv as $iniva) {
  if (!isset($$iniva)) { $$iniva = ""; }
}

/* カウントシステム関係 by 萌え連 */
if (!strcmp($moeta, 'countup')) // 萌えカウントカウントアップ
  votecount();
if (!strcmp($denview, 'view'))  // 萌えカウント殿堂ギャラリ
  denview();

switch ($mode) {
  case 'regist' :
    regist($name, $email, $sub, $com, '', $pwd, $upfile, $upfile_name, $resto);
    break;
  case 'admin' :
    valid($pass);
    if ($admin == "del") admindel($pass);
    if ($admin == "post") {
      echo "</form>";
      form($post, $res, TRUE);
      echo $post;
      die("</body></html>");
    }
    if ($admin == "moecount") adminmoe($pass);
    if ($admin == "moeden")   adminden($pass);
    break;
  case 'edit' :
    valid($pass);
    if (isset($edenadmin)) editden($pass);
    if (isset($countedit)) editcnt($pass);
    break;
  case 'usrdel' :
    usrdel($no, $pwd);
  default :
    if ($res) {
      updatelog($res);
    } else {
      updatelog();
      echo "<meta http-equiv=\"refresh\" content=\"0;URL=",PHP_SELF2,"\">";
    }
}
?>
