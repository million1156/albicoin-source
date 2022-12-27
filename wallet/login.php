<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel='stylesheet' href='//coin.funwithalbi.xyz/style.css' />
  <title>Wallet Login</title>
</head>
<body>
  <h1>&nbsp; Albicoin</h1>
  <a class='signup' href='//coin.funwithalbi.xyz/wallet/signup.php'>Create Wallet</a> &nbsp;
  <a class='login' href='//coin.funwithalbi.xyz/wallet/login.php'>Wallet Login</a> &nbsp;
  <a class='home' href='//coin.funwithalbi.xyz/'>Home</a> &nbsp;
  <a class='manage' href='//coin.funwithalbi.xyz/wallet/manage.php'>Manage</a>

  <br/><br/><br/>
  <center>
    <form method='post'>
      Wallet: <br/><br/>
      <input type='text' name='wallet' autocomplete="off" /> <br/><br/>
      Wallet Password: <br/><br/>
      <input type='password' name='walletpassword' /> <br/><br/>
      <button type='submit' name='login' value='yes'>Login</button>
    </form>
  
    <?php
      if (isset($_POST['login']))
      {
        $db = new PDO("sqlite:../database.sqlite");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pass = $_POST['walletpassword'];
        $wallet = $_POST['wallet'];
  
        $res = $db->exec("CREATE TABLE IF NOT EXISTS wallets (
              id TEXT,
              password TEXT
        );");
        
        $walleta = $db->prepare("SELECT * FROM wallets WHERE id=:id");
        
        $values = [
          ':id' => $wallet
        ];
        $success = $walleta->execute($values);
        $walletd = $walleta->fetch();
        $walleta->closeCursor();
  
        if ($walletd)
        {
          if (password_verify($pass, $walletd['password']))
          {
            $_SESSION['wallet'] = $wallet;
            echo '<p style="color:green;">Successfully logged in to wallet!</p>';
          }
          else
          {
            echo '<p style="color:red;">Incorrect password!</p>';
          }
        }
        else
        {
          echo '<p style="color:red;">There was an error while logging in to your wallet.</p>';
        }
        
        $db = null;
      }
    ?>
  </center>
</body>
</html>