<?php

namespace BitScout\Sepa;

require_once 'Transaction.php';

class Statement {
	
	
	public $unixtime; // When the bank "printed" the bank statement
	public $iban; // IBAN for which the statement was issued
	public $balance;
	
	public $transactions = array();
	
}


