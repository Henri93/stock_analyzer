<?php 
include("connect.php");

function masterLoop()
{
  $mainTickerFile = fopen("tickerMaster.txt", "r");
  while(!feof($mainTickerFile))
  { 
    $companyTicker = fgets($mainTickerFile);
    $comapnyTicker = trim($companyTicker);
    
    $nextDayIncrease = 0;
    $nextDayDecrease = 0;
    $nextDayNoChange = 0;
    $totalDays = 0;
    
    $sumOfIncreases = 0;
    $sumOfDecreases = 0;
    
    $sql = "SELECT date, percent_change FROM $companyTicker WHERE percent_change < '0' ORDER BY date ASC";
    $result = mysql_query($sql);
    
    if($result)
    {
      while($row = mysql_fetch_array($result))
      {
        $date = $row['date'];
        $percentChange = $row['percent_change'];
        $sql2 = "SELECT date, percent_change FROM $companyTicker WHERE date > '$date' ORDER BY date ASC LIMIT 1";
        $result2 = mqsql_query($sql2);
        $numberOfRows = mysql_num_rows($result2);
        if($numberOfRows == 1)
        { 
          $row2 = mysql_fetch_row($result2);
          $tomorrowDate = $row2[0];
          $tomorrowPercentChange = $row2[1];
          
          if($tomorrowPercentChange > 0)
          {
            $nextDayIncrease++;
            $sumOfIncreases += $tomorrowPercentChange;
            $total++;
          }
          else if($tomorrowPercentChange < 0)
          {
            $nextDayDecrease++;
            $sumOfDecreases += $tomorrowPercentChange;
            $total++;
          }
          else
          {
            $nextDayNoChange++;
            $total++;
          }
        }
        else if($numberOfRows == 0)
        {
          //no data after today
        }
        else
        {
          echo "You have an error in analysis_a";
        }
      }
    }
    else
    {
      echo "Unable to select $companyTicker <br />";
    }
    
    $nextDayIncreasePercent = ($nextDayIncrease / $total) * 100;
    $nextDayDecreasePercent = ($nextDayDecrease / $total) * 100;
    $averageIncreasePercent = $sumOfIncreases / $nextDayIncrease;
    $averageDecreasePercent = $sumOfIncreases / $nextDayDecrease;
    
    insertIntoResultTable();
  }
}

function insertIntoResultTable($companyTicker, $nextDayIncrease, $nextDayIncreasePercent, $averageIncreasePercent, $nextDayDecrease, $nextDayDecreasePercent, $averageDecreasePercent)
{
  $buyValue = $nextDayIncreasePercent * $averageIncreasePercent;
  $sellValue = $nextDayDecreasePercent * $averageDecreasePercent;
  
  $query = "SELECT * FROM analysisA WHERE ticker='$companyTicker' ";
  $result = mysql_query($query);
  $numberOfRows = mysql_num_rows($result);
  
  if($numberOfRows == 1)
  {
    $sql = "UPDATE analysisA SET ticker='$companyTciker', daysInc='$nextDayIncrease',pctOfDaysInc='$nextDayIncreasePercent',avgIncPct='$averageIncreasePercent',daysDec='$nextDayDecrease',pctOfDaysDec='$nextDayDecreasePercent',avgDecPct='$averageDecreasePercent',buyValue='$buyValue',sellValue='$sellValue' WHERE ticker='$companyTicker'";
    mysql_query($sql);
  }
  else
  {
    $sql = "INSERT INTO analysisA (ticker,daysInc,pctOfDaysInc,avgIncPct,daysDec,pctofDaysDec,buyValue,sellValue) VALUES ('$companyTicker','$nextDayIncrease','$nextDayIncreasePercent,'$averageIncreasePercent','$nextDayDecrease','$nextDayDecreasePercent','$averageDecreasePercent','$buyValue','$sellValue')";
    mysql_query($sql);
  }
}

?>
