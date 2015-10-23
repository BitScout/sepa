<?php

namespace BitScout\Sepa;

require_once 'Transaction.php';

/**
 * A bank statement consisting of some base data and one or more transactions
 */
class Statement {
	
	public $unixtime; // When the bank "printed" the bank statement
	public $iban; // IBAN for which the statement was issued
	public $balance; // Account balance AFTER this statement's transactions
	public $balance_previous; // Account balance AFTER this statement's transactions
	
	public $transactions = array(); // List of all transactions
	
}


