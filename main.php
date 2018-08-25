<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="css/mystyle.css">
		<link rel="stylesheet" type="text/css" href="css/main_page.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min_1.css">
		<link rel="stylesheet" type="text/css" href="css/tableexport.min.css">
		<meta charset="utf-8">
		<title>관리자 페이지</title>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.12.0.min.js" ></script>
		<script type="text/javascript">
			function tabSetting() {
				// 탭 컨텐츠 hide 후 현재 탭메뉴 페이지만 show
				$('.tabPage').hide();
				$($('.current').find('a').attr('href')).show();
		 
				// Tab 메뉴 클릭 이벤트 생성
				$('li').click(function (event) {
					var tagName = event.target.tagName; // 현재 선택된 태그네임
					var selectedLiTag = (tagName.toString() == 'A') ? $(event.target).parent('li') : $(event.target); // A태그일 경우 상위 Li태그 선택, Li태그일 경우 그대로 태그 객체
					var currentLiTag = $('li[class~=current]'); // 현재 current 클래그를 가진 탭
					var isCurrent = false;  
					 
					// 현재 클릭된 탭이 current를 가졌는지 확인
					isCurrent = $(selectedLiTag).hasClass('current');
					 
					// current를 가지지 않았을 경우만 실행
					if (!isCurrent) {
						$($(currentLiTag).find('a').attr('href')).hide();
						$(currentLiTag).removeClass('current');
		 
						$(selectedLiTag).addClass('current');
						$($(selectedLiTag).find('a').attr('href')).show();
					}
		 
					return false;
				});
			}
		 
			$(function () {
				// 탭 초기화 및 설정
				tabSetting();
			});
		</script>
	</head>
	<body>

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

//실험 id(module화 할 수 있을까?)
$experiment_uuid = "1ce4f2cfb6a944adb1584ec74301041b"; //subject
$experiment_uuid2 = "1ce4f2cf-b6a9-44ad-b158-4ec74301041b"; //survey
$experiment_uuid3 = "38fd7ac53ec14ec2bb4720ea7bf00f46"; //ai-flagship
$experiment_uuid4 = "38fd7ac5-3ec1-4ec2-bb47-20ea7bf00f46";
//$case = $_GET['case'];

if(!empty($_GET['keyword'])){
	$keyword = $_GET['keyword']; //바꿔야 함
	$case = 5;
} else {
	if(!empty($_GET['gender'])){
		if(!empty($_GET['group'])){
			$gender = $_GET['gender']; $group = $_GET['group']; $case = 4;
		} else{
			$gender = $_GET['gender']; $case = 2;
		}
	} else if(!empty($_GET['group'])){
		$group = $_GET['group']; $case = 3;
	} else {
		$case = 1;
	}
}


//쿼리 날리기

switch($case){//종합 정보
	case 1: //no condition
		$result = mysqli_query($db, "SELECT participated_timestamp, experiment_group, name, gender, birth_date, email, phone_number FROM subject WHERE experiment_uuid ='$experiment_uuid' ORDER BY participated_timestamp");
		break;
	case 2: //only gender
		$result = mysqli_query($db, "SELECT participated_timestamp, experiment_group, name, gender, birth_date, email, phone_number FROM subject WHERE gender = '$gender' and experiment_uuid ='$experiment_uuid' ORDER BY participated_timestamp");
		break;
	case 3: //only group (아직)
		$result = mysqli_query($db, "SELECT participated_timestamp, experiment_group, name, gender, birth_date, email, phone_number FROM subject WHERE experiment_uuid ='$experiment_uuid' and experiment_group ='$group' ORDER BY participated_timestamp");
		break;
	case 4: //gender group togerther (아직)
		$result = mysqli_query($db, "SELECT participated_timestamp, experiment_group, name, gender, birth_date, email, phone_number FROM subject WHERE experiment_uuid ='$experiment_uuid' and experiment_group ='$group' and gender = '$gender' ORDER BY participated_timestamp");
		break;
	case 5: //지정 (아직)
		$result = mysqli_query($db, "SELECT participated_timestamp, experiment_group, name, gender, birth_date, email, phone_number FROM subject WHERE experiment_uuid ='$experiment_uuid' and name LIKE '%$keyword%' ORDER BY participated_timestamp");
		break;
}

