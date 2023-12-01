<?php
   session_start();
	include("../../../Private/config.php");

   $err = $unameErr = $passErr = '';

   if($_SERVER["REQUEST_METHOD"] == "POST") {

      //returnerer databasetilkoblingserror
      if($con->connect_errno) {
         echo "Database forbindelse feilet!<BR>";
         echo "Feilmelding: ", $con->connect_error;
         exit();
      }

      //rengjør inputs
      $uname = validData($_POST["username"]);
      $pass = validData($_POST["password"]);

      //sjekker etter tomme felt
      if(empty($uname)) {
            $unameErr = "Vennligst skriv inn brukernavn!<BR>";
      }
      if(empty($pass)) {
            $passErr = "Vennligst skriv inn passord!<BR>";
      }
      $err =  $unameErr . $passErr;
      
      if(empty($err)) {
         //sender query for å hente info om bruker
         $sql = "SELECT * FROM users WHERE username=? and password=?";
         $stmt = $con->prepare($sql);

         //bytter ut "?" i query til variablene under (s -> type="string")
         $stmt->bind_param("ss", $uname, $pass);

         if($stmt->execute()) {

            $result = $stmt->get_result();

            //hvis query resultat ikke er tomt
            if($result->num_rows) {
               //setter log feltet til brukernavn for å holde seg innlogget
               $_SESSION['log'] = $uname;

               //sendes til hovedsiden
               header('Location: index.php');
               exit();
            } else {
               $err = "Feil brukernavn eller passord";
            }
         }
      }
   }
   $con->close();
?>


<HTML>
<HEAD>
   <link rel="stylesheet" href="../CSS/formStyles.css">
</HEAD>
<BODY>

<DIV class="form">
   <H2>Logg inn</H2>
   <FORM name="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

      <LABEL>Brukernavn
      <!-- rød stjerne over tomt felt-->
      <?php if(!empty($unameErr)) echo "<SPAN class=\"red\">*</SPAN>"; else echo "*"; ?>
      </LABEL><BR>
      <INPUT type="text" name="username" placeholder="Fyll ut brukernavn" ><BR>

      <LABEL>Passord
      <!-- rød stjerne over tomt felt-->
      <?php if(!empty($passErr)) echo "<SPAN class=\"red\">*</SPAN>"; else echo "*"; ?>
      </LABEL><BR>
      <INPUT type="text" name="password" placeholder="Fyll ut passord" ><BR>
      <BUTTON type="submit">Logg inn</BUTTON>

   </FORM>
   <?php
      //feilmelding(er) for bruker
      echo "<DIV class=\"red\">"; 
      if(isset($err)){
         echo $err;
      }
      echo "</DIV>";
   ?>
   <!-- link til registrering-->
   <P style="font-style: oblique;">Har du ikke bruker? <a href="registrer.php"><strong>Registrer deg!</strong></a></P>
</DIV>

</BODY>
</HTML>