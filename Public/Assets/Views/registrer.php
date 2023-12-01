<?php
   session_start();
	include("../../../Private/config.php");

   //hvis inlogget -> hovedsiden
   if(isset($_SESSION['log'])) {
      header('Location: ../index.php');
      exit();
   }
   else {
      if($_SERVER["REQUEST_METHOD"] == "POST") {         
         //sjekker databaseforbindelse
         if($con->connect_errno) {
            echo "Database forbindelse feilet!<BR>";
            echo "Feilmelding: ", $con->connect_error;
            exit();
         }
         
         //initierer variabler
         $fname = $lname = $uname = $email = $pass = $phonenr = "";
         $fnameErr = $lnameErr = $unameErr = $emailErr = $passErr = "";
         
         //fjerner tags, mellomrom og tegn fra parameter. Se config.php for funksjonen.
         $fname = validData($_POST["firstname"]);
         $lname = validData($_POST["lastname"]);
         $uname = validData($_POST["username"]);
         $email = validData($_POST["email"]);
         $pass = validData($_POST["password"]);
         $phonenr = validData($_POST["phonenr"]);
         
         //sjekker tomme felt / feil input
         if(empty($fname)) {
            $fnameErr = "Fornavn feltet var tomt!<BR>";
         }
         if(empty($lname)) {
            $lnameErr = "Etternavn feltet var tomt!<BR>";
         }
         if(empty($uname)) {
            $unameErr = "brukernavn feltet var tomt!<BR>";
         }
         if(empty($email)) {
            $emailErr = "E-post feltet var tomt!<BR>";
         }
         if(empty($pass)) {
            $passErr = "Passord feltet var tomt!<BR>";
         }
         if(strlen($uname)<6) {
            $unameErr .= "Brukernavn må ha minimum 6 tegn!<BR>";
         }
         if(strlen($pass)<6) {
            $passErr .= "Passord må ha minimum 6 tegn!!<BR>";
         }
         if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr .= "Bruk en gyldig e-post adresse!<BR>";
         }

         //alle errormeldinger
         $totErr = $fnameErr . $lnameErr . $unameErr . $emailErr . $passErr;

         //hvis error
         if(!empty($totErr)){
            $err = "Prøv på nytt";
         } else {
            //sql query
            $sql = "INSERT INTO `users`(`firstname`, `lastname`, `username`, `password`, `email`, `phonenr`)
            VALUES (?, ?, ?, ?, ?, ?)";
         
            //gjør klar queryen
            $stmt = $con->prepare($sql);
            //bytter ut "?" i queryen over med verdiene våre. (sssssi står for typen av alle parameterene, s = string, i = int)
            $stmt->bind_param("sssssi", $fname, $lname, $uname, $pass, $email, $phonenr);

            if($stmt->execute()) {
               //lagrer innlogget bruker i sesjonen
               $_SESSION['log'] = $uname;
               header('Location: index.php');
               exit();
            }
            else {
               $execErr = "Noe gikk galt <BR> Prøv igjen!";
            }
         }
         $con->close();
      }
   }
?>



<HTML>
<HEAD>
   <link rel="stylesheet" href="../CSS/formStyles.css">
</HEAD>
<BODY>

<!-- Registrerings form-->
<DIV class="form">
   <H2>Registrer en bruker!</H2>
   <FORM name="register" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

      <LABEL>Fornavn
      <!-- asterisk for felt som er kreves, rød hvis tomt -->
      <?php if(!empty($fnameErr)) echo "<SPAN class=\"red\">*</SPAN>"; else echo "*"; ?>
      </LABEL><BR>
      <INPUT type="text" name="firstname" placeholder="Ola" value="<?php echo isset($_POST['firstname']) ? $_POST['firstname'] : ""; ?>" ><BR>

      <LABEL>Etternavn
      <?php if(!empty($lnameErr)) echo "<SPAN class=\"red\">*</SPAN>"; else echo "*"; ?>
      </LABEL><BR>
      <input type="text" name="lastname" placeholder="Nordmann" value="<?php echo isset($_POST['lastname']) ? $_POST['lastname'] : ""; ?>" ><BR>

      <LABEL>Brukernavn
      <?php if(!empty($unameErr)) echo "<SPAN class=\"red\">*</SPAN>"; else echo "*"; ?>
      </LABEL><BR>
      <INPUT type="text" name="username" placeholder="brukernavn" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ""; ?>" ><BR>

      <LABEL>Passord
      <?php if(!empty($passErr)) echo "<SPAN class=\"red\">*</SPAN>"; else echo "*"; ?>
      </LABEL><BR>
      <INPUT type="text" name="password" placeholder="passord" ><BR>

      <LABEL>E-post
      <?php if(!empty($emailErr)) echo "<SPAN class=\"red\">*</SPAN>"; else echo "*"; ?>
      </LABEL><BR>
      <INPUT type="text" name="email" placeholder="OlaNordmann@mail.no" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ""; ?>" ><BR>

      <LABEL>Mobilnummer</LABEL><BR>
      <INPUT type="text" name="phonenr" placeholder="00000000" value="<?php echo isset($_POST['phonenr']) ? $_POST['phonenr'] : ""; ?>"><BR>

      <BUTTON type="submit">Register</BUTTON>
   </FORM>

   <!-- Feilmeldinger og krav på bunnen av formen-->
   <?php 
      if(isset($err)) {
         echo "<DIV class=\"red\">";
            echo $totErr;
         echo "</DIV>";
      }
      elseif(isset($execErr)){
         echo $execErr;
      }
      else {
         echo "<P><B>Krav: </B> Brukernavn og passord må være mer enn 5 tegn.<BR>";
         echo "Asterisk (*) Felt må bli utfylt.<BR>";
         echo "Spesielle tegn kan ikke bli brukt.</P>";
      }
   ?>
   <!-- href login-->
   <P style="font-style: oblique;"> Har du allerede bruker? <a href="login.php"><strong>Logg inn</strong></a></P>
</DIV>

</BODY>
</HTML>
