<?php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']=array (
  '_DEFAULT' => 
  array (
    'init' => 
    array (
      'enableCHashCache' => true,
      'appendMissingSlash' => 'ifNotFile,redirect',
      'adminJumpToBackend' => true,
      'enableUrlDecodeCache' => true,
      'enableUrlEncodeCache' => true,
      'emptyUrlReturnValue' => '/',
    ),
    'pagePath' => 
    array (
      'type' => 'user',
      'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
      'spaceCharacter' => '-',
      'languageGetVar' => 'L',
			'rootpage_id' => 1,
			'expireDays' => 3,
    ),
    'fileName' => 
    array (
      'defaultToHTMLsuffixOnPrev' => 0,
      'acceptHTMLsuffix' => 1,
      'index' => 
      array (
        'print' => 
        array (
          'keyValues' => 
          array (
            'type' => 98,
          ),
        ),
      ),
    ),
    'preVars' => 
    array (
      0 => 
      array (
        'GETvar' => 'L',
        'valueMap' => 
        array (
          1 => '1',
        ),
        'noMatch' => 'bypass',
      ),
    ),
    'postVarSets' => array(
      '_DEFAULT' => array(
        'article' => array(
          array(
            'GETvar' => 'tx_news_pi1[news]',
            'lookUpTable' => array(
              'table' => 'tx_news_domain_model_news',
              'id_field' => 'uid',
              'alias_field' => 'title',
              'addWhereClause' => ' AND NOT deleted AND NOT hidden',
              'useUniqueCache' => 1,
              'useUniqueCache_conf' => array(
                'strtolower' => 1,
                'spaceCharacter' => '-',
              ),
            ),
          ),
          array(
            'GETvar' => 'tx_news_pi1[controller]',
            'noMatch' => 'null',
            'default' => 'News',
          ),
          array(
            'GETvar' => 'tx_news_pi1[action]',
            'noMatch' => 'null',
            'default' => 'detail',
          ),
        ),
      ),
    ),
  ),
);
?>