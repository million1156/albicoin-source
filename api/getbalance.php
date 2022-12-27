<?php
require_once('../config.php');

if (isset($_GET['wallet']) && htmlspecialchars($_GET['wallet']) == $_GET['wallet'])
{
  $address = $_GET['wallet'];
  $db = new PDO("sqlite:../database.sqlite");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  function stw($string, $startString)
  {
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
  }
  
  function validate_block($nonce, $hash, $maxval, $value, $transactions, $uid) {
    $target = "$nonce|$maxval|$value|$transactions|$uid|alb";
    $hashed = hash('SHA256', $target);
    return $hashed == $hash && stw($hashed, str_repeat('0', $difficulty));
  }
  
  $wall = $db->prepare('SELECT * FROM wallets WHERE id=:id');
  $wall->execute([':id' => $address]);
  $wallet = $wall->fetch();
  $wall->closeCursor();
  
  $bloc = $db->prepare('SELECT * FROM blocks');
  $bloc->execute();
  $blocks = $bloc->fetchAll();
  $bloc->closeCursor();
  
  $balance = 0.0000000;

  if ($wallet)
  {
    foreach ($blocks as $block)
    {
      $transactions = json_decode($block['transactions'], true);
    
      if (validate_block($block['nonce'], $block['hash'], explode("|", $block['target'])[1],
                          explode("|", $block['target'])[2], $block['transactions'],  explode("|", $block['target'])[4]))
      {
        foreach ($transactions as $transaction)
        {
          if ($transaction['to'] == $address)
          {
            $balance = $balance + floatval($transaction['amount']);
          }
          elseif ($transaction['from'] == $address)
          {
            $balance = $balance - floatval($transaction['amount']);
          }
        }
      }
    }
    $json = [
      "wallet" => $address,
      "balance" => str_replace(',', '', number_format($balance, 7))
    ];
    
    echo json_encode($json);
  }
  else
  {
    $json = [
      "code" => 404,
      "error" => "invalid wallet"
    ];
  
    echo json_encode($json);
  }
  
  $db = null;
}
else
{
  $json = [
    "code" => 400,
    "error" => "bad request"
  ];
  
  echo json_encode($json);
}
?>