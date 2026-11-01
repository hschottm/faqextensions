<?php

$GLOBALS['TL_DCA']['tl_faq_vote'] = [
    'config' => [
        'dataContainer' => \Contao\DC_Table::class,
        'doNotCopyRecords' => true,
        'ptable' => 'tl_faq',
        'sql' => ['keys' => ['id' => 'primary', 'pid,voter' => 'unique']],
    ],
    'fields' => [
        'id' => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'pid' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        'voter' => ['sql' => "varchar(64) NOT NULL default ''"],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
    ],
];