switch($case){//설문 결과
	case 1: //no condition
		$result2 = mysqli_query($db, "SELECT response, reaction_timestamp, email, experiment_group FROM survey WHERE experiment_uuid ='$experiment_uuid2' ORDER BY email");
		break;
	case 3: //only group (아직)
		$result2 = mysqli_query($db, "SELECT response, reaction_timestamp, email, experiment_group FROM survey WHERE experiment_uuid ='$experiment_uuid2' and experiment_group ='$group' ORDER BY email");
		break;
	case 5: //지정 (아직)
		$result2 = mysqli_query($db, "SELECT response, reaction_timestamp, email, experiment_group FROM survey WHERE experiment_uuid ='$experiment_uuid2' and email IN (SELECT email FROM subject WHERE name LIKE '%$keyword%' ORDER BY participated_timestamp)");
		break;
}

switch($case){//걸음 결과
	case 1: //no condition
		$result4 =mysqli_query($db, "SELECT email, experiment_group, FROM_UNIXTIME(start_time/1000,'%Y%m%d') as start_time, sum(value) as value, type FROM physical_status WHERE type IN ('TotalActivityDistance', 'TotalActivityStepCounts', 'TotalDistance', 'TotalStepCounts') and experiment_uuid ='$experiment_uuid4' GROUP BY type, FROM_UNIXTIME(start_time/1000,'%Y%m%d') ORDER BY start_time, type");
		break;
	case 3: //only group (아직)
		$result4 =mysqli_query($db, "SELECT email, experiment_group, FROM_UNIXTIME(start_time/1000,'%Y%m%d') as start_time, sum(value) as value, type FROM physical_status WHERE type IN ('TotalActivityDistance', 'TotalActivityStepCounts', 'TotalDistance', 'TotalStepCounts') and experiment_group ='$group' and experiment_uuid ='$experiment_uuid4' GROUP BY type, FROM_UNIXTIME(start_time/1000,'%Y%m%d') ORDER BY start_time, type");
		break;
	case 5: //지정 (아직)
		$result4 =mysqli_query($db, "SELECT email, experiment_group, FROM_UNIXTIME(start_time/1000,'%Y%m%d') as start_time, sum(value) as value, type FROM physical_status WHERE type IN ('TotalActivityDistance', 'TotalActivityStepCounts', 'TotalDistance', 'TotalStepCounts') and experiment_uuid ='$experiment_uuid4' and email IN (SELECT email FROM subject WHERE name LIKE '%$keyword%') GROUP BY type, FROM_UNIXTIME(start_time/1000,'%Y%m%d') ORDER BY start_time, type");
		break;
}


?>
		<br>
		<div class="tabWrap">
			<ul class="tab_Menu">
				<?php if($_GET['submit']=='submit1' or !isset($_GET['submit'])){echo '<li class="tabMenu current">';}
				else{echo '<li class="tabMenu">';}?>
					<a href="#tabContent01" >사용자 정보</a>
				</li>
				<?php if($_GET['submit']=='submit2'){echo '<li class="tabMenu current">';}
				else{echo '<li class="tabMenu">';}?>
					<a href="#tabContent02" >걸음 데이터</a>
				</li>
				<?php if($_GET['submit']=='submit3'){echo '<li class="tabMenu current">';}
				else{echo '<li class="tabMenu">';}?>
					<a href="#tabContent03" >설문 데이터</a>
				</li>
			</ul>
			<div class="tab_Content_Wrap">
				<div id="tabContent01" class="tabPage">

<br><br>
<div style="border:1px; text-align:center; clear: both">
	<form action="main.php" method="get">
		<b>성 별:</b>
		<input type="radio" name="gender" value="GENDER_MALE">남
		<input type="radio" name="gender" value="GENDER_FEMALE">여
		<br><br>

		<b>그 룹:</b>
		<input type="radio" name="group" value="캐시워크">캐시워크
		<input type="radio" name="group" value="워커">워커
		<input type="radio" name="group" value="피트머니">피트머니
		<input type="radio" name="group" value="통제집단">통제집단
		<br><br>

		<b>이름검색:</b>
		<input type="text" name="keyword" />
		<br><br>

		<input type="submit" name="submit" value="submit1">
	</form>
</div>

<br><br>

