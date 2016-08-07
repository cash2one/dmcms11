<?php
session_start();
require_once("../duomiphp/common.php");
require_once(duomi_INC.'/core.class.php');
if($cfg_user==0)
{
	ShowMsg('系统已关闭会员功能，正在转向网站首页','/',0,2000);
	exit();
}

$action = isset($action) ? trim($action) : '';
$pg = isset($pg) ? intval($pg) : 1;
$uid=$_SESSION['duomi_user_id'];
if(empty($_SESSION['duomi_user_id']))
{
	showMsg("请先登录","login.php");
	exit();
}

elseif($dm=='mypay')
{
	$key=$_POST['cardkey'];
	if($key==""){showMsg("请输入充值卡号","-1");exit;}
	$pwd=$_POST['cardpwd'];
	if($pwd==""){showMsg("请输入充值卡密码","-1");exit;}
	$sqlt="SELECT * FROM duomi_card where ckey='$key'";
	$sqlt="SELECT * FROM duomi_card where cpwd='$pwd'";
	$row1 = $dsql->GetOne($sqlt);
    if(!is_array($row1) OR $row1['status']<>0){
        showMsg("充值卡信息有误","-1");exit;
    }else{
		$uname=$_SESSION['duomi_user_name'];
		$points=$row1['climit'];
        $dsql->executeNoneQuery("UPDATE duomi_card SET usetime=NOW(),uname='$uname',status='1' WHERE ckey='$key'");
		$dsql->executeNoneQuery("UPDATE duomi_card SET usetime=NOW(),uname='$uname',status='1' WHERE cpwd='$pwd'");
		$dsql->executeNoneQuery("UPDATE duomi_member SET points=points+$points WHERE username='$uname'");
		showMsg("恭喜！充值成功！","mypay.php");exit;
    }
}
else
{
$tempfile = duomi_ROOT."/member/html/mypay.html";
	$content=loadFile($tempfile);
	$t=$content;
	$t=$mainClassObj->parseTopAndFoot($t);
	$t=$mainClassObj->parseHistory($t);
	$t=$mainClassObj->parseSelf($t);
	$t=$mainClassObj->parseGlobal($t);
	$t=$mainClassObj->parduomireaList($t);
	$t=$mainClassObj->parseMenuList($t,"");
	$t=$mainClassObj->parseVideoList($t,-444);
	$t=$mainClassObj->parseNewsList($t,-444);
	$t=$mainClassObj->parseTopicList($t);
	$t=replaceCurrentTypeId($t,-444);
	$t=$mainClassObj->parseIf($t);
	$t=str_replace("{duomicms:member}",front_member(),$t);
	echo $t;
	exit();
}