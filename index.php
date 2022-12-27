<?php
require_once(dirname(__FILE__) . '/config.php');
?>

<html>
  <head>
    <link rel='stylesheet' href='//coin.funwithalbi.xyz/style.css?version=<?php echo time(); ?>' />
    <title>AlbiCoin</title>
  </head>
  <body>
    <h1>&nbsp; Albicoin</h1>
    <a class='signup' href='//coin.funwithalbi.xyz/wallet/signup.php'>Create Wallet</a> &nbsp;
    <a class='login' href='//coin.funwithalbi.xyz/wallet/login.php'>Wallet Login</a> &nbsp;
    <a class='home' href='//coin.funwithalbi.xyz/'>Home</a> &nbsp;
    <a class='manage' href='//coin.funwithalbi.xyz/wallet/manage.php'>Manage</a>
    <center>
      <br/><br/><br/>
      <h1>Current Value:</h1>
      <h2>$<?php echo number_format($ac_value, 7, '.', ''); ?> per coin</h2>
      <img width='850' height='500' src='//coin.funwithalbi.xyz/cdn/logo.png'>
    </center>
  </body>
</html>
<!--
you can make email authentication
using sendgrid api
https://sendgrid.com/

hmmmmmmmmmmmmmmmm

damn i use twilio authy
(especially since it has a pc app)
check discord ok
https://authy.com/download/ its this right?
ok lemme download 1 sec
IT DOES?
-->