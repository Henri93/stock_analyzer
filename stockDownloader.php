http://real-chart.finance.yahoo.com/table.csv?s=GOOG&d=9&e=19&f=2015&g=d&a=0&b=2&c=1990&ignore=.csv
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
<?>
