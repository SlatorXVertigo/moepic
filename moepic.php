<?php
/* �摜�f����

                                moepic.php v2.08
                      (gazou.php + futaba.php + moepic.php)

���̌f���͖G���A���񐻍�� moepic �� futaba �̕ύX�_��K�p�������̂ł��B
���̃X�N���v�g�Ɋւ���o�O�₲����͔z�z���̌f���ɂčs���܂��B
���ꂮ����A�G���A����A�ӂ��΂���A���b�cPHP!����ɂ��̃X�N���v�g�Ɋւ���
����Ȃǂ����Ȃ��ł��������B���T�C�g�l�ɖ��f�������Ȃ��悤���肢���܂��B

�Emoepic.php v2.08 [04/10/24]  URL:�G���A<http://moepic.dip.jp/gazo/>
�Efutaba.php v0.8  lot.051031  URL:�ӂ���<http://www.2chan.net/script/>
�Egazou.php                    URL:���b�cPHP!<http://php.s3.to/>

�z�z�����̓��b�cPHP!�y�тӂ��΋y�іG���A�ɏ����܂��B
�����A�Ĕz�z�͎��R�ɂǂ����B

�ݒu�@�F
�P�C�T�C�g��PHP�Ή����ǂ����𒲂ׂ�B
�Q�C�T�C�g�̐ݒu�������ꏊ�Ƀf�B���N�g�������p�[�~�b�V������777�ɐݒ肷��B
�R�Cmoepic.php�i���̃t�@�C���j�̐ݒ���e��ҏW����B
�S�C�z�z����Ă���Q�t�@�C�����T�C�g�ɓ]������B(moepic.php, moeta.gif)
�T�C�ݒu�ꏊ���� [src] [src_d] [thumb] [redirecthtm] �f�B���N�g�������
�@�@�p�[�~�b�V������777�ɐݒ肷��B�i�X�N���v�g�ɂ�鎩���쐬�̏ꍇ�C
�@�@�s����o�鋰�ꂪ���邽�߁A�K���蓮�ō쐬���Ă��������B�j
�U�Cmoepic.php���u���E�U����ǂݍ��ނƎ����I�ɕK�v�ȃt�@�C�����쐬����܂��B
�� moeta.gif �͖G���A����̉摜�����̂܂ܗ��p�����Ă��������܂����B

gif2png<http://www.tuxedo.org/~esr/gif2png/>������ꍇ�́A
gif�ł��T���l�C�������܂��B�t���̃o�C�i���� linux-i386 �p�ł��B
�� Unisys �́uLZW�����v�������ƂȂ����̂ŁA�ŐV�� GD �ł͕K�v����܂���B

--------------------------------------------------------------------------------
�����̃X�N���v�g�͖G���A����ɔz�z���𒸂��Ă��܂��B

�X�V���e�F
06/02/16
	futaba051031++ (futaba051031 fix.) �̕ύX�_��K�p
	���e�œK��
05/11/06
	futaba.php [05/10/31] �܂ł̕ύX�_��K�p
*/

// �ȉ�2�s�͒����p���
ignore_user_abort(0);
//error_reporting(E_ALL);

// �O���[�o���ϐ��̃Z�b�g
extract($_POST,   EXTR_SKIP);
extract($_GET,    EXTR_SKIP);
extract($_COOKIE, EXTR_SKIP);

$upfile_name = isset($_FILES["upfile"]["name"]) ? $_FILES["upfile"]["name"] : "";
$upfile = isset($_FILES["upfile"]["tmp_name"]) ? $_FILES["upfile"]["tmp_name"] : "";

// �f���ݒ�-------------------------------------------------------------------

define("ADMIN_PASS", 'admin');		// �Ǘ��҃p�X

define("LOGFILE",  'img.log');		// ���O�t�@�C����
define("TREEFILE", 'tree.log');		// ���O�t�@�C����
define("IMG_DIR",  'src/');		// �摜�ۑ��f�B���N�g��  moepic.php ���猩��
define("THUMB_DIR",'thumb/');		// �T���l�C���ۑ��f�B���N�g��
define("LOG_MAX",  500);			// ���O�ő�s��

define("TITLE", '�摜�f����');		// �^�C�g���i<title>��TOP�j
define("HOME",  '../');			// �u�z�[���v�ւ̃����N
define("MAX_KB", 500);			// ���e�e�ʐ��� KB�iphp�̐ݒ�ɂ��2M�܂�
define("MAX_W",  250);			// ���e�T�C�Y���i����ȏ��width���k��
define("MAX_H",  250);			// ���e�T�C�Y����
define("PAGE_DEF", 5);			// ��y�[�W�ɕ\������L��

define("PHP_SELF",  'moepic.php');	// ���̃X�N���v�g��
define("PHP_SELF2", 'moepic.htm');	// ������t�@�C����
define("PHP_EXT", '.htm');		// 1�y�[�W�ȍ~�̊g���q
define("RENZOKU",   5);			// �A�����e�b��
define("RENZOKU2",  3);			// �摜�A�����e�b��
define("MAX_RES",  30);			// ����sage���X��
define("USE_THUMB", 1);			// �T���l�C�������  ����:1 ���Ȃ�:0
define("PROXY_CHECK", 0);		// proxy�̏����݂𐧌�����  ����:1 ���Ȃ�:0
define("DISP_ID",   0);			// ID��\������  ����:2 ����:1 ���Ȃ�:0
define("BR_CHECK", 15);			// ���s��}������s�� ���Ȃ�:0

define("IDSEED", 'id�̎�');		// id�̎�
define("RESIMG", 0);			// ���X�ɉ摜�� �\��:1 �\��Ȃ�:0

// �J���[�����O by �G���A ------------------------------------------------------

define("BG_COL",   '#FFFFFF');		// �w�i�F
define("TXT_COL",  '#0033AA');		// �����F
define("LINK_COL", '#0000EE');		// �����N�F
define("VLINK_COL",'#0000EE');		// �K��ς݃����N�F
define("ALINK_COL",'#DD0000');		// �I���������̐F

define("TIT_COL",  '#5555FF');		// �f���^�C�g���J���[
define("BASE_COL", '#5555FF');		// �x�[�X�J���[

// ----- ���X�L���֌W ----- //
define("RE_COL",   '#992255');		// �����t�������̐F
define("RE_BGCOL", '#F7F7FE');		// �w�i�J���[
define("SUB_COL",  '#CC1105');		// �^�C�g���J���[
define("NAME_COL", '#117743');		// ���O�J���[

// �e��ǉ��ݒ� by �G���A ------------------------------------------------------

define("MOE_LOG",  'moecount/');	// �G���J�E���g���O�t�H���_
define("MOE_KAKU", '.log');		// �J�E���g���O�g���q
define("MOE_DLOG", 'moeden.log');	// �a�����O
define("MOE_IMG",  'src_d/');	// �a���摜�ۑ��t�H���_
define("MOE_DCNT", 100);		// �a������ɂȂ�J�E���g��
define("MOE_DPG",    5);		// �a���M�������[�P�y�[�W�\����
define("MOE_DNAG",   1);		// �a������薇���ȏ�\�����Ȃ�  y:1 n:0
define("MOE_DNAGM",  5);		// �a���ɕ\�����閇�� (����1�̏ꍇ)

define("MOE_TITLE", '�a���M�������[');	// �a���M�������[�^�C�g���i<title>
define("MOE_TITLE2",'�G���J�E���g�a������摜�M�������[');	// �a���M�������[�^�C�g���iTOP
define("MOE_TLINK", '�G���J�E���g�a���M�������[');	// �a���M�������[�����N���b�Z�[�W

define("DEN_MSG", ':*:��,��ߥ:*:��,�a������,��:*:�߁�,��:*: ');	// �a�����胁�b�Z�[�W
define("MOE_MSG0",'Ӵ��');		// �G�����ݒl�\������ (0�J�E���g)
define("MOE_MSG1",'Ӵ��');		// �G�����ݒl�\������ (1�J�E���g�`MOE_DCNT��20%)
define("MOE_MSG2",'Ӵ��');		// �G�����ݒl�\������ (MOE_DCNT��20%�`40%)
define("MOE_MSG3",'Ӵ��');		// �G�����ݒl�\������ (MOE_DCNT��40%�`60%)
define("MOE_MSG4",'Ӵ��');		// �G�����ݒl�\������ (MOE_DCNT��60%�`80%)
define("MOE_MSG5",'Ӵ��');		// �G�����ݒl�\������ (MOE_DCNT��80%�`100%)

define("MOE_BOT", 1);			// �G���{�^���ɉ摜���g�����ۂ�  y:1 n:0
define("MOE_BOTP", 'moeta.gif');		// �G���{�^���̉摜
define("MOE_BOTT", '(�E�́E)Ӵ�!!!');	// �G���{�^���̕���

