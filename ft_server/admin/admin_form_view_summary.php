<?php
/*

@author: Victory
@site: http://dfhu.org
@copyright: dfhu.org
@report_bugs: bugs(at)dfhu.org
@feature_request: features(at)dfhu.org
@file:
@license: BSD

@description:

  This file is great.

*/

require("./login_is.php");
require("../includes/db.inc.php");
$db_dir=DB_DIR;


function anchor_link($url){
  if($url == ""){
    return "none";
  }
  $url=filter_var($url,
		  FILTER_VALIDATE_URL, 
		  FILTER_FLAG_SCHEME_REQUIRED);
  $url_string=htmlentities($url);
  
  $url_link="
<a href=\"$url\" target=\"_blank\">$url_string</a>
";
  return $url_link;

}

if(!preg_match("/freetaleform.sqlite/",
	       $_GET['db'])){

  echo "BAD DATABSE";
  exit;
}
$db=new DBx(DB_DIR . $_GET['db']);


$sql="
SELECT 
  COUNT(*) AS cnt,
  SUM(time_elapsed) AS total_time_elapsed,
  SUM(time_elapsed)/COUNT(*) AS precent
FROM 
  actions
WHERE
  url = :url 
    AND
  input_type != 'landing'
GROUP BY
  input_name
";
$db->p($sql);
$db->exec(Array(':url'=>$_GET['url']));
$meta_row=$db->f();

/** /
$url_link=anchor_link($meta_row['url']);
$referer_link=anchor_link($meta_row['referer']);
$db->p($sql);
$db->exec($prepare_vars);
/**/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>
<title>Form Analytics</title>
<link rel="stylesheet" href="style.css" >
<meta http-equiv="content-type" content="text/html; charset=utf-8">

<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>

<meta name="description" content="" >
<meta name="keywords" content="" >

</head>
<body>

  <h2><?php echo $meta_row['remote_addr']; ?></h2>
  <ul>

    <li><?php echo date('ymd - H:i:s',$meta_row['unixtime']); ?> </li>
    <li><?php echo $url_link; ?> </li>
    <li><?php echo $referer_link ?> </li>
    <li><?php echo htmlentities($meta_row['user_agent']); ?> </li>

  </ul>
  
<?php





$template="
 <tr>
   <td>%form_id%</td>
   <td>%input_name%</td>
   <td>%input_type%</td>
   <td>%key_ups%</td>
   <td class=\"%time_length%\">%time_elapsed%</td>
 </tr>

";

global $form_action_vars;
$vars=
  array_map(create_function('$s',
			    'return "%$s%";'),
	    $form_action_vars);

ob_start();
echo "
<table id=\"form_analytics\">
 <thead>
  <th>Form Id</th>
  <th>Input Name</th>
  <th>Input Type</th>
  <th>Key Ups</th>
  <th>Time Elapsed (seconds)</th>
 </thead>
 <tfoot>
   <td colspan=\"5\" id=\"footnote\">

Footnotes go here, possibly injected via javascript

   </td>
 </tfoot>
";
while($vals=$db->f()){

  $vals=array_map('htmlentities',
		  $vals);

  //echo str_replace($vars,$vals,$template);
  /**/
  echo "<pre>";
  print_r($vals);
  echo "</pre>";
  /**/
  
}
echo "</table>";
ob_end_flush();

?>

</body>
</html>
