<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel='stylesheet' href='//coin.funwithalbi.xyz/style.css' />
  <title>Create Wallet</title>
</head>
<body>
  <h1>&nbsp; Albicoin</h1>
  <a class='signup' href='//coin.funwithalbi.xyz/wallet/signup.php'>Create Wallet</a> &nbsp;
  <a class='login' href='//coin.funwithalbi.xyz/wallet/login.php'>Wallet Login</a> &nbsp;
  <a class='home' href='//coin.funwithalbi.xyz/'>Home</a> &nbsp;
  <a class='manage' href='//coin.funwithalbi.xyz/wallet/manage.php'>Manage</a>

  <br/><br/><br/><br/>
  <center>
    <form method='post'>
      Wallet Password: <br/><br/>
      <input type='password' name='walletpassword' /> <br/><br/>
      <button type='submit' name='create' value='yes'>Create</button>
    </form>
  
    <?php
      function rt($l)
      {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($n = 1; $n <= $l; $n++)
        {
          $result .= $chars[rand(0, strlen($chars))];
        }
        return $result;
      }
  
      if (isset($_POST['create']))
      {
        $db = new PDO("sqlite:../database.sqlite");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pass = $_POST['walletpassword'];
        $password = password_hash($pass, PASSWORD_DEFAULT);
  
        $res = $db->exec("CREATE TABLE IF NOT EXISTS wallets (
              id TEXT,
              password TEXT
        );");
        
        $wallet = $db->prepare("INSERT INTO wallets (id, password) VALUES (:id, :password)");
        $token = rt(32);
        
        $values = [
          ':id' => $token,
          ':password' => $password
        ];
        $success = $wallet->execute($values);
        $wallet->closeCursor();
  
        if ($success)
        {
          echo '<p style="color:green;">Successfully made a wallet!</p><p style="color:green">ADDRESS: <b>'.$token.'</b></p>';
        }
        else
        {
          echo '<p style="color:red;">There was an error while making your wallet.</p>';
        }
        
        $db = null;
      }
    ?>
  </center>
</body>
</html>