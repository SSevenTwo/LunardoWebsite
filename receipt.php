<?php
session_start();
require 'tools.php';
// require 'index.php';

if(empty($_SESSION['cart'])){
    header("Location: index.php");
}


$moviesObject = $pricesObject = "";
$moviesObject =
[
  'ACT' => ['title' => 'The Avengers: EndGame',
            'rating' => 'M',
            'description' => 'The grave course of events set in motion by Thanos that wiped out half
            the universe and fractured the Avengers ranks compels the remaining Avengers
            to take one final stand in Marvel Studios’ grand conclusion to twenty-two films.',
            'screenings' => ['WED' => 'T21',
                            'THU' => 'T21',
                            'FRI' => 'T21',
                            'SAT' => 'T18',
                            'SUN' => 'T18'
                            ]
            ],
  'RMC' => ['title' => 'Top End Wedding',
            'rating' => 'M',
            'description' => 'Lauren and Ned have 10 days to find Laurens mother who has gone
            AWOL in the remote far north of Australia so that they can reunite
            her parents and pull off their dream wedding.',
            'screenings' => ['MON' => 'T18',
                            'TUE' => 'T18',
                            'SAT' => 'T15',
                            'SUN' => 'T15'
                            ]
            ],
  'ANM'=> ['title' => 'Dumbo',
            'rating' => 'PG',
            'description' => 'Struggling circus owner Max Medici enlists a former star and his
            two children to care for Dumbo, a baby elephant born with oversized
            ears. When the family discovers that the animal can fly, it soon
            becomes the main attraction -- bringing in huge audiences and
            revitalizing the run-down circus.',
            'screenings' => ['MON' => 'T12',
                            'TUE' => 'T12',
                            'WED' => 'T18',
                            'THU' => 'T18',
                            'FRI' => 'T18',
                            'SAT' => 'T12',
                            'SUN' => 'T12'
                            ]
            ],
  'AHF' => ['title' => 'The Happy Prince',
            'rating' => 'MA15+',
            'description' => 'His body ailing, Oscar Wilde lives out his last days in exile,
            observing the difficulties and failures surrounding him with
            ironic detachment, humour, and the wit that defined his life.',
            'screenings' => ['WED' => 'T12',
                            'THU' => 'T12',
                            'FRI' => 'T12',
                            'SAT' => 'T21',
                            'SUN' => 'T21'
                            ]
            ]
];

$pricesObject = 
[
  'full' => ['STA' => 19.80,
            'STP' => 17.50,
            'STC' =>15.30,
            'FCA' => 30.80,
            'FCP' => 27.00,
            'FCC' =>24.80
            ],
  'disc' => ['STA' => 14.00,
            'STP' => 12.50,
            'STC' =>11.00,
            'FCA' => 24.00,
            'FCP' => 22.50,
            'FCC' =>21.00
            ]
];
  //the day of movie chosen by form
  $dayOfMovie = $_SESSION['cart']['movie']['day'];
  //the time of movie chosen by form
  $timeOfMovie = $_SESSION['cart']['movie']['hour'];

  //find all the movie's possible screenings
  $screenings = $moviesObject[$_SESSION['cart']['movie']['id']] ['screenings'];

  //the hour chosen from the screening
  $hour = $screenings[$dayOfMovie];

  //the hour chosen inside the moviesObject
  // $hour = $day[$timeOfMovie];

$type = checkDiscountOrFull($dayOfMovie, $hour);
$movie = $moviesObject[$_SESSION['cart']['movie']['id']];

function checkDiscountOrFull($day, $hour)
{
  //all movies on mondays and wednesdays are discounted;
  if($day ==='MON' || $day=== 'WED')
  {
    return 'disc';
  }
  //no movies on sunday and saturday are discounted
  else if($day ==='SAT' || $day === 'SUN')
  {
    return 'full';
  }
  //weekday matinee sessions not covered by the Mon/Wed discount
  else if(($day === 'TUE' || $day === 'THU' || $day === 'FRI') && $hour === 'T12')
  {
    return 'disc';
  }
  //weekday nightly shows are not discounted
  else
  {
    return 'full';
  }
}
$boughtSeats = array();
$eachTicketSubTotal = array();
$totalPrice = 0;

function checkSubTotal($pricesObject, $type, $eachTicketSubTotal, &$totalPrice)
{
  //checks how many of each type of seat the user has booked
  foreach($_SESSION['cart']['seats'] as $seats => $amount)
  {
    //if user has at least booked a type of seat add it to an array
    if(!empty($amount))
    {
      //need to check if this will just replace the same element/index or if it moves forward; can't array.push an associative array
      $boughtSeats[$seats] = $amount;
    }
  }

  echo "<table>
  <tr>
    <th>Seat Type</th>
    <th>Quantity</th>
    <th>Unit Price</th>
    <th>GST</th>
    <th>Total Price</th>
  </tr>";

  foreach ($boughtSeats as $seats => $amount)
  {
      //have to traverse through pricesObject array to find the price and multiply it by how many they bought
      $eachTicketSubTotal[$seats] = number_format($pricesObject[$type][$seats] * $amount,2);

      $GST = number_format($eachTicketSubTotal[$seats] * 0.11,2);
      $unitPrice = number_format($eachTicketSubTotal[$seats] -$GST,2); 

      echo "<tr> 
      <td>${seats}</td> 
      <td>${amount}</td> 
      <td>$${unitPrice}</td> 
      <td>$${GST}</td> 
      <td>$${eachTicketSubTotal[$seats]}</td> 
      </tr>";

      $totalPrice += $eachTicketSubTotal{$seats};
  }
  echo "</table>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <link id='receiptStylecss' type="text/css" rel="stylesheet" href="receiptStyle.css">
</head>
<body>

<?= preShow($_SESSION['cart']) ?>

  <div id = receipt class = page>

    <!--establishment details always displayed first in invoices-->
    <h2>Lunardo Cinemas</h2>
    <address>Email: info@lunardo.com<br>
      Phone: (03) 8675 4672<br>
      56 Moonlight Way, Seymour, VIC, 3660<br>
      ABN number: 00 123 456 789<br><br>
    </address>
    

    <h1>Confirmation of Purchase</h1>
    <span class = category> Name: </span> <span class = information> <?= $_SESSION['cart']['cust']['name']?> </span><br>
    <span class = category> Email: </span> <span class = information> <?= $_SESSION['cart']['cust']['email']?> </span><br>
    <span class = category> Phone Number:</span> <span class = information> <?= $_SESSION['cart']['cust']['mobile']?> </span><br>
    <span class = category> Credit Card:</span> <span class = information> <?= $_SESSION['cart']['cust']['card']?> </span><br>
    <span class = category> Credit Card Expiry:</span> <span class = information> <?= $_SESSION['cart']['cust']['expiry']?> </span><br>

    <h2>Movie Details</h2>
    <p>
      <? ?>
      <span class = category> Title:</span> <span class = information><?=$movie['title']?></span><br>
      <span class = category> Rating:</span> <span class = information><?= $movie['rating']?></span><br>
      <span class = category> Screening Session:</span> <span class = information><?= $_SESSION['cart']['movie']['day']?> at <?= $_SESSION['cart']['movie']['hour']?></span><br>
      <span class = category> Description:</span> <span class = information> <?= $movie['description']?> </span><br>
    </p>

    <h2>Order details</h2>
    <p>
    <?php checkSubTotal($pricesObject, $type, $eachTicketSubTotal, $totalPrice); ?>
    Total price: $<?=number_format($totalPrice,2)?>
    </p>
  </div>

</body>
</html>