<?php //첫번째 page 테이블
					echo '<table class="type09" id="table_result">';
				    echo '<thead>';
				    echo '<tr>';
				    echo '<th scope="cols">번호</th>';       
				    echo '<th scope="cols">가입시간</th>';       
				    echo '<th scope="cols">그룹</th>';   
				    echo '<th scope="cols">이름</th>';       
				    echo '<th scope="cols">성별</th>';       
				    echo '<th scope="cols">생년월일</th>';       
				    echo '<th scope="cols">이메일</th>';       
				    echo '<th scope="cols">전화번호</th>';       
				    echo '<th scope="cols">가입이후</th>';       
				    echo '<th scope="cols">설문</th>';       
				    echo '</tr>';   
				    echo '</thead>';   
				    echo '<tbody>';

				    $curDate_short = date('Y-m-d');
				    $end = new DateTime($curDate_short);

				    $count = 1;
				    while ($row = mysqli_fetch_assoc($result)){

				    	$participated_timestamp = (int)substr($row['participated_timestamp'], 0, 10);
				    	$participated_date_detail = date("Y-m-d H:i:s",$participated_timestamp);
				    	$participated_date_short = date("Y-m-d",$participated_timestamp);
				    	$start = new DateTime($participated_date_short);

				    	echo '<tr>';
				    	echo '<th scope="row">'.strval($count).'</th>';
				    	echo '<td>'.$participated_date_detail.'</td>';
				    	echo '<td>'.$row['experiment_group'].'</td>';
				    	echo '<td><b><a href="individual_result.php?email='.$row['email'].'&group='.$row['experiment_group'].'" target="_blank">'.$row['name'].'</a></b></td>';
				    	echo '<td>'.str_replace("GENDER_","",$row['gender']).'</td>';
				    	echo '<td>'.substr($row['birth_date'], 0, 10).'</td>';
				    	echo '<td>'.$row['email'].'</td>';
				    	echo '<td>'.$row['phone_number'].'</td>';
				    	echo '<td>'.'+'.date_diff($start,$end)->days.'</td>';
				    	echo '<td>'.'설문'.'</td>';
				    	echo '</tr>';
				    	$count = $count + 1;
				    }
				    $count = 0;
?>
				  </tbody>
				  </table>

			<script src="js/bootstrap.min_1.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/FileSaver.min.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/jquery-3.1.1.min.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/tableexport.min.js" type="text/javascript" charset='euc-kr'></script>

			<div style="text-align:center; clear: both">
				<script>
					$('#table_result').tableExport();
				</script>
			</div>

				</div>
				<div id="tabContent02" class="tabPage">


			<br><br>
			<div style="border:1px; text-align:center; clear: both">
				<form action="main.php" method="get">
					<b>그 룹:</b>
					<input type="radio" name="group" value="캐시워크">캐시워크
					<input type="radio" name="group" value="워커">워커
					<input type="radio" name="group" value="피트머니">피트머니
					<input type="radio" name="group" value="통제집단">통제집단
					<br><br>

					<b>이름검색:</b>
					<input type="text" name="keyword" />
					<br><br>

					<input type="submit" name="submit" value="submit2">
				</form>
			</div>
			<br><br>
 
<?php //두번째
					echo '<table class="type09" id="table_result3">'.'<thead>'.'<tr>';
				    echo '<th scope="cols">번호</th>';       
				    echo '<th scope="cols">이름</th>';       
				    echo '<th scope="cols">그룹</th>';   
				    echo '<th scope="cols">날짜</th>';
				    echo '<th scope="cols">타입</th>';
				    echo '<th scope="cols">값</th>';
				    /*
				    echo '<th scope="cols">걸음수(보정)</th>';
				    echo '<th scope="cols">거리</th>';
				    echo '<th scope="cols">걸음수</th>';
				    */
				    echo '</tr>'.'</thead>';

				    /*
				    echo '<tbody>';
				    $count3 = 1;
				    $flag = 0;
				    while($row4 = mysqli_fetch_assoc($result4)){ //row4는 걸음과 거리
				    	if ($flag == 0){
					    	echo '<tr>';
							echo '<th scope="row">'.$count3.'</th>';

							$email_temp2 = $row4['email'];
							$result5 = mysqli_query($db, "SELECT name FROM subject WHERE experiment_uuid ='$experiment_uuid3' and email='$email_temp2'"); //result 5는 이메일을 통해서 사용자 이름을 찾기 위함
							$row5 = mysqli_fetch_assoc($result5);
							echo '<td>'.$row5['name'].'</td>';

							$experiment_group2 = '그룹 미지정';
							if(!empty($row4['experiment_group'])){
								$experiment_group2 = $row4['experiment_group'];
							}

							echo '<td>'.$experiment_group2.'</td>';
							echo '<td>'.$row4['start_time'].'</td>';
							echo '<td>'.$row4['value'].'</td>';
						}
						else{
							echo '<td>'.$row4['value'].'</td>';
							if ($flag == 3){
								$flag = -1;
								$count3 = $count3+1;
							}
						}
						$flag = $flag +1;
					}
					echo '</tr>';
					echo '</tbody></table>';
					*/

				    echo '<tbody>';
				    $count3 = 1;
				    while($row4 = mysqli_fetch_assoc($result4)){ //row4는 걸음과 거리
					    echo '<tr>';
						echo '<th scope="row">'.$count3.'</th>';

						$email_temp2 = $row4['email'];
						$result5 = mysqli_query($db, "SELECT name FROM subject WHERE experiment_uuid ='$experiment_uuid3' and email='$email_temp2'"); //result 5는 이메일을 통해서 사용자 이름을 찾기 위함
						$row5 = mysqli_fetch_assoc($result5);
						echo '<td>'.$row5['name'].'</td>';

						$experiment_group2 = '그룹 미지정';
						if(!empty($row4['experiment_group'])){
							$experiment_group2 = $row4['experiment_group'];
						}

						echo '<td>'.$experiment_group2.'</td>';
						echo '<td>'.$row4['start_time'].'</td>';
						echo '<td>'.$row4['type'].'</td>';
						echo '<td>'.$row4['value'].'</td>';
						$count3 = $count3+1;
					}

					echo '</tr>';
					echo '</tbody></table>';
