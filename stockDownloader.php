<?php>
include("connect.php");

function createURL($ticker)
{
  $currentMonth = date("n");
  $currentMonth = $currentMonth - 1;
  $currentDay = date("j");
  $currentYear = date("Y");
  return "http://real-chart.finance.yahoo.com/table.csv?s=$ticker&d=$currentMonth&e=$currentDay&f=$currentYear&g=d&a=0&b=2&c=1990&ignore=.csv";
}

function getCSVFile($url, $outputFile)
{
  $content = file_get_contents($url);
  $content = str_replace("Date,Open,High,Low,Close,Volume,Adj Close", "", $content);
  $content = trim($content);
  file_put_contents($outputFile, $content);
}

function fileToDatabase($textFile, $tableName)
{
  $file = fopen($textFile, "r");
  while(!feof($file))
  {
    $line = fgets($file);
    $pieces = explode(",", $line);
    
    $date = $pieces[0];
    $open = $pieces[1];
    $high = $pieces[2];
    $low = $pieces[3];
    $close = $pieces[4];
    $volume = $pieces[5];
    
    $amount_change = $close - $open;
    $percent_change = ($amount_change/$open)*100;
    
    $sql = "SELECT * FROM $tableName";
    $result = mysql_query($sql);
    if(!$result)
    {
      $sql2 = "CREATE TABLE $tableName (date DATE, PRIMARY KEY(date), open FLOAT, high FLOAT, low FLOAT, close FLOAT, volume INT, amount_change FLOAT, percent_change FLOAT)";
      mysql_query($sql2);
    }
    
    $sql3 = "INSERT INTO $tableName (date, open, high, low, close, volume, amount_change, percent_change) VALUES ('$date','$open','$high','$low','$close','$volume','$amount_change','$percent_change')";
    
  }
  fclose($file);
  
  
  
}
<?>
