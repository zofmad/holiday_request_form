<?php

require 'vendor/autoload.php';

require_once 'src/connection.php';

use rtb\Employee;

?>
<!DOCTYPE html>

<html>
<head>
  <title>Holiday request form</title>
</head>
<body>

<?php

echo "
<h1>Holiday request form</h1>


<form action='' method='POST'>
     Select Your name:

    <select name='e_id' required>
                <option selected disabled>Choose name</option>";

var_dump(Employee::class);
$employees = Employee::loadALLEmployees($conn);
var_dump($employees);
for($i = 0; $i < count($employees); $i++){
    echo "<option value='$i'>".$employees[$i]->getName()."</option>";
}
echo "
    </select>
    <br>
    Start of holiday:
    <input type='date' name='holiday_start' placeholder='rrrr-mm-dd' max='32767-12-31' required>
    <br>
    End of holiday:
    <input type='date' name='holiday_end' placeholder='rrrr-mm-dd' max='32767-12-31' required>
    <br>
    Confirm Your vacation request with Your e-mail:
    <input type='email' name='e_email' placeholder='Type email' required>
    <br>
    <input type='submit' value='Send holiday request'>
</form>
";

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */




if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['e_id'])
        && isset($_POST['holiday_start']) && isset($_POST['holiday_end'])
        && isset($_POST['e_email'])){

    $id = is_numeric($_POST['e_id']) ? htmlspecialchars($conn->real_escape_string($_POST['e_id'])) : null;

    $holidayStartCheck = explode("-", htmlspecialchars($conn->real_escape_string($_POST['holiday_start'])));
    if(count($holidayStartCheck) == 3){
      $holidayStart = checkdate((int)$holidayStartCheck[1],(int)$holidayStartCheck[2],(int)$holidayStartCheck[0]) ?
      htmlspecialchars($conn->real_escape_string($_POST['holiday_start'])) : null ;

    }else{
      $holidayStart = null;
    }
    $holidayEndCheck = explode("-", htmlspecialchars($conn->real_escape_string($_POST['holiday_end'])));

    if(count($holidayEndCheck) == 3){
      $holidayEnd = checkdate((int)$holidayEndCheck[1],(int)$holidayEndCheck[2], (int)$holidayEndCheck[0]) ?
      htmlspecialchars($conn->real_escape_string($_POST['holiday_end'])) : null ;
    }else{
      $holidayEnd = null;
    }




    $email = is_string($_POST['e_email']) ? htmlspecialchars($conn->real_escape_string($_POST['e_email'])) : null;



    if($id != null && $holidayEnd && $holidayStart && $holidayStart <= $holidayEnd && $email){

      $employee = Employee::getEmployeeById($conn, $id);

      // if($employee->getEmail() == $email)
      // echo "Correct email. <br>";

      $holidayDaysLeft = $employee->getHolidayDaysLeft();
      $dateTimestamp = strtotime($holidayStart);


      $nbOfHolidayDays = 0;

      while($dateTimestamp <= strtotime($holidayEnd)){
        //parametr "l" -reprezentacja słowna dnia tygodnia
        if(!(date( "l", $dateTimestamp) == 'Sunday' || date( "l", $dateTimestamp) == 'Saturday')){
            $nbOfHolidayDays++;
        }



        $dateTimestamp = strtotime("+1 day",  $dateTimestamp);
      }


      $daysDifference = $holidayDaysLeft - $nbOfHolidayDays;

      //aktualizacja bazy danych
      if($daysDifference > 0){
          $employee = $employee->setHolidayDaysLeft($daysDifference);
      }else{
          $employee = $employee->setHolidayDaysLeft(0);

      }
      if($employee){
          if($employee = $employee->updateEmployee($conn)){
              $saveToDB = true;
              $name = $employee->getName();
          }
          else{
            echo "Error saving to database: ".$conn->error."<br>";
            $saveToDB = false;
          }
      }


      //wysylka

      $mailBody = "Hello $name.\nYour holiday request is confirmed. You have requested $nbOfHolidayDays days of paid leave.\n";
      if($daysDifference >= 0){

        $mailBody .= "Now You have $daysDifference days of paid leave left.\n";

      //plain txt as an attachment to html txt mail

      } else {

        $mailBody .= "Unfortunately, You don't have enough days of paid leave. The difference: ".abs($daysDifference)." days ".
          "will be treated as unpaid leave.\n";
      }
      $mailBody .= "";



      if($saveToDB){

        // $transport = (new Swift_SmtpTransport('mail.cba.pl', 587, "ssl"))

        $transport = (new Swift_SmtpTransport('mail.cba.pl', 587))
          ->setUsername('zmadej@zmadej.cba.pl')
          ->setPassword('zmadejRTB1');
          // Create the Mailer using created Transport
          $mailer = new Swift_Mailer($transport);

          $message = (new Swift_Message("Your holiday request-$name"))
            ->setFrom(['zmadej@zmadej.cba.pl' => 'Firm administration-holiday requests'])
            ->setTo(["$email"=> "$name"])
            ->setBody("$mailBody");

          //sending message
          if($result = $mailer->send($message)){
            echo "Confirmation message has been sent to given email. Please check Your mailbox.<br>";
          } else {
            echo "Error sending the email. Please try again";
          }


      }
        // else{
        //   echo "Incorrect email. Holiday request not send. Try again.<br>";
        // }
      } else {
      $error = '';
      if(!$id){
        $error .= "You didn't choose correct name<br>";
      }
      if(!$holidayStart){
        $error .= "Wrong date of holiday start: ".implode("-", $holidayStartCheck)."<br>";
      }
      if(!$holidayEnd){
        $error .= "Wrong date of holiday end: ".implode("-", $holidayEndCheck)."<br>";
      }
      if(!($holidayStart <= $holidayEnd)){
        $error .= "Date of holiday start (".implode("-", $holidayStartCheck).")".
        " is greater than date of holiday end (".implode("-", $holidayEndCheck).")<br>";
      }
      if(!$email){
        $error .= "Wrong email format<br>";
      }

      echo "<p style='background-color: red'>$error</p><br>";
      echo "Try again.";
    }


}



$conn->close();
$conn = null;
?>



</body>
</html>
