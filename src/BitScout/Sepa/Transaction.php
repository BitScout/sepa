<?php

namespace BitScout\Sepa;

class Transaction {
	
	const PURPOSE_SALARAY = 'SALA';
	const PURPOSE_RECURRING = 'RINP'; // Recurring Installment Payment
	
	public $date;
	public $amount;
	public $purpose;
	public $text;
	
	public $filename; // For finding out what file in a ZIP contained the transaction
	
}


