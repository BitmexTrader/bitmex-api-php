<?php
require_once("BitMex.php");

$key = "xxxxx";
$secret = "yyyyy";

$bitmex = new BitMex($key,$secret);

$timeFrame = "1h";
$count = 22;
$hist = $bitmex->getCandles($timeFrame,$count);
var_dump($hist);
echo "<br><br>";
//calc RSI
$totalGain = 0;
$totalLoss = 0;

for($i=0;$i<21;$i++){
		$compareGreater = $hist[$i]["close"] > $hist[$i+1]["close"];
		$compareLess = $hist[$i]["close"] < $hist[$i+1]["close"];
		
		$gainCalc = $hist[$i]["close"] - $hist[$i+1]["close"];
		$lossCalc = $hist[$i+1]["close"] - $hist[$i]["close"];
				
		if($compareGreater){
			$totalGain += $gainCalc;
			echo "Gain ".$gainCalc."<br>";
		}
		
		if($compareLess){
			$totalLoss += $lossCalc;
			echo "Loss ".$lossCalc."<br>";
		}
		
}

$averageGain = $totalGain / 21;
echo "Avg Gain ".$averageGain."<br>";
$averageLoss = $totalLoss / 21;
echo "Avg Loss ".$averageLoss."<br>";

if($averageLoss == 0){
	$rsi = 100;
} else {
	//calc and normalize
	$rs = $averageGain / $averageLoss;				
	$rsi = 100 - (100 / ( 1 + $rs));
}

echo "RSI: ".$rsi;

//output to log file
$logTime = time().".txt";
$openLog = fopen($logTime, "w");
$inputData = $rsi;
fwrite($openLog, $inputData);
fclose($openLog);


//execute a trade
if($rsi < 13){
	//close any open position
  $bitmex->closePosition(null);
  //null is used to specify close the current position at market
  //alternatively you can specify a limit price to close at
  
	//buy
  $buy = $Bitmex->createOrder("Market", "Buy", null, 10000);
  //null is used in place of a limit price, if you wanted to place a limit order change "Market" to "Limit" and specify the limit price in the 3rd position.
}


//close position if ROE is over N%

//get current position details
$positions = $bitmex->getOpenPositions();
$pnl = $positions[0]["unrealisedRoePcnt"];

//if ROE is 12% or higher close position
if($pnl > 0.12){
  $bitmex->closePosition(null);
}

?>
