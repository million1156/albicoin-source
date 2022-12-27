<?php
$db = new PDO("sqlite:../database.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['nonce']) && isset($_GET['to']) && isset($_GET['id']) && isset($_GET['hash']) && htmlspecialchars($_GET['to']) == $_GET['to'])
{
  $seed = crc32($_GET['id']);
  srand($seed);
  
  function stw($string, $startString)
  {
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
  }

  function randf($min, $max)
  {
    return rand($min, $max - 1) + (rand(0, PHP_INT_MAX - 1) / PHP_INT_MAX );
  }

  function validate_block($nonce, $hash, $maxval, $value, $transactions, $uid) {
    $value = substr($value, 0, 7);
    $target = "$nonce|$maxval|$value|$transactions|$uid|alb";
    $hashed = hash('SHA256', $target);
    return $hashed == $hash && stw($hashed, str_repeat('0', $difficulty));
  }

  function getinput($nonce, $hash, $maxval, $value, $transactions, $uid) {
    $value = substr($value, 0, 7);
    $target = "$nonce|$maxval|$value|$transactions|$uid|alb";
    return $target;
  }
  
  include '../config.php';

  if (strlen($_GET['id']) == 8)
  {
    $id = $_GET['id'];
    $nonce = $_GET['nonce'];
    $to = $_GET['to'];
    $hash = $_GET['hash'];

    $vm = randf(0, 25);
    $vm = substr($vm, 0, 7);

    $trans = array(
      [
        'amount' => $vm,
        'from' => 'alb00000000000000000',
        'to' => $to
      ]
    );
    
    if (validate_block($nonce, $hash, 1, $vm, json_encode($trans), $id) == true)
    {
      $uhm = $db->prepare("SELECT * FROM blocks WHERE target LIKE :id");
      $uhm->execute([':id' => '%'.$id.'%']);
      $invalid = $uhm->fetch();
      $uhm->closeCursor();

      $target = getinput($nonce, $hash, 1, $vm, json_encode($trans), $id);

      if ($invalid == false)
      {
        $letsgo = $db->prepare("INSERT INTO blocks (hash, target, nonce, transactions, mined) VALUES (:hash, :target, :nonce, :transactions, 1)");
        $letsgo->execute([
           ':hash' => $hash,
           ':target' => $target,
           ':nonce' => $nonce,
           ':transactions' => json_encode($trans)
        ]);

        $json = [
          'code' => 200,
          'error' => "successfully mined block",
          'sent' => $to,
          'amount' => $vm,
          'seed' => $seed,
          'lmao' => stw($hashed, str_repeat('0', $difficulty))
        ];
      
        echo json_encode($json);
      }
      else
      {
        $json = [
          'code' => 123,
          'error' => "block already exists"
        ];
      
        echo json_encode($json);
      }
    }
    else
    {
      $fail = getinput($nonce, $hash, 1, $vm, json_encode($trans), $id);
      $hmm = hash('SHA256', $fail);
      
      $json = [
        'code' => 403,
        'hashfail' => $fail,
        'hashed' => $hmm,
        'error' => "invalid block"
      ];
    
      echo json_encode($json);
    }
  }
  else
  {
    $json = [
      'code' => 1,
      'error' => 'id not long enough'
    ];
    
    echo json_encode($json);
  }
}
elseif (isset($_GET['getid']) && isset($_GET['wallet']) && htmlspecialchars($_GET['wallet']) == $_GET['wallet'])
{
  include '../config.php';

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

  function randf($min, $max)
  {
    return rand($min, $max - 1) + (rand(0, PHP_INT_MAX - 1) / PHP_INT_MAX );
  }

  $id = rt(8);
  $seed = crc32($id);
  
  srand($seed);

  $vall = randf(0, 25);
  $vall = number_format($vall, 7, '.', '');
  $vall = strval($vall);
  
  $wallet = $_GET['wallet'];

  $json = [
    'id' => $id,
    'value' => $vall,
    'seed' => $seed,
    'transactions' => [
      'amount' => $vall,
      'from' => 'alb00000000000000000',
      'to' => $wallet
    ],
    'difficulty' => $difficulty
  ];

  echo json_encode($json);
}
else
{
  $json = [
    'code' => 400,
    'error' => "bad request"
  ];

  echo json_encode($json);
}

$db = null;
?>