<?php

namespace TYPO3\CMS\DkdRedisSessions\Plugin;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;


class Frontendtest extends AbstractPlugin {

	public $prefixId = 'tx_dkdredissessions_frontendtest';

	/**
	 * @var TypoScriptFrontendController $tsfe
	 */
	protected $tsfe;

	public function main($content, $conf) {
		$this->tsfe = $GLOBALS['TSFE'];
		$template = trim($this->getTemplate());

		$this->updateSession();

		$markers = array(
			'prefix' => $this->prefixId,
			'action' => $this->cObj->typoLink_URL(array('parameter' => $this->tsfe->id)),
			'session_payload' => $this->tsfe->fe_user->getKey('ses', $this->prefixId),
			'payload' => isset($this->piVars['payload']) ? $this->piVars['payload'] : '',
		);

		$content = $this->cObj->substituteMarkerArray($template, $markers, '###|###');

		return $content;
	}


	protected function updateSession() {
		if (isset($this->piVars['put'])) {
			$this->tsfe->fe_user->setKey('ses', $this->prefixId, nl2br($this->piVars['payload']));
		}
		if (isset($this->piVars['delete'])) {
			$this->tsfe->fe_user->setKey('ses', $this->prefixId, NULL);
		}

	}


	protected function getTemplate() {
		return '
<div style="width: 35em;">
	<h3>Edit content</h3>
	<form action="###action###" method="post" style="position: inline;">
		<textarea name="###prefix###[payload]" cols="40" rows="8" style="width:100%; height: 5em;">###payload###</textarea><br />
		<button type="submit" name="###prefix###[post]">Show</button> <button type="submit" name="###prefix###[put]">Save</button> <button type="reset">Undo</button>
		<button type="submit" name="###prefix###[delete]" style="margin-left: 10em;">Flush session data</button>
	</form>
	<h3>Saved content</h3>
	<div style="width:100%; height: 5em; border: 1px solid #884; overflow: scroll;">###session_payload###</div>
</div>
		';
	}

}