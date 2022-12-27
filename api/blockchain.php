<?php
if (isset($_GET['start']) && $_GET['limit'])
{
  $db = new PDO("sqlite:../database.sqlite");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $get = $db->prepare("SELECT * FROM blocks LIMIT :start, :limit");
  $get->execute([':start' => $_GET['start'], ':limit' => $_GET['limit']]);
  $stuff = $get->fetchAll();
  
  foreach ($stuff as $key => $thing)
  {
    $stuff[$key]['transactions'] = json_decode($thing['transactions'], true);
  }
  
  $get->closeCursor();
  
  echo json_encode($stuff);
  
  $db = null;
}
else
{
  $db = new PDO("sqlite:../database.sqlite");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $get = $db->prepare("SELECT * FROM blocks LIMIT 0, 100");
  $get->execute();
  $stuff = $get->fetchAll();
  
  foreach ($stuff as $key => $thing)
  {
    $stuff[$key]['transactions'] = json_decode($thing['transactions'], true);
  }
  
  $get->closeCursor();
  
  echo json_encode($stuff);
  
  $db = null;
}
?>