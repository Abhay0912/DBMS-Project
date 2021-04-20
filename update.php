<?php
include_once 'db.php';
session_start();

$db = mysqli_connect('localhost', 'root', '', 'simp1');

$email = $_SESSION['email'];
$query = "SELECT * FROM teacher_list WHERE email='$email'";
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
$teacher_id = $row['id'];//session variable here
$_SESSION['id'] = $teacher_id;
$tname = $row['name'];
$_SESSION['name'] = $tname;
//add quiz
if(isset($_POST['submit']) && $_SESSION['role']=='Teacher'){
if(@$_GET['q']== 'addquiz') {
$name = $_POST['name'];
$course = $_POST['course'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$total= $_POST['total'];
$noc= $_POST['noc'];
$id=uniqid();

if($name)
{$subpt1="INSERT INTO quiz(quiz_id,created_for, name, start_time, end_time, created_by) VALUES ('$id','$course', '$name', '$start_time', '$end_time','$teacher_id')";
if (mysqli_query($conn,$subpt1)){echo "New Test has been created successfully !";}
else {echo "Error: " . $subpt1 . ":-" . mysqli_error($conn);}
header("location:teacher.php?q=1&step=2&eid=$id&n=$total&noc=$noc");
mysqli_close($conn);}
else{header("location:teacher.php?q=1");}
}}

//edit quiz
if(isset($_POST['submit']) && $_SESSION['role']=='Teacher'){
if(@$_GET['q']== 'edquiz') {
$name = @$_GET['name'];
$total= $_POST['total'];
$noc= $_POST['noc'];
$id=@$_GET['eid'];
header("location:teacher.php?q=1&step=2&eid=$id&n=$total&noc=$noc");
mysqli_close($conn);}
}

//add question
if(isset($_POST['submit']) && $_SESSION['role']=='Teacher'){
if(@$_GET['q']== 'addqns') {
$n=@$_GET['n'];
$eid=@$_GET['eid'];
$ch=@$_GET['ch'];
$noc= $_GET['noc'];
for($i=1;$i<=$n;$i++)
 {
 $qns=$_POST['qns'.$i];
 $right=@$_POST['right'.$i];
$wrong=@$_POST['wrong'.$i];
$typ=@$_POST['typ'.$i];

if($qns!=NULL)
{$qid=uniqid();
	if($typ==0)
{$subpt2="INSERT INTO question (question_id, text, quiz_id, type, pmarks, nmarks) VALUES  ('$qid','$qns','$eid',0,'$right','$wrong')";

if (mysqli_query($conn,$subpt2)){echo "New question has been created successfully !";}
else {echo "Error: " . $subpt2 . ":-" . mysqli_error($conn);}
$a=$_POST['a'.$i];
$isc = $_POST['isc'.$i];

for($j=0;$j<count($a);$j++)
 {
 	if($a[$j]!="")
 	{$oaid=uniqid();
echo'$isc['.$j.'+1]='.$isc[$j+1].'';
 	if($isc[$j]=="y")
 	{$subpt3="INSERT INTO question_option(option_id,text, is_correct,question_id) VALUES ('$oaid','$a[$j]',1,'$qid')";}
 	else if($isc[$j]=="n")
 	{$subpt3="INSERT INTO question_option(option_id,text, is_correct,question_id) VALUES ('$oaid','$a[$j]',0,'$qid')";}

if (mysqli_query($conn,$subpt3)){echo "New options have been created successfully !";}
else {echo "Error: " .$subpt3. ":-" . mysqli_error($conn);}
 	}
 }
}
else if ($typ==1) {
$subpt2="INSERT INTO question (question_id, text, quiz_id, type, pmarks, nmarks) VALUES  ('$qid','$qns','$eid',1,'$right','$wrong')";
if (mysqli_query($conn,$subpt2)){echo "New question has been created successfully !";}
else {echo "Error: " . $subpt2 . ":-" . mysqli_error($conn);}
}

}
}
header("location:teacher.php?q=1");
}
}

if(@$_GET['q']=='quesdel' && $_SESSION['role']=='Teacher') {
$qid=@$_GET['qid'];
$quiz_id=@$_GET['quiz_id'];
$name=@$_GET['name'];
echo'reached file';

$r1 = mysqli_query($conn,"DELETE FROM question_option WHERE question_id='$qid'") or die('Error');

$r2 = mysqli_query($conn,"DELETE FROM question WHERE question_id='$qid' ") or die('Error');
header("location:teacher.php?q=5&name=$name&quiz_id=$quiz_id");
}

if(@$_GET['q']=='opdel' && $_SESSION['role']=='Teacher') {
$qid=@$_GET['qid'];
$quiz_id=@$_GET['quiz_id'];
$name=@$_GET['name'];
$op_id=@$_GET['op_id'];
$r1 = mysqli_query($conn,"DELETE FROM question_option WHERE option_id='$op_id'") or die('Error');

header("location:teacher.php?q=5&name=$name&quiz_id=$quiz_id");
}

if(@$_GET['q']=='remupd' && $_SESSION['role']=='Teacher') {
include_once 'db.php';
$quiz_id=@$_GET['quiz_id'];
$result = mysqli_query($conn,"SELECT * FROM question WHERE quiz_id='$quiz_id' ") or die('Error');
while($row = mysqli_fetch_array($result)) {
	$qid = $row['question_id'];
$r1 = mysqli_query($conn,"DELETE FROM question_option WHERE question_id='$qid'") or die('Error');
}
$r2 = mysqli_query($conn,"DELETE FROM question WHERE quiz_id='$quiz_id' ") or die('Error');
$r3 = mysqli_query($conn,"DELETE FROM quiz WHERE quiz_id='$quiz_id' ") or die('Error');
header("location:teacher.php?q=3");}

if(@$_GET['q']=='des' && $_SESSION['role']=='Teacher')
		{
			$arr=$_POST['arr'];

			$total=0;
			for($i=0;$i<count($arr);$i++){
				$total=$total+$arr[$i];	}

            //dummy data
			$student=$_POST['std_id'];
		
			//echo"this is $student and marks scored is $total";
			$quiz_id=$_POST['quiz_id'];
			$marks_sub=$total;
			$con = mysqli_connect("localhost","root", "", "quiz");
			
			//this is to count whether record exist or not
			$check=mysqli_query($con,"SELECT student_id FROM student_marks where student_id='$student' and quiz_id='$quiz_id' ");
			//echo"quiz_id is = $quiz_id ";		
			if(mysqli_num_rows($check)>0){
				mysqli_query($con,"UPDATE student_response_des set is_corrected='1' where student_id='$student' and quiz_id='$quiz_id'");
				//echo"reached update ";	
				mysqli_query($con,"UPDATE student_marks set marks_sub='$total' WHERE student_id='$student' and quiz_id='$quiz_id'  ");	
			}
			else{//echo"reached insert ";	
				mysqli_query($con,"INSERT into student_marks (student_id,quiz_id,marks_sub,marks_obj) values('$student','$quiz_id','$total',0)");
			}
			header("location:teacher.php?q=6&quiz_id=$quiz_id");
		}
?>