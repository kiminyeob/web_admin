<?php
/*

To do list

1. 참여 시간 formatting
2. 걸음 수와 거리 출력하기
3. 날짜 취합하기
4. unixtime --> 날짜변환(지금은 잘 맞지 않음)

*/
?>

<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="mystyle.css">
	<title>MySql-PHP 연결 테스트</title>
</head>
<body>

<?php
	//참여자 정보 이전 페이지에서 받아서 수정해야 함
	$user_id = "dianehyunsookim@gmail.com";
	$user_group = "캐시워크!!!";

	echo "<h1>".$user_id."<h1>";
	echo "<h2>".$user_group."</h2>";
	echo '<hr>';
?>

<?php
//ERRPR 화면 표시 여부
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>

<?php
//DB 접속 정보(이후에 보안 처리 해야함)
$db = mysqli_connect("keltpower0.kaist.ac.kr:6603", "root", "root", "my-schema"); 

//Character setting 설정
mysqli_query($db, 'set session character_set_connection=utf8mb4;');
mysqli_query($db, 'set session character_set_results=utf8mb4;');
mysqli_query($db, 'set session character_set_client=utf8mb4;');

//쿼리 날리기
$result = mysqli_query($db, "SELECT response, reaction_timestamp FROM survey WHERE email='$user_id'");
?>

<?php //설문 조사 결과 출력하는 PART //테이블 생성

echo '<h1>설문</h1>';
echo '<table class="type09"><thead><tr>';
echo '<th scope="cols">참여시간</th>';

$row = mysqli_fetch_assoc($result);
$json = json_decode($row['response'], true);
for($i=0; $i<count($json['questions']); $i=$i+1){
	echo '<th scope="cols">';
	try{
		print_r($json['questions'][$i]['text']);
	}catch (Exception $e){}
	echo '</th>';
}

echo '</tr></thead><tbody>';
while($row = mysqli_fetch_assoc($result)){ //row
	$json = json_decode($row['response'], true);

	echo '<tr>';
	echo '<th scope="row">'.date("Y-m-d H:i:s", (int)substr($row['reaction_timestamp'], 0, 10)).'</th>'; //unixtime stamp 처리(처음 10개 숫자만 가지고 출력)

	for($i=0; $i<count($json['questions']); $i=$i+1){
		try{
			echo '<td>'.$json['questions'][$i]['response'][0]."</td>";
		}catch (Exception $e){
			//echo "NULL"."<br>";
		}
	}//for
	echo '</tr>';
}//while

echo '</tbody></table>';
mysqli_close($db);
?>

<?php //신체 활동 결과 출력
echo '<h1>Data</h1>';
?>
 
</body>
</html>