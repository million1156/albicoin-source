<?php
$db = new PDO("sqlite:../database.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id']) && htmlspecialchars($_GET['id']) == $_GET['id'])
{
  $id = $_GET['id'];
  
  $exist = $db->prepare("SELECT * FROM requests WHERE id=:id");
  $exist->execute([':id' => $id]);
  $exists = $exist->fetch();
  $exist->closeCursor();

  if ($exists)
  {
    $json = [
      'code' => 200,
      'by' => $exists['too'],
      'id' => $exists['id'],
      'paid' => $exists['paid'],
      'amount' => $exists['amount'],
      'paydate' => $exists['paydate'],
      'notes' => 'paid 1 means true, 0 means false'
    ];

    echo json_encode($json);
  }
  else
  {
    $json = [
      'code' => 404,
      'error' => 'invalid request id'
    ];

    echo json_encode($json);
  }
}
else
{
  $json = [
    'code' => 400,
    'error' => 'bad request'
  ];

  echo json_encode($json);
}

$db = null;
?>