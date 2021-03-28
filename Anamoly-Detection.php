#This code is written in PHP to detect any anamoly data present in the data. I am using a  Z-score algorithm. 
// HTML code is there
<!DOCTYPE html>
<html lang = "en">
    <head> <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!-- Compiled and minified CSS -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
     <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta charset = "utf-8">
         <title> ZSCORE </title>
        </head>
        <body>
             <nav>
    <div class="nav-wrapper container">
      <a href="#" class="brand-logo">DashBoard</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a href="/index.php">Home</a></li>
        <li><a href="/graph.php">Graph</a></li>
         <li><a href="/Zscore2.php">Zscore</a></li>
         <li><a href="prediction_style.php">Prediction</a></li>
         <li><a href="/logout.php">Log out !</a></li>
      </ul>
    </div>
  </nav>
         <div class="card-panel">
             <div class="card-panel teal lighten-2"><span class="red"><h5 class="center-align">Anomaly Detection</h5></span></div>
          </div>
          
          
  </body>
  // The Z-score algorithm 
<?php
   
    // calling database_connection page
    require 'database_connection.php';
    
    //It takes only 10 data from the dataset everytime it calculates the high_threshold and low_threshold value.
    
    $new = 1;
    $old = 10;
    
    
    // making database connection
    $db = new Database();
    $conn= $db->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT COUNT(*) AS VALUE FROM DATA2");
    $stmt->execute();
    //set the resulting array to associative 
    $result = $stmt->fetch();
    // the value is stored
    $ans = $result['VALUE']; ?>
    <p> <b> ><div class="red-text">  <?php echo nl2br("\n Total Values =". $ans."\n"); ?> </div></b></p>
    <?php
    $left = $ans - 10;
    
    $id =1;
    
    
    
    
    while($old <= $ans)
    {
        
        condition($ans,$left);
    
        
        
        
        $stmt = $conn->prepare("SELECT VALUE FROM DATA2 WHERE ID BETWEEN $new AND $old");
        $stmt->execute();
        //set the resulting array to associative 
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        
        
        
        $mean_value = mean($result);// calling the mean function
        $zscore_value = zscore($result);// calling the zscore function;
       

        $stmt = $conn->prepare("SELECT VALUE FROM DATA2 WHERE ID = $old");
        $stmt-> execute();
        $result1 = $stmt->fetch();
        
        $High_bound = $zscore_value + $result1['VALUE']; //the high_bound value
        $Acc_High_bound = number_format($High_bound,3,'.','');
        
        ?>
       <p> <b><div class = "red-text"><?php echo nl2br("\n High_Bound for ".$id." set = ". $Acc_High_bound); ?> </div></b></p>
       <?php
        
        //calculating the low_bound
        
        $stmt = $conn->prepare("SELECT VALUE FROM DATA2 WHERE ID = $new");
        $stmt-> execute();
        $result2 = $stmt->fetch();
    
        $Low_bound = $result2['VALUE'] - $zscore_value; // The Low_bound value
         $Acc_Low_bound = number_format($Low_bound,3,'.',''); ?>
        <p> <b><div class = "red-text"><?php echo nl2br("Low_Bound for ".$id." set = ". $Acc_Low_bound. "\n"); //showing the high_bound value ?> </div></b></p>
        <?php

        if($new >= 11)
        {
            notification($result,$High_bound,$Low_bound);
          
        }
        
        $new += 10;
        $old += 10;
        $id++;
        
        
    }
    
    
    // calculating the mean
    function mean($result)
    {
        $total =0;
        foreach($result as $item)
        {
            $total += $item['VALUE'];
        }
            $GLOBALS['mean'] = $total/10;
            return $GLOBALS['mean'];
    }        
    
    
    //calculating the zscore
    function zscore($result)
    {
        $variance=0;
        foreach( $result as $item1)
        {
            $variance += (pow(($item1['VALUE'] - $GLOBALS['mean']),2))/ 10;
            $GLOBALS['zscore'] = 6 * sqrt($variance);
        }
        return $GLOBALS['zscore'];
    }
    
    //condition function
    function condition($ans,$left)
    {
        if(($ans % 10) != 0)
        
        { 
            ?>
        
         <p> <b><div class = "red-text"><?php    echo nl2br("The no. of data is :" .$ans. "\n"); 
            echo nl2br("The no. of data you required more:" .$left."\n"); ?> </div></b></p>
            <?php
           
            die();
        }
    }

    //notification function 

    function notification($result,$High_bound,$Low_bound)
    {
        foreach($result as $value)
        {
            if($value['VALUE'] >= 88.0) 
            {
                $rise = $value['VALUE'];
                telegram_highBound($rise);
                
            }
            else if($value['VALUE'] <= $Low_bound)
            {
                $low = $value['VALUE'];
                telegram_lowBound($low);
                
        }
    }
    
    //telegram function to send the notification to the user.
    
    function telegram_highBound($rise)
    {
        
        $apiToken = "1278198034:AAFCx0E_zvoE7WAUpujq4vR6KkRnWnNyBEo";
        
                $data = [
                 'chat_id' => '@pu_temperture_data',
                    'text' => ' The temperture is high '. $rise
                ];
        
            
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
       
    }
    
     function telegram_lowBound($low)
    {
        
        $apiToken = "1278198034:AAFCx0E_zvoE7WAUpujq4vR6KkRnWnNyBEo";
        
                $data = [
                 'chat_id' => '@pu_temperture_data',
                    'text' => ' The temperture is low '. $low
                ];
        
            
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
        
        
    }
?>