define("MOE_IPC", 1);			// ���e��IP�K���������邩�ۂ�  y:1 n:0
// (ver2.07�ȍ~�P�摜�P�J�E���g�K���j
define("MOE_IPO", 0);			// �K���͒��O��IP�݂̂ɂ���  y:1 n:0
// (ver2.07�ȑO�̏�Ԃɖ߂��j

define("NO_TITLE",'����');		// �^�C�g���ȗ����̃^�C�g��
define("NO_COM",  '(�E�́E)ӴӴ');	// �{���ȗ����̖{��
define("NO_NAME", '��������');		// ���O�ȗ����̖��O

define("AUTOLNK", 1);			// �{����URL�̃I�[�g�����N��  y:1 n:0

// --2.08�ǉ�
define("USE_RE_HTM", 1);			//�摜�W�����vHTML�𗘗p���邩�ۂ�  y:1 n:0
define("RE_HTM_DIR", 'redirecthtm/');	//�摜�W�����vHTML�ۑ��f�B���N�g��

// -----------------------------------------------------------------------------

// ���K�\���ɂ��}�b�`
$badstring = array("dummy_string", "dummy_string2"); // ���₷�镶����
$badip = array("..0.0", "addr2.dummy.com"); // ���₷��z�X�g
$badfile = array("dummy", "dummy2"); // ���₷��t�@�C����md5

$addinfo = ''; // ���e�����ӏ����̒ǋL����

$path = realpath("./").'/'.IMG_DIR;
init();		// �����������ݒ��͕s�v�Ȃ̂ō폜����

/* �w�b�_ */
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
[<a href="'.HOME.'" target="_top">�z�[��</a>]
[<a href="'.PHP_SELF.'?mode=admin">�Ǘ��p</a>]
<hr width="90%" size=1>
';
}

