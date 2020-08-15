<!-- Bernet Vincent Crud Application, check the READ-ME -->
<!-- Added some jQuerry to show and insert or not new Position (with a new table eponymic) and new Education (with 2 new tables, Institution and Education) -->


<!-- To begin with we call our pdo to link our php to our database, and we also call our utility php files, which is usefull for many functions. we end up by calling our session. -->
<?php
  require_once "pdo.php";
  require_once "utility.php";
  session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Bernet Vincent Add</title>
<!-- Personal Css file common for every page -->
<link rel="stylesheet" href="index.css">
<!-- Don't forget to call jQuerry librairy -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Bunch of optinal commodity to make it looks better -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

<meta charset="UTF-8" />
<body>
  <?php

    // First check if the user is logged in
    if (!isset($_SESSION['email']))
    {
      die("<div style='text-align:center;color:pink;weight:bold;font-size:35px;margin-top:10%;'>ACCESS DENIED<br> <a href='index.php'>Back to Index</a></div>");
    }

    // Second if the user requested cancel go back to index.php
    if (isset($_POST['cancel']))
    {
      header("Location: index.php");
      return;
    }

    // Third : Flash Message -> print the result of our login / add / Logout / register action
    flashMessages();

    // Fourth : If our add button have been pressed then we check values validity then via sql Querry we insert new data
    if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) )
    {
        // 1] Check if our regular form have valid data :
        $msg = validateProfile2();
        if (is_string($msg))
        {
          $_SESSION['message'] = $msg;
          header('Location: add.php');
          return;
        }
          // 2] Check now our position additionnal form
          $msg = validatePos();
          if (is_string($msg))
          {
            $_SESSION['message'] = $msg;
            header('Location: add.php');
            return;
          }

          // 3] Check now our education additionnal form
          $msg = validateEdu();
          if (is_string($msg))
          {
            $_SESSION['message'] = $msg;
            header('Location: add.php');
            return;
          }

          // Data are valid we can insert now, we use $_SESSION so there will be no more automatics pop ups generated by the browser when we reload the page (with "are you sure to resend the form ?" like message)
          $_SESSION["first_name"]=$_POST["first_name"];
          $_SESSION["last_name"]=$_POST["last_name"];
          $_SESSION["email"]=$_POST["email"];
          $_SESSION["headline"]=$_POST["headline"];
          $_SESSION["summary"]=$_POST["summary"];

          $sql = "INSERT INTO profile(user_id,first_name,last_name, email, headline, summary) VALUES (:user_id,:first_name,:last_name,:email,:headline, :summary)";
          $result1 = $pdo->prepare($sql);
          $result1->execute(array
          (
            ':user_id' => $_SESSION['user_id'],
            ':first_name' => htmlentities($_POST['first_name']),
            ':last_name' => htmlentities($_POST['last_name']),
            ':email' => htmlentities($_POST['email']),
            ':headline' => htmlentities($_POST['headline']),
            ':summary' => htmlentities($_POST['summary'])
          ));
          $profile_id=$pdo->lastInsertId();

          // Check utility.php to see those functions
          insertPositions($pdo, $profile_id);
          insertEducations($pdo, $profile_id);

          // Message of validation in SESSION then we redirect to index.php
          $_SESSION["message"]="<div style='color:green; text-align: center;'>Your new profile as been added</div>";
          header("Location: index.php");
          return;
      }

  ?>

  <!-- View part -->
  <?php
    echo('<div class="Titre" style="text-align:left;"> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
        Adding Profiles for '.$_SESSION["name"].'</div>');
        ?>
<!-- Our Add form -->
<div class="add-box">

  <form method="post">

    <div class="user-box">
      <p>first_name:
        <input type="text" name="first_name" size="30"/>
      </p>
    </div>

    <div class="user-box">
      <p>last_name:
        <input type="text" name="last_name" size="28"/>
      </p>
    </div>

    <div class="user-box">
      <p>email:
        <input type="text" name="email" size="31"/>
      </p>
    </div>

    <div class="user-box">
      <p>headline:
        <input type="text" name="headline" size="28"/>
      </p>
    </div>

    <div class="user-box">
      <p>summary:
        <textarea style ="background:#19273c;color:white;" name="summary" rows="3" cols="45" ></textarea>
      </p>
    </div>

    <div class="user-box">
      <p> School: &nbsp&nbsp&nbsp&nbsp&nbsp
        <button id = "addEdu" type="button">
            +
        </button>
      </p>
    </div>

    <div id="education_fields">
      <!-- Here we gonna add our education fields -->
    </div>

    <div class="user-box">
      <p> Position: &nbsp&nbsp
        <button id = "addPos" type="button" >
           +
        </button>
      </p>
    </div>

    <div id="position_fields">
      <!-- Here we gonna add our position fields -->
    </div>

    <!-- Our submit_box, those span tag are kind of desorienting i know, but they are just here to do some animation in css later (check index.css file) -->
    <a href="#">
      <span></span>
      <span></span>
      <span></span>
      <span></span>
      <input class = "myButton" type="submit" value="Add"/>
      <input class = "myButton" type="submit" name ="cancel" value="Cancel"/>
    </a>
  </form>


<script type="text/javascript">
  // When document is completly loaded : adding Clicking event, when the user click on "+", so we add a new field for education or position inputs
  // First for Position field
  countPos = 0;
  $(document).ready(function (){
    window.console && console.log("The dom is ready: Script begin");
    $("#addPos").click(function(event){
      event.preventDefault();
      if (countPos>=9)
      {
        alert("Maximum of nine position entries exceeded");
        return;
      }
      countPos++;

      // Kind of dirty code here, just to create new position related fields
      window.console && console.log('Adding position'+countPos);
      $('#position_fields').append(
        '<div id="position'+countPos+'"> \
        <input type="button" value="-" \
          onclick= "window.console && console.log(\'Removing position'+countPos+'\');countPos--;$(\'#position'+countPos+'\').remove();return false;"> Year: <input type="text" name="year'+countPos+'" value=""/>  \
          <textarea name="description'+countPos+'"style="background:#19273c;color:white;" rows="2" cols="45"></textarea>\
          </div><br>');

    }
  );

    // Now for Education field
    countEdu = 0;
    $("#addEdu").click(function(event){
      event.preventDefault();
      if (countEdu>=9)
      {
        alert("Maximum of nine education entries exceeded");
        return;
      }
      countEdu++;

      // Kind of dirty code here, just to create new education related fields
      window.console && console.log('Adding education'+countEdu);
      $('#education_fields').append(
        '<div id="education'+countEdu+'"> \
        <input type="button" value="-" \
          onclick= "window.console && console.log(\'Removing education'+countEdu+'\');countEdu--;$(\'#education'+countEdu+'\').remove();return false;"> Year: <input type="text" name="edu_year'+countEdu+'" value=""/>  \
          <textarea name="edu_school'+countEdu+'"style="background:#19273c;color:white;" rows="2" cols="45"></textarea>\
          </div><br>');

    }
  );

}
);
</script>
</div>

</body>
</html>
