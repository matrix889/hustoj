<?php require_once("admin-header.php");?>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<title>添加作业或考试</title>

<?php

	require_once("../include/const.inc.php");
$description="";
 if (isset($_POST['syear']))
{
	
	require_once("../include/db_info.inc.php");
	require_once("../include/check_post_key.php");
	
	$starttime=$_POST['syear']." ".intval($_POST['shour']).":".intval($_POST['sminute']).":00";
	$endtime=$_POST['eyear']." ".intval($_POST['ehour']).":".intval($_POST['eminute']).":00";
	//	echo $starttime;
	//	echo $endtime;

        $title=$_POST['title'];
        $private=$_POST['private'];
        $description=$_POST['description'];
        if (get_magic_quotes_gpc ()){
                $title = stripslashes ($title);
                $private = stripslashes ($private);
             //   $description = stripslashes ($description);
        }

	$title=mysql_real_escape_string($title);
	$private=mysql_real_escape_string($private);
	$description=mysql_real_escape_string($description);
	
    $lang=$_POST['lang'];
    $langmask=0;
    foreach($lang as $t){
			$langmask+=1<<$t;
	} 
$langmask=((1<<count($language_ext))-1)&(~$langmask);

	//echo $langmask;	
	
	$sql="INSERT INTO `contest`(`title`,`start_time`,`end_time`,`private`,`langmask`,`description`)
		VALUES('$title','$starttime','$endtime','$private',$langmask,'$description')";
//	echo $sql;
	mysql_query($sql) or die(mysql_error());
	$cid=mysql_insert_id();
	echo "Add Contest ".$cid;
	$sql="DELETE FROM `contest_problem` WHERE `contest_id`=$cid";
	$plist=trim($_POST['cproblem']);
	$pieces = explode(",",$plist );
	if (count($pieces)>0 && strlen($pieces[0])>0){
		$sql_1="INSERT INTO `contest_problem`(`contest_id`,`problem_id`,`num`) 
			VALUES ('$cid','$pieces[0]',0)";
		for ($i=1;$i<count($pieces);$i++){
			$sql_1=$sql_1.",('$cid','$pieces[$i]',$i)";
		}
		//echo $sql_1;
		mysql_query($sql_1) or die(mysql_error());
		$sql="update `problem` set defunct='N' where `problem_id` in ($plist)";
		mysql_query($sql) or die(mysql_error());
	}
	$sql="DELETE FROM `privilege` WHERE `rightstr`='c$cid'";
	mysql_query($sql);
	$sql="insert into `privilege` (`user_id`,`rightstr`)  values('".$_SESSION['user_id']."','m$cid')";
	mysql_query($sql);
	$_SESSION["m$cid"]=true;
	$pieces = explode("\n", trim($_POST['ulist']));
	if (count($pieces)>0 && strlen($pieces[0])>0){
		$sql_1="INSERT INTO `privilege`(`user_id`,`rightstr`) 
			VALUES ('".trim($pieces[0])."','c$cid')";
		for ($i=1;$i<count($pieces);$i++)
			$sql_1=$sql_1.",('".trim($pieces[$i])."','c$cid')";
		//echo $sql_1;
		mysql_query($sql_1) or die(mysql_error());
	}
	echo "<script>window.location.href=\"contest_list.php\";</script>";
}
else{
	
   if(isset($_GET['cid'])){
		   $cid=intval($_GET['cid']);
		   $sql="select * from contest WHERE `contest_id`='$cid'";
		   $result=mysql_query($sql) or die(mysql_error());
		   $row=mysql_fetch_object($result);
		   $title=$row->title;
		   mysql_free_result($result);
			$plist="";
			$sql="SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`=$cid ORDER BY `num`";
			$result=mysql_query($sql) or die(mysql_error());
			for ($i=mysql_num_rows($result);$i>0;$i--){
				$row=mysql_fetch_row($result);
				$plist=$plist.$row[0];
				if ($i>1) $plist=$plist.',';
			}
			mysql_free_result($result);
   }
else if(isset($_POST['problem2contest'])){
	   $plist="";
	   //echo $_POST['pid'];
	   sort($_POST['pid']);
	   foreach($_POST['pid'] as $i){		    
			if ($plist) 
				$plist.=','.$i;
			else
				$plist=$i;
	   }
}else if(isset($_GET['spid'])){
	require_once("../include/check_get_key.php");
		   $spid=intval($_GET['spid']);
		 
			$plist="";
			$sql="SELECT `problem_id` FROM `problem` WHERE `problem_id`>=$spid ";
			$result=mysql_query($sql) or die(mysql_error());
			for ($i=mysql_num_rows($result);$i>0;$i--){
				$row=mysql_fetch_row($result);
				$plist=$plist.$row[0];
				if ($i>1) $plist=$plist.',';
			}
			mysql_free_result($result);
}  
  include_once("../fckeditor/fckeditor.php") ;
if(isset($OJ_LANG)){
		require_once("../lang/$OJ_LANG.php");
		
	}else{
		require_once("../lang/en.php");
	}
	

	
?>
    <link type="text/css" rel="Stylesheet" href="../date/styles/main.css" />
    <script type="text/javascript" language="javascript" src="../date/scripts/jquery.js"></script>
    <script type="text/javascript" language="javascript" src="../date/scripts/eye-base.js"></script>
    <script type="text/javascript" language="javascript" src="../date/scripts/eye-all.js"></script>
    
	<form method=POST >
	<p align=center><font size=4 color=#333399>
    添加作业或考试</font></p>
	<p align=left>作业标题<input class=input-xxlarge  type=text name=title size=71 value="<?php echo isset($title)?$title:""?>"></p>
	<p align=left>开始时间:<br>&nbsp;&nbsp;&nbsp;
	日期：<input onClick="eye.datePicker.show(this);"  class=input type=text name=syear size=4 >
	
	<select class=input-mini name=shour >
	       <option value=0>0</option>
	       <option value=1>1</option>
	       <option value=2>2</option>
	       <option value=3>3</option>
	       <option value=4>4</option>
	       <option value=5>5</option>
	       <option value=6>6</option>
	       <option value=7>7</option>
	       <option value=8>8</option>
	       <option value=9>9</option>
	       <option value=10>10</option>
	       <option value=11>11</option>
	       <option value=12>12</option>
	       <option value=13>13</option>
	       <option value=14>14</option>
	       <option value=15>15</option>
	       <option value=16>16</option>
	       <option value=17>17</option>
	       <option value=18>18</option>
	       <option value=19>19</option>
	       <option value=20>20</option>
	       <option value=21>21</option>
	       <option value=22>22</option>
	       <option value=23>23</option>
	       
	     </select>
	
	:<select class=input-mini name=sminute   >
	       <option value=0>0</option>
	       <option value=5>5</option>
	       <option value=10>10</option>
	       <option value=15>15</option>
	       <option value=25>25</option>
	       <option value=30>30</option>
	       <option value=35>35</option>
	       <option value=40>40</option>
	       <option value=45>45</option>
	       <option value=50>50</option>
	       <option value=55>55</option>
	
	</select><p align=left>结束时间:<br>&nbsp;&nbsp;&nbsp;
	日期:<input onclick="eye.datePicker.show(this);"  class=input type=text name=eyear size=4 >
	<select class=input-mini name=ehour >
	       <option value=0>0</option>
	       <option value=1>1</option>
	       <option value=2>2</option>
	       <option value=3>3</option>
	       <option value=4>4</option>
	       <option value=5>5</option>
	       <option value=6>6</option>
	       <option value=7>7</option>
	       <option value=8>8</option>
	       <option value=9>9</option>
	       <option value=10>10</option>
	       <option value=11>11</option>
	       <option value=12>12</option>
	       <option value=13>13</option>
	       <option value=14>14</option>
	       <option value=15>15</option>
	       <option value=16>16</option>
	       <option value=17>17</option>
	       <option value=18>18</option>
	       <option value=19>19</option>
	       <option value=20>20</option>
	       <option value=21>21</option>
	       <option value=22>22</option>
	       <option value=23>23</option>
	     </select>
	
	:<select class=input-mini name=eminute  >
           <option value=0>0</option>
	       <option value=5>5</option>
	       <option value=10>10</option>
	       <option value=15>15</option>
	       <option value=25>25</option>
	       <option value=30>30</option>
	       <option value=35>35</option>
	       <option value=40>40</option>
	       <option value=45>45</option>
	       <option value=50>50</option>
	       <option value=55>55</option>
	
	</select></p><br>
	<p  align=left>
	<?php echo $MSG_Public?>:<select name=private><option value=0>作业</option><option value=1>考试</option></select>
	
	可选编程语言：<select name="lang[]" multiple="multiple"    style="height:220px">
	<?php
$lang_count=count($language_ext);

 $langmask=$OJ_LANGMASK;

 for($i=0;$i<$lang_count;$i++){
                 echo "<option value=$i selected>
                        ".$language_name[$i]."
                 </option>";
  }

?>


        </select>
	<?php require_once("../include/set_post_key.php");?>
	<br>问题（请用英文逗号分隔题目序号如:1000,1003）:<input class=input-xxlarge type=text size=60 name=cproblem value="<?php echo isset($plist)?$plist:""?>">
	<br>
	<p align=left>作业考试说明:<br><!--<textarea rows=13 name=description cols=80></textarea>-->

<?php
$fck_description = new FCKeditor('description') ;
$fck_description->BasePath = '../fckeditor/' ;
$fck_description->Height = 300 ;
$fck_description->Width=600;
$fck_description->Value = $description ;
$fck_description->Create() ;

?>
	可以参加考试的学生学号列表:<textarea name="ulist" rows="20" cols="20"></textarea>
	<br />
	*可以将学生学号从Excel整列复制过来，然后要求他们用学号做UserID注册,就能进入Private的比赛作为作业和测验。
	<p><input type=submit value=Submit name=submit><input type=reset value=Reset name=reset></p>
	</form>

<?php }
require_once("../oj-footer.php");

?>

