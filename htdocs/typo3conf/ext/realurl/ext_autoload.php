<?php
$extpath = t3lib_extMgm::extPath('realurl');
return array(
   'tx_realurl' => $extpath . 'class.tx_realurl.php',
   'tx_realurl_advanced' => $extpath . 'class.tx_realurl_advanced.php',
   'tx_realurl_modfunc1' => $extpath . 'modfunc1/class.tx_realurl_modfunc1.php',
   'tx_realurl_pagebrowser' => $extpath . 'modfunc1/class.tx_realurl_pagebrowser.php',
   'tx_realurl_autoconfgen' => $extpath . 'class.tx_realurl_autoconfgen.php',
);
