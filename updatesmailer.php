<?php

/*
    * Required files
    *
    * Description of files required.
    *
    * Helper.inc Utility functions.
    * Log.inc    Error and normal records.
    * Check.inc  Form validation utility functions.
    * Db.inc     Database access functions.
*/

require_once 'Log.inc';
require_once 'Db.inc';
$db = new Db();

$qry     = 'SELECT * FROM Updatelog WHERE sent=0 ORDER BY created ASC';
$result  = $db->makeQuery($qry);
$updates = [];

if (($result !== false) && mysqli_num_rows($result) !== 0) {
    $db    = new Db();
    $limit = mysqli_num_rows($result);
    while ($row = mysqli_fetch_assoc($result)) {
        $updates[$row['formid']][$row['field']] = $row;
    }

    $formStrings = [];
    foreach ($updates as $formid => $fields) {
        $table = '<table border="1"><thead><tr><th>Field</th><th>Old value</th><th>New value</th><th>Username</th></tr></thead><tbody>';
        foreach ($fields as $field => $record) {
            $table .= '<tr><td>'.$record['field'].'</td>';
            $table .= '<td>'.$record['oldrecord'].'</td>';
            $table .= '<td>'.$record['newrecord'].'</td>';
            $table .= '<td>'.$record['username'].'</td></tr>';
        }

        $table .= '</tbody></table>';

        $formStrings[$formid] = $table;
    }

    $emailBody = '';
    foreach ($formStrings as $formid => $table) {
        $result      = $db->makeQuery("SELECT name, formname FROM Company C, Form F WHERE C.compkey=F.compkey AND F.formid='$formid'");
        $formDetails = mysqli_fetch_assoc($result);
        $emailBody  .= '<br>Company name: '.$formDetails['name'];
        $emailBody  .= '<br>Form name: '.$formDetails['formname'];
        $emailBody  .= $table;
    }
    $emailOptions['toEmail'] = ['kaustubhkhavnekar@gmail.com'];
    $emailOptions['Subject'] = 'Form update log';
    $emailOptions['ReplyTo'] = $_SESSION['email'];
    $emailOptions['Body']    = $emailBody;

    // echo $emailBody;
    include_once 'Mailer.inc';
    $mailer = new Mailer();
    $mailer->sendHTMLMail($emailOptions);
// UPDATE  Updatelog
// SET     sent = 1
// WHERE   sent = 0
// ORDER BY created ASC
// LIMIT $limit
}
