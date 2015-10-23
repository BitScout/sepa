<?php

namespace BitScout\Sepa;

/**
 * A transaction within a bank statement,
 * i. e. how much money was transferred from or to what account
 */
class Transaction {
	
	const PURPOSE_SALARAY = 'SALA';
	const PURPOSE_RECURRING = 'RINP'; // Recurring Installment Payment
	
	public $identifier; // e. g. 2015-02-26-19.50.47.096091
	public $date; // Transaction date as YYYY-MM-DD
	public $amount;
	public $purpose; // Purpose code, see Transaction::PURPOSE_*
	public $additional_info; // Country-dependant, non-standardized descriptor (online transfer, debit, ATM, ...)
	public $text; // Transaction text chosen by the initiating party
	public $name; // Name of the opposing party
	public $iban;
	public $bic;
	public $account; // Bank account number
	public $bank_code;
	public $bank_name;
	
	public $filename; // For finding out what file in a ZIP contained the transaction
	
}


