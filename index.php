<?php
function resultBlockStyled($errors,$successes){
	//Error block
	if(count($errors) > 0) {
		echo "<div id='alerts-error'>
		<ul>";
		foreach($errors as $error) {
			echo "<li>".$error."</li>";
		}
		echo "</ul>";
		echo "</div>";
	}
	//Success block
	if(count($successes) > 0) {
		echo "<div id='alerts-success'>
		<ul>";
		foreach($successes as $success) {
			echo "<li>".$success."</li>";
		}
		echo "</ul>";
		echo "</div>";
	}
}

function wrap_text_with_tags( $haystack, $needle , $beginning_tag, $end_tag ) {
    $needle_start = stripos($haystack, $needle);
    $needle_end = $needle_start + strlen($needle);
    $return_string = substr($haystack, 0, $needle_start) . $beginning_tag . $needle . $end_tag . substr($haystack, $needle_end);
    
    return $return_string;
}

function do_pi($guess_full) {
	global $successes;
	global $errors;

	$pi_digits = "1415926535897932384626433832795028841971693993751058209749445923078164062862089986280348253421170679821480865132823066470938446095505822317253594081284811174502841027019385211055596446229489549303819644288109756659334461284756482337867831652712019091456485669234603486104543266482133936072602491412737245870066063155881748815209209628292540917153643678925903600113305305488204665213841469519415116094330572703657595919530921861173819326117931051185480744623799627495673518857527248912279381830119491298336733624406566430860213949463952247371907021798609437027705392171762931767523846748184676694051320005681271452635608277857713427577896091736371787214684409012249534301465495853710507922796892589235420199561121290219608640344181598136297747713099605187072113499999983729780499510597317328160963185950244594553469083026425223082533446850352619311881710100031378387528865875332083814206171776691473035982534904287554687311595628638823537875937519577818577805321712268066130019278766111959092164201989";

	// Strip off the leading '3.' value of the entered guess
	if (substr($guess_full, 0, 2) === "3." && strlen($guess_full) > 2) {
		$guess_digits = substr($guess_full, 2);
		$guess_digits_length = strlen($guess_digits);


		// Determine how many decimal places the user got correct
		for ($i=1; $i<$guess_digits_length; $i++) {
			if ($guess_digits[$i] !== $pi_digits[$i]) break;
		}

		// For the trainer, either finish displaying the current block of 5, or display the next block of 5
		// Check if the input length is divisible by 5
		if ($i % 5 === 0) {
			// Add the next 5 digits
			$k = 5;
			$trainer_length = $i + $k;
		}
		else {
			// Keep adding 1 until the result is divisible by 5
			for ($k=1; $k<=5; $k++) {
				if (($k + $i) % 5 === 0) {
					$trainer_length = $i + $k;
					break;
				}
			}
		}

		// New pi value the user should train for
		$trainer_value = substr($pi_digits, 0, $trainer_length);

		// Create some congratulatory strings
		if ($i >= 1000) {
			$congratulatory = "Ya right!";
		} elseif ($i >= 500) {
			$congratulatory = "Holy Shit!";
		} elseif ($i >= 150) {
			$congratulatory = "Awesome!";
		} elseif ($i >= 125) {
			$congratulatory = "The grind is on.";
		} elseif ($i >= 100) {
			$congratulatory = "Wow! You made it!";
		} elseif ($i >= 75) {
			$congratulatory = "Almost to a hundred.";
		} elseif ($i >= 50) {
			$congratulatory = "Getting there.";
		} elseif ($i >= 25) {
			$congratulatory = "Not bad.";
		} elseif ($i >= 10) {
			$congratulatory = "Better than most.";
		} else {
			$congratulatory = "Okay.";
		}
		$phrase = ($i > 1 ? "$i decimal places" : "only 1 decimal place");

		$successes[] = "$congratulatory You got $phrase correct.";
		$successes[] = "&nbsp;";
		$successes[] = "Pi approximated to $trainer_length decimal places is";

		// Break the trainer value into segments of 5
		// Determine how many segments we need
		$segment_count = ceil($trainer_length / 5);
		$segment_count_lines = ceil($segment_count / 7);

		//$successes[] = "there are $segment_count segments over $segment_count_lines lines";

		// If there are more than 7 segments, print multiple lines
		for ($j=1; $j<=$segment_count_lines; $j++) {
			unset($line);
			$line = array();

			// Print 35 characters per line
			// Remaining chunks
			$chunks_remaining = $segment_count - (($j - 1) * (35/5));
			if ($chunks_remaining <= 7) {
				$data = substr($pi_digits, ($j - 1) * 35, $chunks_remaining * 5);
				
				// Final line. Set an offset to work backwards from
				$ending = True;
			}
			else {
				// There will be another line
				$data = substr($pi_digits, ($j - 1) * 35, 35);
			}
			
			// Bold the string
			$return_string = wrap_text_with_tags( $data , $data , "<strong>" ,"</strong>");

			// Add a space after every 5th character
			$str = chunk_split($data, 5, ' ');


			// Add a leading '3.' to the first line. Otherwise, add two fixed-wdth spaces to allign text
			if ($j === 1) {
				$prefix = "3.";
			}
			else {
				$prefix = "&nbsp;&nbsp;";
			}
			
			// Count backwards from end of string - $k and bold that section
			if ($ending) {
				$correct = substr(trim($str), 0, -$k);
				$trainer = substr(trim($str), -$k);
				$successes[] = "<pre>$prefix<strong>$correct</strong>$trainer</pre>";
			}
			else {
				$successes[] = "<pre>$prefix<strong>$str</strong></pre>";
			}
		}
	}
	else {
		$errors[] = "Well they say we all have to start somewhere...";
		$errors[] = "&nbsp;";
		$errors[] = "Pi approximated to the first 5 decimal places is";
		$errors[] = "<pre>3.14159</pre>";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Pi Trainer</title>
	<link rel="stylesheet" type="text/css" href="pi.css">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>

    <?php
	if (isset($_POST['submit'])) {
		// Continue only if there are no errors
		if (count($errors) === 0) {
			do_pi($_POST['pi']);
		}
	}
	?>

	<div id="content-wide">
		<div id="alerts-container">
			<?php echo resultBlockStyled($errors,$successes); ?>
		</div>

        <div>
			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
				<ul>
					<div class="section-label">Pi Trainer</div>
					<div id="content">
						<li>
							<span>&nbsp;Enter as many digits of pi as you can!</span><br/>
							<input class="long" type="text" name="pi" placeholder="Enter as many digits of pi as you can!" value="3." maxlength="1002" />
						</li>
						<li>
							<input type="submit" id="submit-button" name="submit" value="Check" />
						</li>
					</div>
				</ul>
			</form>
		</div>
    </div>
	<div id="footer">
    	<span id="footer-span">Powered by <a href="http://www.righteousbanana.com" target="_blank">RighteousBanana</a>.</span>
	</div>
</body>
</html>
