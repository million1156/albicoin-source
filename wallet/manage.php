<?php
session_start();

require_once(dirname(__FILE__) . '/../config.php');

if (!isset($_SESSION['wallet']))
{
  header("Location: //coin.funwithalbi.xyz/wallet/login.php");
}

$db = new PDO("sqlite:../database.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function validate_block($nonce, $hash, $maxval, $value, $transactions, $uid) {
  $target = "$nonce|$maxval|$value|$transactions|$uid|alb";
  $hashed = hash('SHA256', $target);
  return $hashed == $hash;
}

$wall = $db->prepare('SELECT * FROM wallets WHERE id=:id');
$wall->execute([':id' => $_SESSION['wallet']]);
$wallet = $wall->fetch();
$wall->closeCursor();

$bloc = $db->prepare('SELECT * FROM blocks');
$bloc->execute();
$blocks = $bloc->fetchAll();
$bloc->closeCursor();

$balance = 0.0000000;

foreach ($blocks as $block)
{
  $transactions = json_decode($block['transactions'], true);

  if ($block['mined'] == '0')
  {
    if (validate_block($block['nonce'], $block['hash'], explode("|", $block['target'])[1],
                        explode("|", $block['target'])[2], $block['transactions'],  explode("|", $block['target'])[4]))
    {
      foreach ($transactions as $transaction)
      {
        if ($transaction['to'] == $_SESSION['wallet'])
        {
          $balance = $balance + floatval($transaction['amount']);
        }
        elseif ($transaction['from'] == $_SESSION['wallet'])
        {
          $balance = $balance - floatval($transaction['amount']);
        }
      }
    }
  }
  else
  {
    foreach ($transactions as $transaction)
    {
      if ($transaction['to'] == $_SESSION['wallet'])
      {
        $balance = $balance + floatval($transaction['amount']);
      }
      elseif ($transaction['from'] == $_SESSION['wallet'])
      {
        $balance = $balance - floatval($transaction['amount']);
      }
    }
  }
}

if (isset($_POST['amt']) && isset($_POST['to']))
{
  $amt = floatval($_POST['amt']);
  $too = $_POST['to'];

  function stw($string, $startString)
  {
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
  }
  
  if ($amt <= $balance)
  {
    $toa = $db->prepare("SELECT * FROM wallets WHERE id=:id");
    $toa->execute([':id' => $too]);
    $to = $toa->fetch();
    $toa->closeCursor();

    if ($to)
    {
      $sent = true;

      try
      {
        if ($amt > 0.00000001 && $amt <= 25000)
        {
          foreach ($blocks as $block)
          {
            $transactions = json_decode($block['transactions'], true);
            foreach ($transactions as $transaction)
            {
              if ($transaction['from'] == $_SESSION['wallet'] || $transaction['to'] == $_SESSION['wallet'])
              {
                $tests = $block;
                break;
              }
            }
          }
          $yas = json_decode($tests['transactions'], true);
          array_push($yas, array('amount' => $amt, 'from' => $_SESSION['wallet'], 'to' => $to['id']));
          $tests['transactions'] = json_encode($yas);

          $trs = json_decode($tests['transactions'], true);
          $trs = json_encode($trs);

          if ($tests['mined'] == '0')
          {
            for ($n = 1; $n <= 100000000; $n++)
            {
              $lmao = explode('|', $tests['target']);
              $valll = $lmao[2];
              $id = $lmao[4];
              $target = "$n|1|$valll|$trs|$id|alb";
              $hash = hash('SHA256', $target);
              if (stw($hash, str_repeat('0', $difficulty)))
              {
                $letsgo = $db->prepare("UPDATE blocks SET hash=:hash, target=:target, nonce=:nonce, transactions=:trs WHERE hash=:id");
                $letsgo->execute([
                   ':hash' => $hash,
                   ':target' => $target,
                   ':nonce' => $n,
                   ':trs' => $trs,
                   ':id' => $tests['hash']
                ]);
                break;
              }
              $n++;
            }
          }
          else
          {
            $letsgo = $db->prepare("UPDATE blocks SET transactions=:trs WHERE hash=:id");
            $letsgo->execute([
               ':trs' => $trs,
               ':id' => $tests['hash']
            ]);
          }
        }
        else
        {
          $sent = false;
        } 
      }
      catch (PDOException $e)
      {
        $sent = false;
      }
      
      if ($sent == false)
      {
        echo '<p style="color:red;">An error occured!</p>';
      }
      else
      {
        echo "<p style='color:green;'>Successfully sent $amt AlbiCoin!</p>";
      }
    }
    else
    {
      echo '<p style="color:red;">Invalid wallet address!</p>';
    }
  }
  else
  {
    echo '<p style="color:red;">Not enough AlbiCoin!</p>';
  }
}


