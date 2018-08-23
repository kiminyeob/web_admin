<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>MySql-PHP 연결 테스트</title>
</head>
<body>
 
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

echo "MySql 연결 테스트<br>";
 
$db = mysqli_connect("keltpower0.kaist.ac.kr:6603", "root", "root", "my-schema"); // 이후에 보안 처리해야 함
 
if($db){
    echo "connect : 성공<br>";
}
else{
    echo "disconnect : 실패<br>";
}

mysqli_query($db, 'set session character_set_connection=utf8mb4;');
mysqli_query($db, 'set session character_set_results=utf8mb4;');
mysqli_query($db, 'set session character_set_client=utf8mb4;');

$result = mysqli_query($db, 'SELECT VERSION() as VERSION');
$data = mysqli_fetch_assoc($result);
echo $data['VERSION'];
echo "<br>";


$result2 = mysqli_query($db, 'SELECT response FROM survey');
$data = mysqli_fetch_assoc($result2);
echo $data['response'];

/*
while($row = mysqli_fetch_assoc($result2)){
	echo $row['email'];
	echo "<br/>";
}
*/

mysqli_close($db);

?>
 
</body>
</html>