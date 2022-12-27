<?php
session_start();

if (!isset($_SESSION['wallet']))
{
  header('Location: ./login.php');
}

$exist = false;
$amount = 0.00;
$paid = false;
$to = 'alb00000000000000000';

$id = null;
$exists = null;

$db = new PDO("sqlite:../database.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id']))
{
  $wallet = $_SESSION['wallet'];
  $id = $_GET['id'];
  
  $ex = $db->prepare('SELECT * FROM requests WHERE id=:id');
  $ex->execute([':id' => $id]);
  $exists = $ex->fetch();
  $ex->closeCursor();

  if ($exists)
  {
    $exist = true;
    if ($exists['paid'] == 1)
    {
      $paid = true;
    }
    else
    {
      $to = $exists['too'];
      $amount = $exists['amount'];
    }
  }
}

include '../config.php';

function pay($to, $amt, $from, $pass, $id)
{
  $db = new PDO("sqlite:../database.sqlite");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $bloc = $db->prepare("SELECT * FROM blocks");
  $bloc->execute();
  $blocks = $bloc->fetchAll();
  $bloc->closeCursor();

  function stw($string, $startString)
  {
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
  }
  
  function validate_block($nonce, $hash, $maxval, $value, $transactions, $uid) {
    $value = substr($value, 0, 7);
    $target = "$nonce|$maxval|$value|$transactions|$uid|alb";
    $hashed = hash('SHA256', $target);
    return stw($hashed, str_repeat('0', $difficulty));
  }

  function getinput($nonce, $hash, $maxval, $value, $transactions, $uid) {
    $value = substr($value, 0, 7);
    $target = "$nonce|$maxval|$value|$transactions|$uid|alb";
    return $target;
  }
  
  $balance = 0.0000000;
  
  foreach ($blocks as $block)
  {
    $transactions = json_decode($block['transactions'], true);

    foreach ($transactions as $transaction)
    {
      $hash = hash('SHA256', getinput($block['nonce'], $block['hash'], 1, explode('|', $block[''])[2], $block['transactions'], explode('|', $block[''])[4]));
      
      if (validate_block($block['nonce'], $block['hash'], 1, explode('|', $block[''])[2], $block['transactions'], explode('|', $block[''])[4]) && stw($hash, str_repeat('0', $difficulty)))
      {
        if ($transaction['to'] == $from)
        {
          $balance = $balance + $transaction['amount'];
        }
        elseif ($transaction['from'] == $from)
        {
          $balance = $balance - $transaction['amount'];
        }
      }
    }
  }

  if ($balance >= $amt)
  {
    $yes = null;
    
    foreach ($blocks as $block)
    {
      $transactions = json_decode($block['transactions'], true);
  
      foreach ($transactions as $transaction)
      {
        $hash = hash('SHA256', getinput($block['nonce'], $block['hash'], 1, explode('|', $block[''])[2], $block['transactions'], explode('|', $block[''])[4]));
        
        if (validate_block($block['nonce'], $block['hash'], 1, explode('|', $block[''])[2], $block['transactions'], explode('|', $block[''])[4]) && stw($hash, str_repeat('0', $difficulty)))
        {
          $yes = $block;
          break;
        }
      }
    }

    if ($yes != null)
    {
      $yes['transactions'] = json_decode($yes['transactions'], true);
      array_push($yes['transactions'], array('amount' => $amt, 'from' => $from, 'to' => $to));
      $yes['transactions'] = json_encode($yes['transactions']);

      $sequel = $db->prepare("UPDATE blocks SET transactions=:trs WHERE hash=:hash");
      $sequel->execute([':trs' => $yes['transactions'], ':hash' => $yes['hash']]);
      $sequel->closeCursor();
      
      $seque = $db->prepare("UPDATE requests SET paid=1, paydate=:pd WHERE id=:id");
      $seque->execute([':pd' => time(), ':id' => $id]);
      $seque->closeCursor();
    }
    return true;
  }
  else
  {
    return false;
  }
}

$exist = false;
$amount = 0.00;
$paid = false;
$to = 'alb00000000000000000';

$exists = null;

if (isset($_GET['id']))
{
  $wallet = $_SESSION['wallet'];
  $id = $_GET['id'];
  
  $ex = $db->prepare('SELECT * FROM requests WHERE id=:id');
  $ex->execute([':id' => $id]);
  $exists = $ex->fetch();
  $ex->closeCursor();

  if ($exists)
  {
    $exist = true;
    if ($exists['paid'] == 1)
    {
      $paid = true;
    }
    else
    {
      $to = $exists['too'];
      $amount = $exists['amount'];
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel='stylesheet' href='//coin.funwithalbi.xyz/style.css?version=<?php echo time(); ?>' />
  <title>Pay</title>
</head>
<body>
  <?php if ($exists == true)
{ ?>
        <?php if ($paid == false)
    { ?>
        <h1>Pay <b style='color:green;'><?php echo number_format($amount, 7); ?></b> ALC to <b style='color:green;'><?php echo $to; ?></b></h1>
        <form method='post'>
          <input type='text' name='pm' placeholder='Payment Method Wallet Address' />
          <input type='password' name='pass' placeholder='Wallet Password' />
          <button type='submit' name='paying' value='mhm'>Pay</button>
        </form>
                                

<?php
                                   
          if (isset($_POST['paying']))
          {
            $from = $_POST['pm'];
            $password = $_POST['pass'];
            
            if (pay($to, number_format($amount, 7), $from, $password, $id))
            {
              echo '<h2 style="color:green;">Payment successfull!</h2>';
            }
            else
            {
              echo '<h2 style="color:red;">Payment not completed!</h2>';
            }
          }
    } else {
        ?>
      <h1>Request already paid!</h1>
  <?php
  } ?>
 <?php } else { ?>
        <h1>Request Does Not Exist!</h1>
               <?php
              }
  $db = null;
  ?>
</body>
</html>