<?php

namespace BitScout\Sepa;

require_once 'Statement.php';

class Sepa {
	
	const DEBIT = 'DBIT';
	
	/**
	 * Expects a ZIP or XML file containing a SEPA C52 bank statement
	 * 
	 * @param string $filepath Path to a C52 XML file or a ZIP file containing such files     	
	 */
	public static function readStatement($filepath) {
		$filepath = realpath($filepath);
		
		$statement = new Statement ();
		
		$zip = new \ZipArchive ();
		if ($zip->open ( $filepath ) === TRUE) {
			
			for($i = 0; $i < $zip->numFiles; $i ++) {
				
				// Ignore non-XML files
				$filename = $zip->getNameIndex ( $i );
				$extension = strtolower ( pathinfo ( $filename, PATHINFO_EXTENSION ) );
				if ($extension !== 'xml') {
					continue;
				}
				
				// Read file content from within the ZIP file and interprete it as XML
				$zipDeepPath = 'zip://' . $filepath . '#' . $filename;
				$xml = simplexml_load_string ( Sepa::readFile ( $zipDeepPath ) );
				
				Sepa::readC52 ( $xml, $statement, $filename);
			}
		} else {
			$xml = simplexml_load_file ( $filepath );
			Sepa::readC52 ( $xml, $statement );
		}
		
		return $statement;
	}
	
	/**
	 * Reads a C52 SEPA bank statment
	 *
	 * @param string $filepath        	
	 */
	private static function readC52($xml, $statement, $filename=null) {
		
		// Read general information on this statement
		if ($statement->unixtime === null) {
			$statement->unixtime = strtotime ( $xml->BkToCstmrAcctRpt->GrpHdr->CreDtTm );
		}
		if ($statement->iban === null) {
			$statement->iban = $xml->BkToCstmrAcctRpt->Rpt->Acct->Id->IBAN;
		}
		
		// Add transactions from this XML file
		$mixedStatements = $xml->BkToCstmrAcctRpt->Rpt->children ();
		
		foreach ( $mixedStatements as $xmlStatement ) {
			$nodeName = $xmlStatement->getName ();
			
			switch ($nodeName) {
				case 'Id' :
				case 'CreDtTm' :
				case 'Acct' :
				case 'ElctrncSeqNb' :
					
					// Ignore these
					continue 2;
					
					break;
				case 'Ntry' : // Transaction
					
					$transaction = new Transaction ();
					$transaction->filename = $filename;
					$transaction->date = $xmlStatement->ValDt->Dt;
					$transaction->amount = (double)$xmlStatement->Amt;
					$transaction->purpose = $xmlStatement->NtryDtls->TxDtls->Purp->Cd;
					$transaction->text = $xmlStatement->NtryDtls->TxDtls->RmtInf->Ustrd;
					
					if ($xmlStatement->CdtDbtInd == Sepa::DEBIT) {
						$transaction->amount = - 1 * $transaction->amount;
						
						$transaction->name = $xmlStatement->NtryDtls->TxDtls->RltdPties->Cdtr->Nm;
						$transaction->iban = $xmlStatement->NtryDtls->TxDtls->RltdPties->CdtrAcct->Id->IBAN;
						$transaction->bank_name = $xmlStatement->NtryDtls->TxDtls->RltdAgts->CdtrAgt->FinInstnId->Nm;
						$transaction->bank_code = $xmlStatement->NtryDtls->TxDtls->RltdAgts->CdtrAgt->FinInstnId->Othr->Id;
						$transaction->bic = $xmlStatement->NtryDtls->TxDtls->RltdAgts->CdtrAgt->FinInstnId->BIC;
					} else {
						$transaction->name = $xmlStatement->NtryDtls->TxDtls->RltdPties->Dbtr->Nm;
						$transaction->iban = $xmlStatement->NtryDtls->TxDtls->RltdPties->DbtrAcct->Id->IBAN;
						$transaction->bank_name = $xmlStatement->NtryDtls->TxDtls->RltdAgts->DbtrAgt->FinInstnId->Nm;
						$transaction->bank_code = $xmlStatement->NtryDtls->TxDtls->RltdAgts->DbtrAgt->FinInstnId->Othr->Id;
						$transaction->bic = $xmlStatement->NtryDtls->TxDtls->RltdAgts->DbtrAgt->FinInstnId->BIC;
					}
					
					array_push ( $statement->transactions, $transaction );
					
					break;
				case 'Bal' : // Balance value (before or after these transactions)
					
					if ($xmlStatement->Tp->CdOrPrtry->Cd == 'CLBD') {
						$statement->balance = $xmlStatement->Amt;
					}
					
					break;
			}
			
			// echo '<br>['.$nodeName.'] <pre>'.$xmlStatement->asXML().'</pre>';
		}
	}
	
	/**
	 * Reads a text file and returns it as a string
	 * 
	 * @param string $path
	 */
	private function readFile($path) {
		$handle = fopen ($path, 'r' );
		$content = '';
		while ( ! feof ( $handle ) ) {
			$content .= fread ( $handle, 8192 );
		}
		fclose ( $handle );
		
		return $content;
	}
}
