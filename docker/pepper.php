<?php

$root = "/fox";

$accounts = json_decode(file_get_contents($root . "/mint/data_account.json"));
$transactions = json_decode(file_get_contents($root . "/mint/data_transaction.json"));

$today = date("Y-m-d");
$now = date("YmdHis");

foreach($accounts as $a){
    switch($a->type){
        case "CreditAccount":
            switch($a->creditAccountType){
                case "CREDIT_CARD":
                    creditcard($a);
            }
    }
}

    function
concise($str){
    $cs = "";
    
    $s = explode("|", chunk_split($str, 1, "|"));
    
    foreach($s as $c)
        if(ctype_alnum($c))
            $cs = $cs . $c;
    
    return($cs);
}

    function
creditcard($a){
    global      $root, $transactions;
    global      $today, $now;
    
    $fi = concise($a->fiName);
    
    $outfile = $root . "/ofx/$fi-" . $a->cpAccountNumberLast4 . "-" . $today . ".ofx";
    
    $out = fopen($outfile, "w");
    
    $acct = $a->cpAccountNumberLast4;
    $balance = $a->value;
    $available = $a->availableCredit;
    $acctname = $a->name;
    
    $datemin = PHP_INT_MAX;
    $datemax = PHP_INT_MIN;
    
    $count = 0;

    foreach($transactions as $t){
        if($t->accountId != $a->id)
            continue;
        
        $d = strtotime($t->date);
        $datemin = min($datemin, $d);
        $datemax = max($datemax, $d);
        
        ++$count;
    }

    $dtmin = date("YmdHis", $datemin);
    $dtmax = date("YmdHis", $datemax);
    
$hdr = <<<XXX
OFXHEADER:100
DATA:OFXSGML
VERSION:102
SECURITY:NONE
ENCODING:USASCII
CHARSET:1252
COMPRESSION:NONE
OLDFILEUID:NONE
NEWFILEUID:NONE

XXX;

    fprintf($out, "$hdr\n");

$sonrs = <<<XXX
<SIGNONMSGSRSV1>
<SONRS>
<STATUS>
<CODE>0
<SEVERITY>INFO
<MESSAGE>SUCCESS
</STATUS>
<DTSERVER>$now
<LANGUAGE>ENG
<FI>
<ORG>$fi
</FI>
</SONRS>
</SIGNONMSGSRSV1>

XXX;

    fprintf($out, "<OFX>\n$sonrs");

$trnhdr = <<<XXX
<CREDITCARDMSGSRSV1>
<CCSTMTTRNRS>
<TRNUID>0
<STATUS>
<CODE>0
<SEVERITY>INFO
</STATUS>
<CCSTMTRS>
<CURDEF>USD
<CCACCTFROM>
<ACCTID>$acct
</CCACCTFROM>
<BANKTRANLIST>
<DTSTART>$dtmin
<DTEND>$dtmax

XXX;

    fprintf($out, "$trnhdr");
   
    foreach($transactions as $t){
        if($t->accountId != $a->id)
            continue;
        
        $amt = $t->amount;
        $fid = $t->id;
        $name = substr(strtr(htmlspecialchars($t->description, ENT_XML1), "\r\n", "::"), 0, 32);
        $memo = htmlspecialchars($t->category->name, ENT_XML1);
        
        $type = ($amt > 0) ? "CREDIT" : "DEBIT";

        $asof = date("YmdHis", strtotime($t->date));
        
$trans = <<<XXX
<STMTTRN>
<TRNTYPE>$type
<DTPOSTED>$asof
<TRNAMT>$amt
<FITID>$fid
<NAME>$name
<MEMO>$memo
</STMTTRN>

XXX;

        fprintf($out, $trans);
    }


$end = <<<XXX
</BANKTRANLIST>
<LEDGERBAL>
<BALAMT>$balance
<DTASOF>$now
</LEDGERBAL>
<AVAILBAL>
<BALAMT>$available
<DTASOF>$now
</AVAILBAL>
</CCSTMTRS>
</CCSTMTTRNRS>
</CREDITCARDMSGSRSV1>
</OFX>

XXX;

    fprintf($out, $end);
    
    fclose($out);
    
    echo "Saved $count transactions from $acctname in $outfile.\n";
}