$bloc = $db->prepare('SELECT * FROM blocks');
$bloc->execute();
$blocks = $bloc->fetchAll();
$bloc->closeCursor();

$balance = 0.0000000;

foreach ($blocks as $block)
{
  $transactions = json_decode($block['transactions'], true);

  if ($block['mined'] == '0')
  {
    if (validate_block($block['nonce'], $block['hash'], explode("|", $block['target'])[1],
                        explode("|", $block['target'])[2], $block['transactions'],  explode("|", $block['target'])[4]))
    {
      foreach ($transactions as $transaction)
      {
        if ($transaction['to'] == $_SESSION['wallet'])
        {
          $balance = $balance + floatval($transaction['amount']);
        }
        elseif ($transaction['from'] == $_SESSION['wallet'])
        {
          $balance = $balance - floatval($transaction['amount']);
        }
      }
    }
  }
  else
  {
    foreach ($transactions as $transaction)
    {
      if ($transaction['to'] == $_SESSION['wallet'])
      {
        $balance = $balance + floatval($transaction['amount']);
      }
      elseif ($transaction['from'] == $_SESSION['wallet'])
      {
        $balance = $balance - floatval($transaction['amount']);
      }
    }
  }
}

$usd = $balance * $ac_value;
$usd = number_format($usd, 7);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel='stylesheet' href='//coin.funwithalbi.xyz/style.css?version=<?php echo time(); ?>' />
  <title>Manage Wallet</title>
</head>
<body>
  <h1>&nbsp; Albicoin</h1>
  <a class='signup' href='//coin.funwithalbi.xyz/wallet/signup.php'>Create Wallet</a> &nbsp;
  <a class='login' href='//coin.funwithalbi.xyz/wallet/login.php'>Wallet Login</a> &nbsp;
  <a class='home' href='//coin.funwithalbi.xyz/'>Home</a> &nbsp;
  <a class='manage' href='//coin.funwithalbi.xyz/wallet/manage.php'>Manage</a>
  <br/><br/><br/>
  <center>
    <h1>Wallet: <?php echo $_SESSION['wallet']; ?></h1><br/>
    <h1>Balance: &nbsp; <?php echo number_format($balance, 7) . " ($$usd)"; ?></h1><br><br><br>

    <form method='post'>
      <h2>Send AlbiCoin:</h2>
      <input type='text' name='to' placeholder='Wallet Of Receiver' /> <br/><br/>
      <input type='number' name='amt' placeholder='Amount' step='0.000000001' max='25000' /> <br/><br/>
      <button type='submit'>Go</button>
    </form>
    <br/><br/><br/>
    <form method='post'>
      <h2>Request AlbiCoin:</h2>
      <input type='number' step='0.00001' max='2500' name='thing' placeholder='Amount' /><br/><br/>
      <button type='submit' name='yes' value='thetitlesaysitall'>Request</button>
      <?php
        if (isset($_POST['yes']))
        {
          if ($_POST['thing'] <= 2500 && $_POST['thing'] >= 0.00001)
          {
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

            $idthing = rt(35);
            
            $seque = $db->prepare("INSERT INTO requests (too, paid, amount, paydate, id) VALUES (:too, 0, :amt, 1, :id)");
            $seque->execute([':too' => $_SESSION['wallet'], ':amt' => $_POST['thing'], ':id' => $idthing]);
            $seque->closeCursor();

            echo "<p style='color:green;'>ADDRESS: $idthing</p>";
          }
        }
      ?>
    </form>
  </center>
</body>
</html>