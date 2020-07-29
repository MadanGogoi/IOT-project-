// this code will predict the temnperature based on earlier data. I am using Weighted Moving Algorithm

<!DOCTYPE html>
<html lang = "en">
    <head>
         <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!-- Compiled and minified CSS -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
     <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta charset = "UTF-8">
        <title> Pedicted Data</title>
        </head>
         <body>
     <nav>
    <div class="nav-wrapper container">
      <a href="#" class="brand-logo">DashBoard</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a href="/">Home</a></li>
        <li><a href="/graph.php">Graph</a></li>
         <li><a href="/zscore.php">Anomaly Detection</a></li>

         <li><a href="/logout.php">Log out !</a></li>
      </ul>
    </div>
  </nav>
  <div class="card-panel">
             <div class="card-panel teal lighten-2"><span class="red"><h5 class="center-align">PREDICTION</h5></span></div>
          </div>
    <div class="container">     
    
    
    // The algorithm starts 

<?php

require 'database_connection.php';



// making database connection
    $db = new Database();
    $conn= $db->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT COUNT(*) AS VALUE FROM DATA2");
    $stmt->execute();
    
     //set the resulting array to associative 
    $result = $stmt->fetch();
    
    $car = array();
    $predict = array();
    $i=0;
    

        
    $ans = $result['VALUE'];

    
    
   //$limit = 10;
    
    $values = ($ans - $limit)+1;
    
    //example 
    $v1 = filter_input(INPUT_POST, 'FRAME');
    echo "Frame Size :".$v1;
    $v2 = filter_input(INPUT_POST, 'NUMBER');
    echo ", PREDICT NUMBER :".$v2;
    echo nl2br("\n".$ans."\n");?>
     <div class="col center">
    <div class="center"><u>Data Values</u></div>
          <table class="striped" cellspacing="0" cellpadding="2" >
              <thead>
                <tr>
                      <th> Data</th>
                  </tr>
              </thead>
              <tbody>
    <?php
    
    $start_data = $ans - $v1;
     $stmt = $conn->prepare("SELECT VALUE FROM DATA2 WHERE ID BETWEEN $start_data AND $ans");
     $stmt->execute();
     
     //set the resulting array to associative 
    $stmt->bindColumn('VALUE', $val);
    while($result = $stmt->fetch(PDO::FETCH_BOUND))
    {
      $car[$i] = $val;?>
       <tr >
                  <td><?php  echo $car[$i]; ?></td>
                  </tr>
      <?php
     
      $i++;
      
    }
   
   
    $id =0;$die = 3;$loop =0;$n1 =1;$count = 1;
    
    while($loop < $v2 )
    
    {
        
    
       
        $value =0; $c =1; $div =0;
        //echo nl2br("\n".$id."\t".$die."\t");
        
        for($i = $id; $i <= $die; $i++)
        {
            $value += $c * $car[$i];
            $div += $c;
            $c += 1;
            
        }
        //echo $value. "\t\t\t". $c."\t\t\t".$div;
        $value = $value/$div;
        $approx_value = number_format($value,3,'.','');
         echo nl2br("\n"."predicted value is  ".$count. "=". $approx_value."\n\n");
         $predict[$n1] = $approx_value;
        
         
        $number = sizeof($car);
      
       $car[$number] = $value;
         
        //$number = sizeof($car);
       // echo nl2br($number."\n".$id."\t\t".$die);
        

        $id = $id +1;
        $die = $number-1;
        $loop++;
        $n1++;
        $count++;
        sleep(5);
        
       // for($i = $id; $i <= $die;$i++)
        //{
           // echo nl2br("\t". $car[$i]);
        //}
    
    }
    echo nl2br("\n The predicted values are");
    for($i = 0;$i <= $number;$i++)
    {
        
        echo nl2br("\n". $predict[$i]);
    }
    ?>
    </div>
    </body>
    </html>
    <!--
   
    /*
    //taking the next four data from the dataset to find the error
    /*
    $v1 = $v2 + 1;
    $v2 = $v2 + 3;
    

    $stmt->bindColumn('VALUE', $val);
     while($result = $stmt->fetch(PDO::FETCH_BOUND))
    {
      $error[$i] = $val;
      
      echo nl2br("\t". $error[$i]."\n");
      $i++;
      
    }
    
    $error_value =0;
    
    for($id =0;$id < 4;$id++)
    {
        $error_value += $predict[$id]- $error[$id];
        
    }    
   
   echo nl2br("\n");
   echo $error_value;
   */
   -->
