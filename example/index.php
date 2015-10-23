<?php
use BitScout\Sepa\Sepa;
?>
<html>
<body>

	<h1>SEPA Statement</h1>
<?php

require_once '../src/BitScout/Sepa/Sepa.php';

$statement = Sepa::readStatement('20150723-20151021-123456-camt52Booked.ZIP');

?>
<table border="1" cellspacing="0" cellpadding="5">
	<tr><td>Printed</td><td><?php echo date('Y-m-d H:i:s', $statement->unixtime); ?></td></tr>
	<tr><td>IBAN</td><td><?php echo $statement->iban; ?></td></tr>
	<tr><td>Balance</td><td><?php echo $statement->balance; ?></td></tr>
	<tr><td>Balance (previous)</td><td><?php echo $statement->balance_previous; ?></td></tr>
</table>
<h2>Transactions</h2>
<table border="1" cellspacing="0" cellpadding="5">
	<tr>
		<th>Date & ID</th>
		<th>Amount</th>
		<th>Opposing Party &amp; Bank</th>
		<th>Bank Account</th>
		<th>Text & Transaction type</th>
	</tr>
<?php foreach($statement->transactions as $transaction) { ?>
	<tr>
		<td>
			<?php echo $transaction->date; ?><br>
			<?php echo $transaction->identifier; ?>
		</td>
		<td align="right"><?php echo number_format($transaction->amount, 2); ?></td>
		<td>
			<?php echo $transaction->name; ?><br>
			<?php echo $transaction->bank_name; ?>
		</td>
		<td>
			<?php echo $transaction->bic; ?> <?php echo $transaction->bank_code; ?><br>
			<?php echo $transaction->iban; ?> <?php echo $transaction->account; ?>
		</td>
		<td>
			<?php echo $transaction->text; ?><br>
			<?php echo $transaction->additional_info; ?> (<b><?php echo $transaction->filename; ?></b>)<br>
		</td>
	</tr>
<?php } ?>
</table>
</body>
</html>