/* ���e�t�H�[�� */
function form(&$dat, $resno, $admin="") {
  global $addinfo; $msg=""; $hidden=""; $res="";
  $maxbyte = MAX_KB * 1024;

  if ($resno) {
    $msg .= "[<a href=\"".PHP_SELF2."\">�f���ɖ߂�</a>]\n";
    $msg .= "<table width=\"100%\"><tr><th bgcolor=\"#5555ff\">\n";
    $msg .= "<font color=\"#FFFFFF\">���X���M���[�h</font>\n";
    $msg .= "</th></tr></table>\n";
  }
  if ($admin) {
    $hidden = "\n<input type=\"hidden\" name=\"admin\" value=\"".ADMIN_PASS."\">";
    $res = "\n".'<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">�X��No. </font></b></td>'.
           '<td><input type="text" name="resto" size=14 maxlength=14></td></tr>'.
           "\n".'<tr><td colspan=2><small>���X��No. �Ƀ��X�������� �X���b�hNo. ����͂��邱�Ƃł��̃X���Ƀ��X�ł��܂��B</small></td></tr>';
    $msg = "<ul>\n".
           "<li>�^�O�������܂�\n".
           "<li>�u�Ǘ��l�v�̖��O���g���܂�\n".
           "<li>PROXY�̋K����������܂���\n".
           "</ul>\n";
  }

  $dat .= $msg.'<center>
<form action="'.PHP_SELF.'" method="POST" enctype="multipart/form-data">
<input type="hidden" name="mode" value="regist">'.$hidden.'
<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxbyte.'">
';

  if ($resno) $dat .= "<input type=\"hidden\" name=\"resto\" value=\"".$resno."\">\n";

  $dat .= '<table cellpadding=1 cellspacing=1>'.$res.'
<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">���Ȃ܂�</font></b></td><td><input type="text" name="name" size=28></td></tr>
<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">E-mail</font></b></td><td><input type="text" name="email" size=28></td></tr>
<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">��@�@��</font></b></td><td><input type="text" name="sub" size=35>
<input type="submit" value="���M����"></td></tr>
<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">�R�����g</font></b></td><td><textarea name="com" wrap="soft" cols=48 rows=4></textarea></td></tr>
';

  if (RESIMG || !$resno) {
    $dat .= '<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">�Y�tFile</font></b></td>
<td><input type="file" name="upfile" size=35>
';
    if (!$resno) $dat .= "[<label><input type=\"checkbox\" name=\"textonly\" value=\"on\">�摜�Ȃ�</label>]</td></tr>\n";
}

  $dat .= '<tr><td bgcolor="'.BASE_COL.'"><b><font color="#FFFFFF">�폜�L�[</font></b></td>
<td><input type="password" name="pwd" size=8 maxlength=8 value=""><small>(�L���̍폜�p�B�p������8�����ȓ�)</small></td></tr>
<tr><td colspan=2>
<small>
<li>�Y�t�\�t�@�C���FGIF, JPG, PNG �u���E�U�ɂ���Ă͐���ɓY�t�ł��Ȃ����Ƃ�����܂��B</li>
<li>�ő哊�e�f�[�^�ʂ� '.MAX_KB.' KB �܂łł��Bsage�@�\�t���B</li>
<li>�摜�͉� '.MAX_W.'�s�N�Z���A�c '.MAX_H.'�s�N�Z���𒴂���Ək���\������܂��B</li>
<li>���[ '.MOE_DCNT.' �J�E���g�œa����������܂��B</li>
'.$addinfo.'</small>
</td></tr>
</table>
</form>
</center>
<hr>';
}

/* �L������ */
function updatelog($resno=0) {
  global $path; $p=0;

  $tree = @file(TREEFILE);
  $counttree = count($tree);
  $find = FALSE;
  if ($resno) {
    for ($i = 0; $i < $counttree; $i++) {
      $tline = explode(",", rtrim($tree[$i]));
      if ($resno == $tline[0] || array_search($resno, $tline)) { // ���X�挟��
        $resno = $tline[0];
        $st = $i;
        $find = TRUE;
        break;
      }
    }
    if (!$find) { error("�Y���L�����݂���܂���@No.".$resno); }
  }

  $line = @file(LOGFILE);
  $countline = count($line);
  for ($i = 0; $i < $countline; $i++) {
    list($no,) = explode(",", $line[$i]);
    $lineindex[$no] = $i + 1; // �t�ϊ��e�[�u���쐬
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
    $j = $lineindex[$disptree] - 1; // �Y���L����T����$j�ɃZ�b�g
    if (empty($line[$j])) { continue; } // $j���͈͊O�Ȃ玟�̍s
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pwd,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
    // URL�ƃ��[���Ƀ����N
    if ($email) $name = "<a href=\"mailto:".$email."\">".$name."</a>";
    if (AUTOLNK) $com = auto_link($com);
    $com = eregi_replace("(^|>)(&gt;[^<]*)", "\\1<font color=\"".RE_COL."\">\\2</font>", $com);
    // �摜�t�@�C����
    $img = $path.$time.$ext;
    $src = IMG_DIR.$time.$ext;
    // <img�^�O�쐬
    $imgsrc = "";
    if ($ext && is_file($img)) {
      $size = filesize($img); // alt�ɃT�C�Y�\��

      if (@is_file(RE_HTM_DIR.$time.".htm")) { // �\�������_�C���N�g�����N����ꍇ
        $re_htm = RE_HTM_DIR.$time.".htm";
        if ($w && $h) { // �T�C�Y�����鎞
          if (@is_file(THUMB_DIR.$time.'s.jpg')) {
            $imgsrc = "<small>Redirect:�T���l�C����\�����Ă��܂�.�N���b�N����ƌ��̃T�C�Y��\�����܂�.</small><br>".
                      "<a href=\"".$re_htm."\" target=\"_blank\"><img src=\"".THUMB_DIR.$time.'s.jpg'.
                      "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
          } else {
            $imgsrc = "<small>Redirect</small><a href=\"".$re_htm."\" target=\"_blank\"><img src=\"".$src.
                      "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
          }
        } else { // ����ȊO
          $imgsrc = "<a href=\"".$re_htm."\" target=\"_blank\"><img src=\"".$src.
                    "\" border=0 align=left hspace=20 alt=\"".$size." B\"></a>";
        }
        $dat .= "�摜�^�C�g���F<a href=\"".$re_htm."\" target=\"_blank\">".$time.$ext."</a>-(".$size." B)<br>".$imgsrc;

      } else { // �\�������_�C���N�g�����N���Ȃ��ꍇ

        if ($w && $h) { // �T�C�Y�����鎞
          if (@is_file(THUMB_DIR.$time.'s.jpg')) {
            $imgsrc = "<small>�T���l�C����\�����Ă��܂�.�N���b�N����ƌ��̃T�C�Y��\�����܂�.</small><br>".
                      "<a href=\"".$src."\" target=\"_blank\"><img src=\"".THUMB_DIR.$time.'s.jpg'.
                      "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
          } else {
            $imgsrc = "<a href=\"".$src."\" target=\"_blank\"><img src=\"".$src.
                      "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
          }
        } else { // ����ȊO
          $imgsrc = "<a href=\"".$src."\" target=\"_blank\"><img src=\"".$src.
                    "\" border=0 align=left hspace=20 alt=\"".$size." B\"></a>";
        }
        $dat .= "�摜�^�C�g���F<a href=\"".$src."\" target=\"_blank\">".$time.$ext."</a>-(".$size." B)<br>".$imgsrc;
      }

// �G���J�E���g�V�X�e���@by �G���A ---------------------------------------------

// ���X���O�Ǘ��������Ȃ�܂����P�X���b�h�ɑ΂��P���O���쐬������@��
// ���p���܂��B�z�񌟍����̏������S���v��Ȃ��̂ŕ��ׂ�������Ȃ�����S�ł��B
// �Œ���̏����̕��׋y�у��O�̈��S���̖ʂ���A���O�����̔������𖳎����܂��B

// ���X�ԐM���͕\�����Ȃ�
if (!$resno) {
  // �ϐ��������i�I�[�g�����[�h�΍�
  $mcountlog = $denview = $moeta = NULL;
  $logmoe = MOE_LOG.$time.MOE_KAKU;

  if (!is_file($logmoe)) { // ���O�t�@�C���������Ȃ�쐬
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
  $countmp = count($mp_data); // ���݂̃J�E���g�����擾
//  for ($m = 0; $m < $countmp; $m++) {
//    list($mcountlog,) = explode(",", $mp_data[$m]);
//  }
  list($mcountlog,) = explode(",", $mp_data[$countmp - 1]);

  if ($mcountlog == 'DEN') { // �����a������Ȃ�
    $dat .= "\n<div align=center><b><font color=\"#ff0000\" size=\"+2\">".DEN_MSG."</font></b></div>\n";

    // �a�����O�ǂݍ���
    ignore_user_abort(1);
    $fp = fopen(MOE_DLOG, "r+") or error("ERROR! load denlog");
    set_file_buffer($fp, 0);
    flock($fp, LOCK_EX);
    $buf = fread($fp, 1000000);
    $dendat = explode("\n", $buf);
    array_pop($dendat);
    $countden = count($dendat);

    // ���łɓa�����肵�Ă��邩�ǂ������ׂ�
    $denflag = FALSE;
    for ($m = 0; $m < $countden; $m++) { // �t�@�C������
      list($mno,) = explode(",", $dendat[$m]);
      if ($mno == $no) { $denflag = TRUE; break; }
      $dendat[$m] .= "\n";
    }
    if (!$denflag) { // �a�����O���ɖ����Ȃ�A�ǉ�
      copy($src, MOE_IMG.$time.$ext); // �摜�t�@�C���R�s�[
      // �z��ɓa�����肵���摜�̏����i�[
      $mnew = implode(",", array($no,$now,$name,$time,$ext,$w,$h,$chk,"\n"));
      array_unshift($dendat, $mnew);
      // ���O�X�V
      $remnew = implode('', $dendat);
      rewind($fp);
      fputs($fp, $remnew);
      ftruncate($fp, ftell($fp));
    }
    fclose($fp);
    ignore_user_abort(0);
  } else { // �a�����肶��Ȃ�
    $dat .= "\n<form action=\"".PHP_SELF."\" method=\"POST\">".
            "<input type=\"hidden\" name=\"moeta\" value=\"countup\">".
            "<input type=\"hidden\" name=\"moeno\" value=\"".$no."\">".
            "<input type=\"hidden\" name=\"mcount\" value=\"".$time."\">\n".
            "<div align=center><b>";
    // �{�^���\��
    if (MOE_BOT) {
      $dat .= "<font color=\"#cc1105\" size=\"+1\">���[�F</font><input type=\"image\" src=\"".MOE_BOTP."\" alt=\"\">";
    } else {
      $dat .= "<font color=\"#cc1105\" size=\"+1\">���[�F</font><input type=\"submit\" value=\"".MOE_BOTT."\">";
    }
    // ���݂̃J�E���g���\��
    if ($mcountlog >= MOE_DCNT * 0.8) {
      $dat .= "�@����:<font color=\"#cc1105\" size=\"+5\">".$mcountlog."</font>".MOE_MSG5."</b></div></form>\n";
    } elseif ($mcountlog >= MOE_DCNT * 0.6) {
      $dat .= "�@����:<font color=\"#cc1105\" size=\"+4\">".$mcountlog."</font>".MOE_MSG4."</b></div></form>\n";
    } elseif ($mcountlog >= MOE_DCNT * 0.4) {
      $dat .= "�@����:<font color=\"#cc1105\" size=\"+3\">".$mcountlog."</font>".MOE_MSG3."</b></div></form>\n";
    } elseif ($mcountlog >= MOE_DCNT * 0.2) {
      $dat .= "�@����:<font color=\"#cc1105\" size=\"+2\">".$mcountlog."</font>".MOE_MSG2."</b></div></form>\n";
    } elseif ($mcountlog >= 1) {
      $dat .= "�@����:<font color=\"#cc1105\" size=\"+1\">".$mcountlog."</font>".MOE_MSG1."</b></div></form>\n";
    } else {
      $dat .= "�@����:<font color=\"#117783\" size=\"+1\">0</font>".MOE_MSG0."</b></div></form>\n";
    }
  }
}
// �G���J�E���g�V�X�e���@by �G���A ---------------------------------------------

    } // if ($ext && is_file($img)) �̏I���

    // ���C���쐬
    if ($resno) {
      $dat .= "\n<p><input type=\"checkbox\" name=\"".$no."\" value=\"delete\">";
    }

    $dat .= "<font color=\"".SUB_COL."\" size=\"+1\"><b>".$sub."</b></font> \n";
    $dat .= "Name <font color=\"".NAME_COL."\"><b>".$name."</b></font> ".$now." No.".$no." &nbsp; \n";
    if (!$resno) $dat .= "[<a href=\"".PHP_SELF."?res=".$no."\">�ԐM or �폜</a>]\n";
    $dat .= "<blockquote>".$com."</blockquote>\n";

    // ���낻�������B
    if ($lineindex[$no] - 1 >= LOG_MAX * 0.95) {
      $dat .= "<font color=\"#f00000\"><b>���̃X���͌Â��̂ŁA�������������܂��B</b></font><br>\n";
    }

    // ���X�쐬
    $counttreeline = count($treeline);
    if (!$resno) {
      $s = $counttreeline - 10;
      if ($s < 1) { $s = 1; }
      elseif ($s > 1) {
        $dat .= "<font color=\"#707070\">���X".
                ($s - 1)."���ȗ��B�S�ēǂނɂ͕ԐM�{�^���������Ă��������B</font><br>\n";
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
      // URL�ƃ��[���Ƀ����N
      if ($email) $name = "<a href=\"mailto:".$email."\">".$name."</a>";
      if (AUTOLNK) $com = auto_link($com);
      $com = eregi_replace("(^|>)(&gt;[^<]*)", "\\1<font color=\"".RE_COL."\">\\2</font>", $com);

    // �摜�t�@�C����
    $img = $path.$time.$ext;
    $src = IMG_DIR.$time.$ext;
    // <img�^�O�쐬
    $imgsrc = "";
    if ($ext && is_file($img)) {
      $size = filesize($img); // alt�ɃT�C�Y�\��
      if ($w && $h) { // �T�C�Y�����鎞
        if (@is_file(THUMB_DIR.$time.'s.jpg')) {
          $imgsrc = "<small>�T���l�C���\��</small><br>".
                    "<a href=\"".$src."\" target=\"_blank\"><img src=\"".THUMB_DIR.$time.'s.jpg'.
                    "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
        } else {
          $imgsrc = "<a href=\"".$src."\" target=\"_blank\"><img src=\"".$src.
                    "\" border=0 align=left width=".$w." height=".$h." hspace=20 alt=\"".$size." B\"></a>";
        }
      } else { // ����ȊO
        $imgsrc = "<a href=\"".$src."\" target=\"_blank\"><img src=\"".$src.
                  "\" border=0 align=left hspace=20 alt=\"".$size." B\"></a>";
      }
      $imgsrc = "<br> &nbsp; &nbsp; <a href=\"".$src."\" target=\"_blank\">".$time.$ext."</a>-(".$size." B) ".$imgsrc;
    }

      // ���C���쐬
      $dat .= "<table border=0><tr><td align=right valign=top nowrap>�c</td><td bgcolor=\"".RE_BGCOL."\">\n";

      if ($resno) { // ���X���`�F�b�N�{�b�N�X�\��
        $dat .= "<input type=\"checkbox\" name=\"".$no."\" value=\"delete\">";
      }

      $dat .= "<font color=\"".SUB_COL."\" size=\"+1\"><b>".$sub."</b></font> \n";
      $dat .= "Name <font color=\"".NAME_COL."\"><b>".$name."</b></font> ".$now." No.".$no." &nbsp; \n";
      $dat .= $imgsrc."<blockquote>".$com."</blockquote>";
      $dat .= "</td></tr></table>\n";
    }
    $dat .= "<br clear=left><hr>\n";
    clearstatcache(); // �t�@�C����stat���N���A
    $p++;
    if ($resno) { break; } // res����tree1�s����
  }

  if ($resno) { // ���X���ɕ\��
    $dat .= '<table align=right><tr><td align=center nowrap><input type="hidden" name="mode" value="usrdel">
�y�L���폜�z[<input type="checkbox" name="onlyimgdel" value="on">�摜��������]<br>
�폜�L�[<input type="password" name="pwd" size=8 maxlength=8 value="">
<input type="submit" value="�폜"></form></td></tr></table>
';
  }

    // �e�y�[�W�ւ̃����N�p�e�[�u��
    if (!$resno) { // res���͕\�����Ȃ�
      $prev = $st - PAGE_DEF;
      $next = $st + PAGE_DEF;
      // ���y�[�W����
      $dat .= '<table align=center border=1><tr>';
      if ($prev >= 0) {
        if ($prev == 0) {
          $dat .= '<form action="'.PHP_SELF2.'" method="GET">';
        } else {
          $dat .= '<form action="'.$prev / PAGE_DEF.PHP_EXT.'" method="GET">';
        }
        $dat .= '<td><input type="submit" value="�O�̃y�[�W"></td></form>';
      } else {
        $dat .= '<td>�ŏ��̃y�[�W</td>';
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
        $dat .= '<input type="submit" value="���̃y�[�W"></td></form>';
      } else {
        $dat .= '<td>�Ō�̃y�[�W</td>';
      }
      $dat .= "</tr></table><br clear=all>\n";
    }

    foot($dat);
    if ($resno) { echo $dat; break; } // ���X���͈�s�̂�

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
  if (!$resno && is_file(($page / PAGE_DEF + 1).PHP_EXT)) { unlink(($page / PAGE_DEF + 1).PHP_EXT); } // �O�y�[�W�폜
}

/* �t�b�^ */
function foot(&$dat) {
  $dat .= '
<div align=center>
<small><!-- GazouBBS v3.0 --><!-- �ӂ��Ή�0.8 --><!-- �G���A2.08 -->
- <a href="http://php.s3.to" target="_top">GazouBBS</a> + <a href="http://www.2chan.net/" target="_top">futaba</a> + <a href="http://moepic.dip.jp/gazo/" target="_top">moeren</a> -
</small>
</div>
</body></html>';
}

/* �I�[�g�����N */
function auto_link($proto) {
  $proto = ereg_replace("(https?|ftp|news)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", "<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>", $proto);
  return $proto;
}

/* �G���[��� */
function error($mes, $dest='', $flag=FALSE) {
  global $upfile_name, $path;
  if (is_file($dest)) unlink($dest);
  if (!$flag) head($dat);
  else $dat = "</form>\n";
  echo $dat;
  echo "<br><br><hr size=1><br><br>\n",
       "<div align=center><font color=\"red\" size=5><b>",$mes,"<br><br><a href=\"",PHP_SELF2,"\">�����[�h</a></b></font></div>\n",
       "<br><br><hr size=1>\n";
  die("</body></html>");
}

/* �v���N�V�ڑ��`�F�b�N */
function proxy_connect($port) {
  $a=""; $b="";
  $fp = @fsockopen($_SERVER["REMOTE_ADDR"], $port, $a, $b, 2);
  if (!$fp) { return 0; } else { return 1; }
}

/* �L���������� */
function regist($name,$email,$sub,$com,$url,$pwd,$upfile,$upfile_name,$resto) {
  global $path,$badstring,$badfile,$badip,$pwdc,$textonly,$admin;
  $dest=""; $mes="";

  if ($_SERVER["REQUEST_METHOD"] != "POST") { error("�s���ȓ��e�����Ȃ��ŉ�����(post)"); }

  // ����
  $time = time();
  $tim = $time.substr(microtime(), 2, 3);

  // �A�b�v���[�h����
  if ($upfile && file_exists($upfile)) {
    $dest = $path.$tim.'.tmp';
    move_uploaded_file($upfile, $dest);
    // ���ŃG���[�Ȃ火�ɕύX
    //copy($upfile, $dest);
    $upfile_name = CleanStr($upfile_name);
    if (!is_file($dest)) { error("�A�b�v���[�h�Ɏ��s���܂���<br>�T�[�o���T�|�[�g���Ă��Ȃ��\��������܂�", $dest); }
    $size = getimagesize($dest);
    if (!is_array($size)) { error("�A�b�v���[�h�Ɏ��s���܂���<br>�摜�t�@�C���ȊO�͎󂯕t���܂���", $dest); }
    $chk = md5_of_file($dest);
    foreach ($badfile as $value) {
      if (ereg("^$value", $chk)) {
        error("�A�b�v���[�h�Ɏ��s���܂���<br>�֎~�摜�ł�", $dest); // ����摜
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
      default : $ext = ".xxx"; error("�Ή����Ȃ��t�H�[�}�b�g�ł��B", $dest);
    }
    if ($ext == '.bmp') {
      error("�A�b�v���[�h�Ɏ��s���܂���<br>BMP�`���̓T�|�[�g���Ă��܂���", $dest);
    }

    // �摜�\���k��
    if ($W > MAX_W || $H > MAX_H) {
      $W2 = MAX_W / $W;
      $H2 = MAX_H / $H;
      ($W2 < $H2) ? $key = $W2 : $key = $H2;
      $W = ceil($W * $key);
      $H = ceil($H * $key);
    }
    $mes = "�摜 $upfile_name �̃A�b�v���[�h���������܂���<br><br>";
  }

  // ���₷�镶����
  foreach ($badstring as $value) {
    if(ereg($value, $com)
    || ereg($value, $sub)
    || ereg($value, $name)
    || ereg($value, $email)
    ) {
      error("���₳��܂���(str)", $dest);
    };
  }

  // �t�H�[�����e���`�F�b�N
  if (!$name || ereg("^[ |�@|]*$", $name)) $name = "";
  if (!$com || ereg("^[ |�@|\t]*$", $com)) $com = "";
  if (!$sub || ereg("^[ |�@|]*$", $sub))   $sub = "";

  if (!$resto && !$textonly && !is_file($dest)) { error("�摜������܂���", $dest); }
  if (!$com && !is_file($dest)) { error("���������ĉ�����", $dest); }

  if ($admin != ADMIN_PASS) { // �Ǘ��l�̏ꍇ�͂��̂܂�
    $name = str_replace("�Ǘ�", "\"�Ǘ�\"", $name);
    $name = str_replace("�폜", "\"�폜\"", $name);
  }

  if (strlen($com) > 1000)  { error("�{�����������܂����I", $dest); }
  if (strlen($name) > 100)  { error("���O���������܂����I", $dest); }
  if (strlen($email) > 100) { error("���[�����������܂����I", $dest); }
  if (strlen($sub) > 100)   { error("�薼���������܂����I", $dest); }
  if (strlen($resto) > 10)  { error("���X�ԍ����ُ�ł�", $dest); }
  if (strlen($url) > 10)    { error("�ُ�ł�", $dest); }

  // �z�X�g�擾
  $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);

  // ����host
  foreach ($badip as $value) {
    if (eregi("$value$", $host)) {
      error("���₳��܂���(host)", $dest);
  } }

  // �v���N�V�`�F�b�N
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
    // �v���N�V�̋^��������ꍇ
    if ($pxck == "on") {
      if (proxy_connect('80') == 1) {
        error("�d�q�q�n�q�I�@���J�o�q�n�w�x�K�����I�I(80)", $dest);
      } elseif (proxy_connect('8080') == 1) {
        error("�d�q�q�n�q�I�@���J�o�q�n�w�x�K�����I�I(8080)", $dest);
      }
    }
  }

  // No.�ƃp�X�Ǝ��Ԃ�URL�t�H�[�}�b�g
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
  $youbi = array('��', '��', '��', '��', '��', '��', '�y');
  $yd = $youbi[gmdate("w", $time + 9*60*60)];
  $now = gmdate("y/m/d", $time + 9*60*60)."(".(string)$yd.")".gmdate("H:i", $time + 9*60*60);
  // ID�\��
  if (DISP_ID) {
    if ($email && DISP_ID == 1) {
      $now .= " ID:???";
    } else {
      $now .= " ID:".substr(crypt(md5($_SERVER["REMOTE_ADDR"].IDSEED.gmdate("Ymd", $time + 9*60*60)), 'id'), -8);
    }
  }
  // �e�L�X�g���`
  $email= CleanStr($email);  $email = ereg_replace("[\r\n]", "", $email);
  $sub  = CleanStr($sub);    $sub   = ereg_replace("[\r\n]", "", $sub);
  $url  = CleanStr($url);    $url   = ereg_replace("[\r\n]", "", $url);
  $resto= CleanStr($resto);  $resto = ereg_replace("[\r\n]", "", $resto);
  $com  = CleanStr($com);
  // ���s�����̓���
  $com = str_replace("\r\n", "\n", $com);
  $com = str_replace("\r", "\n", $com);
  // �A�������s����s
  $com = ereg_replace("\n((�@| )*\n){3,}", "\n", $com);
  if (!BR_CHECK || substr_count($com, "\n") < BR_CHECK) {
    $com = nl2br($com); // ���s�����̑O��<br>��������
  }
  $com = str_replace("\n", "", $com); // \n�𕶎��񂩂����

  $name = str_replace("��", "��", $name);
  $name = ereg_replace("[\r\n]", "", $name);
  $names = $name;
  $name = CleanStr($name);
  // �g���b�v
  if (ereg("(#|��)(.*)", $names, $regs)) {
    $cap = $regs[2];
    $cap = strtr($cap, "&amp;", "&");
    $cap = strtr($cap, "&#44;", ",");
    $name = ereg_replace("(#|��)(.*)", "", $name);
    $salt = substr($cap."H.", 1, 2);
    $salt = ereg_replace("[^\.-z]", ".", $salt);
    $salt = strtr($salt, ":;<=>?@[\\]^_`", "ABCDEFGabcdef");
    $name .= "</b>��".substr(crypt($cap, $salt), -10)."<b>";
  }

  if (!$name) $name = NO_NAME;
  if (!$com)  $com  = NO_COM;
  if (!$sub)  $sub  = NO_TITLE;

  // ���O�ǂݍ���
  ignore_user_abort(1);
  $fp = fopen(LOGFILE, "r+") or error("ERROR! load log", $dest);
  set_file_buffer($fp, 0);
  flock($fp, LOCK_EX);
//  rewind($fp);
  $buf = fread($fp, 1000000);
  if ($buf == '') { error("error load log", $dest); }
  $line = explode("\n", $buf);
  $countline = count($line) - 1; // \n�𐔂��邽��
  for ($i = 0; $i < $countline; $i++) {
//    if (empty($line[$i])) { continue; }
    list($artno,) = explode(",", rtrim($line[$i])); // �t�ϊ��e�[�u���쐬
    $lineindex[$artno] = $i + 1;
    $line[$i] .= "\n";
  }

  // ��d���e�`�F�b�N
  $imax = ($countline > 20) ? 20 : $countline;
  for ($i = 0; $i < $imax; $i++) {
//    if (empty($line[$i])) { continue; }
    list($lastno,,$lname,,,$lcom,,$lhost,$lpwd,,,,$ltime,) = explode(",", $line[$i]);
    if (strlen($ltime) > 10) { $ltime = substr($ltime, 0, -3); }
    if ( ($host == $lhost) || (substr(md5($pwd), 2, 8) == $lpwd) || (substr(md5($pwdc), 2, 8) == $lpwd) ) { $pchk = 1; } else { $pchk = 0; }
    if (RENZOKU && $pchk && $time - $ltime < RENZOKU)
      error("�A�����e�͂������΂炭���Ԃ�u���Ă��炨�肢�v���܂�", $dest);
    if (RENZOKU2 && $pchk && $time - $ltime < RENZOKU2 && $upfile_name)
      error("�摜�A�����e�͂������΂炭���Ԃ�u���Ă��炨�肢�v���܂�", $dest);
    if (RENZOKU && $pchk && $com == $lcom && !$upfile_name)
      error("�{�����O��̓��e���e�Ɠ����ł�", $dest);
  }
  // ���O�s���I�[�o�[
  if ($countline > LOG_MAX) {
    for ($d = $countline - 1; $d >= LOG_MAX - 1; $d--) {
//      if (empty($line[$d])) { continue; }
      list($dno,,,,,,,,,$dext,,,$dtime,) = explode(",", $line[$d]);
      if (is_file($path.$dtime.$dext)) unlink($path.$dtime.$dext);
      if (is_file(THUMB_DIR.$dtime.'s.jpg')) unlink(THUMB_DIR.$dtime.'s.jpg');
// �G���J�E���g���O�I�[�o�[�폜 -------------

$delmoecount = MOE_LOG.$dtime.MOE_KAKU;
if (is_file($delmoecount)) unlink($delmoecount);

// ���_�C���N�g�t�@�C�����O�I�[�o�[�폜 -----

$delrehtm = RE_HTM_DIR.$dtime.".htm";
if (is_file($delrehtm)) unlink($delrehtm);

// ------------------------------------------
      $line[$d] = "";
      treedel($dno);
    }
  }
  // �A�b�v���[�h����
  if ($dest && file_exists($dest)) {
    $imax = ($countline > 200) ? 200 : $countline;
    for ($i = 0; $i < $imax; $i++) { // �摜�d���`�F�b�N
//      if (empty($line[$i])) { continue; }
      list(,,,,,,,,,$extp,,,$timep,$chkp,) = explode(",", $line[$i]);
      if ($chkp == $chk && file_exists($path.$timep.$extp)) {
        error("�A�b�v���[�h�Ɏ��s���܂���<br>�����摜������܂�", $dest);
    } }
  }

  if (!$resto && !$textonly) {
    // �G���J�E���g��t�@�C�������X�ȊO�ŉ摜���L��ꍇ�̂ݍ쐬�@by �G���A
    $logmoe = MOE_LOG.$tim.MOE_KAKU;
    $mfp = fopen($logmoe, "w");
    flock($mfp, LOCK_EX);
    set_file_buffer($mfp, 0);
    fputs($mfp, "0,0.0.0.0\n");
    fclose($mfp);
    chmod($logmoe, 0666);
    // �摜�ւ̃��_�C���N�g�pHTML�̍쐬�@by �G���A
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

  // �c���[�X�V
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
  $countline = count($line) - 1; // \n�𐔂��邽��
  for ($i = 0; $i < $countline; $i++) {
//    if (empty($line[$i])) { continue; }
    $line[$i] .= "\n";

    $j = explode(",", rtrim($line[$i]));
    if (is_null($lineindex[$j[0]])) {
      $line[$i] = ''; // ���O�ɖ�����΁A��ɂ���
    }

  }
  if ($resto) { // ���X�ԍ����w�肳��Ă���ꍇ
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
    else { error("�X���b�h������܂���", $dest); }
  }
  $newline .= implode('', $line);
//  set_file_buffer($tp, 0);
//  ftruncate($tp, 0);
  rewind($tp);
  fputs($tp, $newline);
  ftruncate($tp, ftell($tp));
  flock($tp, LOCK_UN);
  fclose($tp);

  // �N�b�L�[�ۑ�
  setcookie("pwdc", $c_pass, time() + 7*24*3600); /* 1�T�ԂŊ����؂� */
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
      setcookie("namec", $c_name, time() + 7*24*3600); /* 1�T�ԂŊ����؂� */
    }
  }

  if ($dest && file_exists($dest)) { // �摜�t�@�C������
    rename($dest,$path.$tim.$ext);
    if (USE_THUMB) { thumb($path,$tim,$ext); }
  }
  updatelog();

  ignore_user_abort(0);

  echo "<html><head><meta http-equiv=\"refresh\" content=\"1;URL=",PHP_SELF2,"\"></head>",
       "<body>",$mes," ��ʂ�؂�ւ��܂�</body></html>";
}

/* �T���l�C���쐬 */
function thumb($path, $tim, $ext) {
  if (!function_exists("ImageCreate") || !function_exists("ImageCreateFromJPEG")) { return; }
  $fname = $path.$tim.$ext; // �t�@�C����
  $thumb_dir = THUMB_DIR;   // �T���l�C���ۑ��f�B���N�g��
  $width     = MAX_W;       // �o�͉摜��
  $height    = MAX_H;       // �o�͉摜����
  // �摜�̕��ƍ����ƃ^�C�v���擾
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
  // ���T�C�Y
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
  // �o�͉摜�i�T���l�C���j�̃C���[�W���쐬   ���摜���c���Ƃ� �R�s�[
  if (function_exists("ImageCreateTrueColor") && get_gd_ver() == "2") {
    $im_out = ImageCreateTrueColor($out_w, $out_h);
    ImageCopyResampled($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  } else {
    $im_out = ImageCreate($out_w, $out_h);
    ImageCopyResized($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  }
  // �T���l�C���摜��ۑ�
  ImageJPEG($im_out, $thumb_dir.$tim.'s.jpg', 60);
  chmod($thumb_dir.$tim.'s.jpg', 0666);
  // �쐬�����C���[�W��j��
  ImageDestroy($im_in);
  ImageDestroy($im_out);
}

/* gd�̃o�[�W�����𒲂ׂ� */
function get_gd_ver() {
  if (function_exists("gd_info")) {
    $gdver = gd_info();
    $phpinfo = $gdver["GD Version"];
  } else { // php4.3.0�����p
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

/* �t�@�C��md5�v�Z php4.2.0�����p */
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

/* �c���[�폜 */
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

/* �e�L�X�g���` */
function CleanStr($str) {
  global $admin;

  $str = trim($str); // �擪�Ɩ����̋󔒏���
  if (get_magic_quotes_gpc()) { // �����폜
    $str = stripslashes($str);
  }
  if ($admin != ADMIN_PASS) { // �Ǘ��҂̓^�O�\
    $str = htmlspecialchars($str); // �^�O���֎~
    $str = str_replace("&amp;", "&", $str); // ���ꕶ��
  }
  return str_replace(",", "&#44;", $str); // �J���}��ϊ�
}

/* ���[�U�[�폜 */
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
      $line[$i] = ""; // �p�X���[�h���}�b�`�����s�͋��
      $delfile = $path.$dtim.$dext; // �폜�t�@�C��
      if (!$onlyimgdel) {
        treedel($dno);
      }
      if (is_file($delfile)) unlink($delfile); // �폜
      if (is_file(THUMB_DIR.$dtim.'s.jpg')) unlink(THUMB_DIR.$dtim.'s.jpg'); // �폜
// �G���J�E���g���O���[�U�[�폜 -------------

$delmoecount = MOE_LOG.$dtim.MOE_KAKU;
if (is_file($delmoecount)) unlink($delmoecount);

// ���_�C���N�g�t�@�C�����O���[�U�[�폜 -----

$delrehtm = RE_HTM_DIR.$dtim.".htm";
if (is_file($delrehtm)) unlink($delrehtm);

// ------------------------------------------
    }
  }
  if (!$flag) { error("�Y���L����������Ȃ����p�X���[�h���Ԉ���Ă��܂�"); }
}

/* �p�X�F�� */
function valid($pass) {
  if ($pass && $pass != ADMIN_PASS) error("�p�X���[�h���Ⴂ�܂�");

  head($dat);
  echo $dat,
       "[<a href=\"",PHP_SELF2,"\">�f���ɖ߂�</a>]\n",
       "[<a href=\"",PHP_SELF,"\">���O���X�V����</a>]\n",
       "<table width=\"100%\"><tr><th bgcolor=\"",BASE_COL,"\">\n",
       "<font color=\"#FFFFFF\">�Ǘ����[�h</font>\n",
       "</th></tr></table>\n",
       "<form action=\"",PHP_SELF,"\" method=\"POST\">\n";
  // ���O�C���t�H�[��
  if (!$pass) {
    echo <<<__EOD__
<div align=center>
<table border=0><tr><td>
<input type="radio" name="admin" value="del" checked>�L���폜<br>
<input type="radio" name="admin" value="post">�Ǘ��l���e<br>
<input type="radio" name="admin" value="moecount">�G���J�E���g�Ǘ�<br>
<input type="radio" name="admin" value="moeden">�a���M�������[�Ǘ�<br>
<input type="hidden" name="mode" value="admin">
</td></tr></table>
<input type="password" name="pass" size=8>
<input type="submit" value=" �F�� ">
</div>
</form>\n
__EOD__;
    die("</body></html>");
  }
}

/* �Ǘ��ҍ폜 */
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
    $countline = count($line) - 1; // \n�𐔂��邽��

    for ($i = 0; $i < $countline; $i++) {
//      if (empty($line[$i])) { continue; }
      $line[$i] .= "\n";
    }
    $find = FALSE;
    for ($i = 0; $i < $countline; $i++) {
      list($no,,,,,,,,,$ext,,,$tim,) = explode(",", $line[$i]);
      if ($onlyimgdel == "on") {
        if (array_search($no, $delno)) { // �摜�����폜
          $delfile = $path.$tim.$ext; // �폜�t�@�C��
          if (is_file($delfile)) unlink($delfile); // �폜
          if (is_file(THUMB_DIR.$tim.'s.jpg')) unlink(THUMB_DIR.$tim.'s.jpg'); // �폜
// �G���J�E���g�Ǘ��l�폜 -------------------

$delmoecount = MOE_LOG.$tim.MOE_KAKU;
if (is_file($delmoecount)) unlink($delmoecount);

// ���_�C���N�g�t�@�C�����O�Ǘ��l�폜 -------

$delrehtm = RE_HTM_DIR.$tim.".htm";
if (is_file($delrehtm)) unlink($delrehtm);

// ------------------------------------------
        }
      } else {
        if (array_search($no, $delno)) { // �폜�̎��͋��
          $find = TRUE;
          $line[$i] = "";
          $delfile = $path.$tim.$ext; // �폜�t�@�C��
          if (is_file($delfile)) unlink($delfile); // �폜
          if (is_file(THUMB_DIR.$tim.'s.jpg')) unlink(THUMB_DIR.$tim.'s.jpg'); // �폜
// �G���J�E���g�Ǘ��l�폜 -------------------

$delmoecount = MOE_LOG.$tim.MOE_KAKU;
if (is_file($delmoecount)) unlink($delmoecount);

// ���_�C���N�g�t�@�C�����O�Ǘ��l�폜 -------

$delrehtm = RE_HTM_DIR.$tim.".htm";
if (is_file($delrehtm)) unlink($delrehtm);

// ------------------------------------------
          treedel($no);
        }
      }
    }
    if ($find) { // ���O�X�V
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

  // �폜��ʂ�\��
  echo <<<__EOD__
<input type="hidden" name="mode" value="admin">
<input type="hidden" name="admin" value="del">
<input type="hidden" name="pass" value="$pass">
<div align=center>
�E�폜�������L���̃`�F�b�N�{�b�N�X�Ƀ`�F�b�N�����A�폜�{�^���������ĉ������B
<p><input type="submit" value="�폜����"><input type="reset" value="���Z�b�g">
[<input type="checkbox" name="onlyimgdel" value="on">�摜��������]</p>\n
__EOD__;

  echo "<table border=1 cellspacing=0>\n",
       "<tr bgcolor=\"#6080f6\"><th>�폜</th><th>�L��No</th><th>���e��</th><th>�薼</th>",
       "<th>���e��</th><th>�R�����g</th><th>�z�X�g��</th><th>�Y�t<br>(Bytes)</th><th>md5</th></tr>\n";

  // ���O�t�@�C���ǂݍ���
  $line = @file(LOGFILE);
  $countline = count($line);
  $all = 0;
  for ($j = 0; $j < $countline; $j++) {
    $img_flag = FALSE;
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pw,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
    // �t�H�[�}�b�g
    $now = ereg_replace('.{2}/(.*)$', '\1', $now);
    $now = ereg_replace('\(.*\)', ' ', $now);
    if (strlen($name) > 10) $name = substr($name, 0, 9).".";
    if (strlen($sub) > 10)  $sub  = substr($sub,  0, 9).".";
    if ($email) $name = "<a href=\"mailto:".$email."\">".$name."</a>";
    $com = str_replace("<br />", " ", $com);
    $com = htmlspecialchars($com);
    if (strlen($com) > 20) $com = substr($com, 0, 18).".";
    // �摜������Ƃ��̓����N
    if ($ext && is_file($path.$time.$ext)) {
      $img_flag = TRUE;
      $clip = "<a href=\"".IMG_DIR.$time.$ext."\" target=\"_blank\">".$time.$ext."</a><br>";
      $size = filesize($path.$time.$ext);
      $chk  = substr($chk, 0, 10);
      $all += $size; // ���v�v�Z
    } else {
      $clip = "";
      $chk  = "";
      $size = 0;
    }
    $bg = ($j % 2) ? "#d6d6f6" : "#f6f6f6"; // �w�i�F

    echo "<tr bgcolor=\"",$bg,"\">\n",
         "<th><input type=\"checkbox\" name=\"",$no,"\" value=\"delete\"></th>",
         "<th>",$no,"</th><td><small>",$now,"</small></td><td>",$sub,"</td>",
         "<td><b>",$name,"</b></td><td><small>",$com,"</small></td><td>",$host,"</td>",
         "<td align=\"center\">",$clip,"(",$size,")</td><td>",$chk,"</td>\n</tr>\n";
  }
  $all = (int)($all / 1024);

  echo "</table>\n<p><input type=\"submit\" value=\"�폜����\">",
       "<input type=\"reset\" value=\"���Z�b�g\"></p>\n</div>\n</form>\n",
       "<div align=center>�y �摜�f�[�^���v : <b>",$all,"</b> KB �z</div>\n";
  die("</body></html>");
}

/* �����ݒ� */
function init() {
  $err = "";
  $chkfile = array(LOGFILE, TREEFILE, MOE_DLOG);
  if (!is_writable(realpath("./"))) error("�J�����g�f�B���N�g���ɏ����܂���<br>");
  foreach ($chkfile as $value) {
    if (!file_exists(realpath($value))) {
      $fp = fopen($value, "w");
      set_file_buffer($fp, 0);
      if ($value == LOGFILE)  fputs($fp, "1,2002/01/01(��)00:00,������,,����,�{���Ȃ�,,,,,,,1009810800,,\n");
      if ($value == TREEFILE) fputs($fp, "1\n");
      if ($value == MOE_DLOG) fputs($fp, "");
      fclose($fp);
      if (file_exists(realpath($value))) @chmod($value, 0666);
    }
    if (!is_writable(realpath($value))) $err .= $value."�������܂���<br>";
    if (!is_readable(realpath($value))) $err .= $value."��ǂ߂܂���<br>";
  }
  @mkdir(MOE_LOG, 0777); @chmod(MOE_LOG, 0777);
  if (!is_dir(realpath(MOE_LOG))) $err .= MOE_LOG."������܂���<br>";
  if (!is_writable(realpath(MOE_LOG))) $err .= MOE_LOG."�������܂���<br>";
  if (!is_readable(realpath(MOE_LOG))) $err .= MOE_LOG."��ǂ߂܂���<br>";

  @mkdir(RE_HTM_DIR, 0777); @chmod(RE_HTM_DIR, 0777);
  if (!is_dir(realpath(RE_HTM_DIR))) $err .= RE_HTM_DIR."������܂���<br>";
  if (!is_writable(realpath(RE_HTM_DIR))) $err .= RE_HTM_DIR."�������܂���<br>";
  if (!is_readable(realpath(RE_HTM_DIR))) $err .= RE_HTM_DIR."��ǂ߂܂���<br>";

  @mkdir(MOE_IMG, 0777); @chmod(MOE_IMG, 0777);
  if (!is_dir(realpath(MOE_IMG))) $err .= MOE_IMG."������܂���<br>";
  if (!is_writable(realpath(MOE_IMG))) $err .= MOE_IMG."�������܂���<br>";
  if (!is_readable(realpath(MOE_IMG))) $err .= MOE_IMG."��ǂ߂܂���<br>";

  @mkdir(IMG_DIR, 0777); @chmod(IMG_DIR, 0777);
  if (!is_dir(realpath(IMG_DIR))) $err .= IMG_DIR."������܂���<br>";
  if (!is_writable(realpath(IMG_DIR))) $err .= IMG_DIR."�������܂���<br>";
  if (!is_readable(realpath(IMG_DIR))) $err .= IMG_DIR."��ǂ߂܂���<br>";

  if (USE_THUMB) {
    @mkdir(THUMB_DIR, 0777); @chmod(THUMB_DIR, 0777);
    if (!is_dir(realpath(THUMB_DIR))) $err .= THUMB_DIR."������܂���<br>";
    if (!is_writable(realpath(THUMB_DIR))) $err .= THUMB_DIR."�������܂���<br>";
    if (!is_readable(realpath(THUMB_DIR))) $err .= THUMB_DIR."��ǂ߂܂���<br>";
  }
  if ($err) error($err);
}

/* �a���M�������[�Ǘ���� by �G���A */
function adminden($pass) {

  $selfpath = PHP_SELF;
  echo "</form>\n";

  // �\������������ꍇ
  if (MOE_DNAG) {
    echo "<div align=center><table border=0><tr><td><ul>\n",
         "<li><small>�M�������[�ɂ͍ŐV��",MOE_DNAGM,"���̂݉摜��\�����Ă��܂��B</small>\n",
         "<li><small>",MOE_DNAGM,"���ȍ~�\���͂���Ă��܂��񂪉摜�͑��݂��܂��B</small>\n",
         "</ul></td></tr></table></div>\n";
  }

  echo <<<__TMP__
<div align=center>
<table border=0 cellspacing=1>
<tr bgcolor="#6080f6">
<th>No</th><th>���e��</th><th>���e��</th><th>�摜</th><th>����</th>
</tr>\n
__TMP__;

  // �a�����O�ǂݍ���
  $mdlog = @file(MOE_DLOG);
  $countmd = count($mdlog);

  $all = 0;
  for ($i = 0; $i < $countmd; $i++) {
    $img_flag = FALSE;
    list($no,$now,$name,$time,$ext,$w,$h,$chk) = explode(",", $mdlog[$i]);

    // �摜�T�C�Y����
    $aw = ceil($w / 3);
    $ah = ceil($h / 3);

    $src = MOE_IMG.$time.$ext;
    // �摜������Ƃ��̓����N
    if ($ext && is_file($src)) {
      $img_flag = TRUE;
      $size = filesize($src);
      $all += $size; // ���v�v�Z
      $clip = "<a href=\"".$src."\" target=\"_blank\">".
              "<img src=\"".$src."\" border=0 width=".$aw." height=".$ah." alt=\"".$size." B\"></a>";
    } else {
      $size = 0;
      $clip = "�摜����";
    }
    $bg = "#f6f6f6"; $bg2 = "#d6d6f6"; // �w�i�F

    // �L���I����ʂ�\��
    echo <<<__TMP__
<tr bgcolor="$bg">
<th bgcolor="$bg2">$no</th><td align=center>$now</td><td align=center><b>$name</b></td>
<td align=center>$clip</td>
<td><form action="$selfpath" method="POST">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="pass" value="$pass">
<input type="hidden" name="edenadmin" value="$time">
<input type="submit" value=" �폜 ">
</form></td>
</tr>\n
__TMP__;

  }
  $all = (int)($all / 1024);

  echo <<<__TMP__
</table><br>
�y �摜�f�[�^���v : <b>$all</b> KB �z</div>\n
__TMP__;
  die("</body></html>");
}

/* �G���a���ҏW by �G���A */
function editden($pass) {
  global $edenadmin;

  // �a�����O�ǂݍ���
  $fp = fopen(MOE_DLOG, "r+") or error("ERROR! moeden log!", '', TRUE);
  set_file_buffer($fp, 0);
  flock($fp, LOCK_EX);
  $buf = fread($fp, 1000000);
  if ($buf == '') { error("error admin renew", '', TRUE); }
  $mdlog = explode("\n", $buf);
  $countmd = count($mdlog) - 1;

  // ���O����f�[�^���폜
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
    error("�폜�Ώۂ�������܂���ł���", '', TRUE);
  }

  // �a������摜�폜
  $ddel = MOE_IMG.$edenadmin.$dext;
  if (is_file($ddel)) unlink($ddel);

  // �J�E���g���O�C��
  $logmoe = MOE_LOG.$edenadmin.MOE_KAKU;
  if (is_file($logmoe)) {
    $fp = fopen($logmoe, "w");
    flock($fp, LOCK_EX);
    set_file_buffer($fp, 0);
    fputs($fp, "0,0.0.0.0\n");
    fclose($fp);
    chmod($logmoe, 0666);
  }

  // �A�b�v�f�[�g
  updatelog();

  // ���ʂ�\��
  echo <<<__TMP__
<br><br><hr size=1><br><br>
<div align=center>
<font color="red" size=5><b>�a������폜���܂����@No.{$dno}</b></font><p>
<input type="hidden" name="mode" value="admin">
<input type="hidden" name="admin" value="moeden">
<input type="hidden" name="pass" value="$pass">
<input type="submit" value=" �Ǘ���ʂɖ߂� ">
</div>
</form><hr size=1>\n
__TMP__;
  die("</body></html>");
}

/* �G���J�E���g�Ǘ���� by �G���A */
function adminmoe($pass) {
  global $path;

  if (is_file(MOE_BOTP)) {
    $meter = "<th>Ұ��<BR><img src=\"".MOE_BOTP."\" alt=\"\"></th>";
    $mflag = TRUE;
  } else {
    $meter = "";
    $mflag = FALSE;
  }

  echo <<<__TMP__
</form>
<ul><li>���X�g�J�E���gIP�ɃJ�[�\�����悹��ƑS�J�E���gIP���O���\������܂��B</li></ul>
<div align=center>
<table border=1 cellspacing=0>
<tr bgcolor="#6080f6">
<th>No</th><th>���e��</th><th>�薼</th><th>���e��</th><th>�R�����g</th>
<th>׽Ķ���IP</th><th>�Y�t<br>(Bytes)</th>$meter<th>����</th>
</tr>\n
__TMP__;

  // ���O�t�@�C���ǂݍ���
  $line = @file(LOGFILE);
  $countline = count($line);
  $all = 0; $bgcol = 0;
  for ($j = 0; $j < $countline; $j++) {
    $img_flag = FALSE; $datip = "";
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pw,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);

    // �J�E���g���O������Ε\��
    $logmoe = MOE_LOG.$time.MOE_KAKU;
    if ($ext && is_file($path.$time.$ext) && is_file($logmoe)) {

      // �t�H�[�}�b�g
      $now = ereg_replace('.{2}/(.*)$', '\1', $now);
      $now = ereg_replace('\(.*\)', ' ', $now);
      if (strlen($name) > 10) $name = substr($name, 0, 9).".";
      if (strlen($sub) > 10)  $sub  = substr($sub,  0, 9).".";
      if ($email) $name = "<a href=\"mailto:".$email."\">".$name."</a>";
      $com = str_replace("<br />", " ", $com);
      $com = htmlspecialchars($com);
      if (strlen($com) > 20) $com = substr($com, 0, 18).".";
      // �摜������Ƃ��̓����N
      if ($ext && is_file($path.$time.$ext)) {
        $img_flag = TRUE;
        $clip = "<a href=\"".IMG_DIR.$time.$ext."\" target=\"_blank\">".$time.$ext."</a><br>";
        $size = filesize($path.$time.$ext);
        $all += $size; // ���v�v�Z
      } else {
        $clip = "";
        $size = 0;
      }
      $bg = ($bgcol++ % 2) ? "#d6d6f6" : "#f6f6f6"; // �w�i�F

      // �J�E���g���O�ǂݍ���
      $mp_data = file($logmoe);
      $countmp = count($mp_data);

      for ($i = 0; $i < $countmp; $i++) {
        list($mcountlog, $mcountip) = explode(",", $mp_data[$i]);
        $mcountip = trim($mcountip);
        $datip .= "[".$mcountlog."]".$mcountip." "; // &#13;&#10;
      }

      // �_�O���t�v�Z
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

      // �ҏW��ʂ�\��
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
           '<input type="submit" name="ctedit" value="�X�V">',
           '<input type="submit" name="ctedit" value="���Z�b�g">',
           '</td></form>',
           "</tr>\n";
    }
  }
  $all = (int)($all / 1024);

  echo <<<__TMP__
</table><br>
�y �摜�f�[�^���v : <b>$all</b> KB �z</div>\n
__TMP__;
  die("</body></html>");
}

/* �G���J�E���g�ҏW by �G���A */
function editcnt($pass) {
  global $counttno, $countedit, $ctedit, $newcount;

  $logmoe = MOE_LOG.$countedit.MOE_KAKU;
  if (!file_exists($logmoe)) {
    error("�Y�����O��������܂���ł���", '', TRUE);
  }

  $mp_data = file($logmoe);
  $countmp = count($mp_data);
//  for ($i = 0; $i < $countmp; $i++) {
//    list($mcountlog,) = explode(",", $mp_data[$i]);
//  }
  list($mcountlog,) = explode(",", $mp_data[$countmp - 1]);

  if ($ctedit == '���Z�b�g') { // �J�E���g�l�����ɖ߂�

    $fp = fopen($logmoe, "w");
    flock($fp, LOCK_EX);
    set_file_buffer($fp, 0);
    fputs($fp, "0,0.0.0.0\n");
    fclose($fp);
    chmod($logmoe, 0666);
    $msg = "!!CountReset!!<br><br>".$logmoe."<br>".$mcountlog." count -&gt; 0 count";

  } else { // �J�E���g�l���X�V

    if ($newcount != 'DEN') {
      if (!is_numeric($newcount)) {
        error("�J�E���g�l���s���ł��@No.".$counttno, '', TRUE);
      }
      if ($newcount >= MOE_DCNT) { // �a���C��
        $newcount = 'DEN';
      }
    } else {
      if ($mcountlog == 'DEN') {
        error("���łɓa�����肵�Ă��܂��@No.".$counttno, '', TRUE);
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

  // ���ʂ�\��
  echo <<<__TMP__
<br><br><hr size=1><br><br>
<div align=center>
<font color="red" size=5><b>$msg</b></font><p>
<input type="hidden" name="mode" value="admin">
<input type="hidden" name="admin" value="moecount">
<input type="hidden" name="pass" value="$pass">
<input type="submit" value=" �Ǘ���ʂɖ߂� ">
</div>
</form><hr size=1>\n
__TMP__;
  die("</body></html>");
}

/* �G���J�E���g�J�E���g�A�b�v�V�X�e���i��moecount.php�jby �G���A */
function votecount() {
  global $mcount, $moeno;

  $ip = $_SERVER['REMOTE_ADDR'];

  $mlogfile = MOE_LOG.$mcount.MOE_KAKU;
  if (!is_file($mlogfile)) {
    error("���O�t�@�C�������݂��܂���@No.".$moeno);
  }

  // �J�E���g���O�ǂݍ���
  $logmoe = file($mlogfile);
  $countlogmoe = count($logmoe);
  $ipflag = FALSE;
  if (MOE_IPC) { // ���e��������
    if (MOE_IPO) { // ���O�̂�
      list($vcountlog, $vipck) = explode(",", $logmoe[$countlogmoe - 1]);
      if (trim($vipck) == $ip) { $ipflag = TRUE; }
    } else { // ���ׂ�
      for ($i = 0; $i < $countlogmoe; $i++) {
        list($vcountlog, $vipck) = explode(",", $logmoe[$i]);
        if (trim($vipck) == $ip) {
          $ipflag = TRUE;
          break;
    } } }
  } else { // ���e�����Ȃ�
//    for ($i = 0; $i < $countlogmoe; $i++) {
//      list($vcountlog,) = explode(",", $logmoe[$i]);
//    }
    list($vcountlog,) = explode(",", $logmoe[$countlogmoe - 1]);
  }

  if (!$ipflag) { // ����IP�������ꍇ
    if ($vcountlog != 'DEN') {
      $countm = $vcountlog + 1;
      if ($countm >= MOE_DCNT) { //�a���C��
        $countm = 'DEN';
      }
    } else {
      $countm = 'DEN';
    }
    // �J�E���g���O�X�V
    $wnew = implode(",", array($countm, $ip, "\n"));
    $fp = fopen($mlogfile, "a");
    set_file_buffer($fp, 0);
    flock($fp, LOCK_EX);
    fputs($fp, $wnew);
    fclose($fp);
    chmod($mlogfile, 0666);
    if (!strcmp($countm, 'DEN')) {
      $msg = "�a�����肵�܂����I";
    } else {
      $msg = "�G���J�E���g�ɓ��[���܂����B";
    }
  } else { // IP����
    $msg = "�ЂƂ̉摜�ɕ����񓊕[�͂ł��܂���B";
  }

  updatelog();
  $self2_path = PHP_SELF2;

  // �X�^�C���V�[�g
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

  // �X�V��ʂ�\��
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
<span style="font-size: 8pt"><a href="$self2_path">�����[�h</a></span>
</div>
</body>
</html>\n
__EOD__;

  exit;
}

/* �G���J�E���g�a���M�������i��denview.php) by �G���A */
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

  echo "<div class=\"back\">[<a href=\"",PHP_SELF2,"\">�߂�</a>]</div>\n",
       "<div><font color=\"#cc1105\" size=\"+2\"><b><span>",MOE_TITLE2,"</span></b></font>\n";

  if (MOE_DNAG) {
    echo "<ul><li><small>�ŐV��",MOE_DNAGM,"���̂݉摜��\���A�ȉ����O�Ɠ��t�݂̂ƂȂ�܂��B</small></li></ul>\n";
  }
  echo "</div>\n";

  // �����|�C���^
  $start = 0;
  $stop  = MOE_DPG;

  // ����, �O�� �̃|�C���^
  if (!strcmp($denpage, 'next')) {
    $start = $nowpage + MOE_DPG;
    $stop  = $start   + MOE_DPG;
  } elseif (!strcmp($denpage, 'back')) {
    $stop  = $nowpage;
    $start = $stop - MOE_DPG;
  }

  // �a�����O�ǂݍ���
  $mdlog = @file(MOE_DLOG);
  $countmd = count($mdlog);

  // ��蕪�J��Ԃ�
  $imax = ($stop > $countmd) ? $countmd : $stop; // ����̐ݒ�
  $imin = ($start < 0) ? 0 : $start;             // �����̐ݒ�
  for ($i = $imin; $i < $imax; $i++) {
    $img_flag = FALSE;
    list($no,$now,$name,$time,$ext,$w,$h,$chk) = explode(",", $mdlog[$i]);

    $src = MOE_IMG.$time.$ext;
    // �摜������Ƃ��̓����N
    if ($ext && is_file($src)) {
      if (MOE_DNAG && $i >= MOE_DNAGM) {
        $clip = "�摜�̕\���͏o���܂���";
      } else {
        $img_flag = TRUE;
        $size = filesize($src);
        $clip = "<a href=\"".$src."\" target=\"_blank\">".
                "<img src=\"".$src."\" border=0 width=".$w." height=".$h." alt=\"".$size." B\"></a>";
      }
    } else {
      $clip = "�摜�폜�ς�";
    }
    // �L������
    echo <<<__TMP__
<div>
<table class="garelly">
 <tr>
  <td class="garelly">$clip</td>
 </tr><tr>
  <td class="gauther">���e�ҁF<b>$name</b><br>���e���F$now</td>
 </tr>
</table>
</div>\n
__TMP__;

  }

  // �ړ��{�^������
  echo "<br>\n<div>",
       "<form action=\"",PHP_SELF,"\" method=\"POST\">",
       "<input type=\"hidden\" name=\"nowpage\" value=\"",$start,"\">",
       "<input type=\"hidden\" name=\"denview\" value=\"view\">";

  if ($stop > MOE_DPG) {
    echo '<input type="submit" name="denpage" value="back">';
  }
  echo " ���� $start �` $stop ";
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

/* �J�E���g�V�X�e���֌W by �G���A */
if (!strcmp($moeta, 'countup')) // �G���J�E���g�J�E���g�A�b�v
  votecount();
if (!strcmp($denview, 'view'))  // �G���J�E���g�a���M������
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