?>
				</div>

			<script src="js/bootstrap.min_1.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/FileSaver.min.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/jquery-3.1.1.min.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/tableexport.min.js" type="text/javascript" charset='euc-kr'></script>

			<div style="text-align:center; clear: both">
				<script>
					$('#table_result3').tableExport();
				</script>
			</div>


				<div id="tabContent03" class="tabPage">

<br><br>
<div style="border:1px; text-align:center; clear: both">
	<form action="main.php" method="get">
		<b>그 룹:</b>
		<input type="radio" name="group" value="캐시워크">캐시워크
		<input type="radio" name="group" value="워커">워커
		<input type="radio" name="group" value="피트머니">피트머니
		<input type="radio" name="group" value="통제집단">통제집단
		<br><br>

		<b>이름검색:</b>
		<input type="text" name="keyword" />
		<br><br>

		<input type="submit" name="submit" value="submit3">
	</form>
</div>

<br><br>

<?php //세번째 page 테이블
					echo '<table class="type09" id="table_result2">';
				    echo '<thead>';
				    echo '<tr>';
				    echo '<th scope="cols">번호</th>';       
				    echo '<th scope="cols">이름</th>';       
				    echo '<th scope="cols">그룹</th>';   
				    echo '<th scope="cols">설문참여시간</th>';  

					$row2 = mysqli_fetch_assoc($result2);
					$json2 = json_decode($row2['response'], true);
					for($i=0; $i<count($json2['questions']); $i=$i+1){
						echo '<th scope="cols">';
						try{
							echo '문항'.strval($i+1);
						}catch (Exception $e){}
						echo '</th>';
					}

				    echo '</tr>';   
				    echo '</thead>';   
				    echo '<tbody>';

				    $count2 = 1;
					while($row2 = mysqli_fetch_assoc($result2)){ //row
						$json2 = json_decode($row2['response'], true);

						echo '<tr>';
						echo '<th scope="row">'.$count2.'</th>';

						$email_temp = $row2['email'];
						$result3 = mysqli_query($db, "SELECT name FROM subject WHERE experiment_uuid ='$experiment_uuid' and email='$email_temp'");
						$row3 = mysqli_fetch_assoc($result3);
						echo '<td>'.$row3['name'].'</td>';

						$experiment_group = '그룹 미지정';
						if(!empty($row2['experiment_group'])){
							$experiment_group = $row2['experiment_group'];
						}

						echo '<td>'.$experiment_group.'</td>';

				    	$reaction_timestamp = (int)substr($row2['reaction_timestamp'], 0, 10);
				    	$reaction_timestamp_detail = date("Y-m-d H:i:s",$reaction_timestamp);

						echo '<td>'.$reaction_timestamp_detail.'</td>'; //unixtime stamp 처리(처음 10개 숫자만 가지고 출력)

						for($i=0; $i<count($json2['questions']); $i=$i+1){
							try{
								echo '<td>'.$json2['questions'][$i]['response'][0]."</td>";
							}catch (Exception $e){
								//echo "NULL"."<br>";
							}
						}//for
						echo '</tr>';
						$count2 = $count2+1;
					}//while

					echo '</tbody></table>';
					?>

			<script src="js/bootstrap.min_1.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/FileSaver.min.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/jquery-3.1.1.min.js" type="text/javascript" charset='euc-kr'></script>
			<script src="js/tableexport.min.js" type="text/javascript" charset='euc-kr'></script>

			<div style="text-align:center; clear: both">
				<script>
					$('#table_result2').tableExport();
				</script>
			</div>

				</div>
			</div>
		</div>
	</body>
</html>