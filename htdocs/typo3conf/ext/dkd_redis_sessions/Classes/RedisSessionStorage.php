<?php
/**
 * Created by IntelliJ IDEA.
 * User: dkd-kahler
 * Date: 22.08.13
 * Time: 16:05
 * To change this template use File | Settings | File Templates.
 */

namespace TYPO3\CMS\DkdRedisSessions;
use TYPO3\CMS\Core\Cache\Backend\RedisBackend;
use TYPO3\CMS\Core\Session;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Implements Redis as TYPO3\CMS\Core\Session\StorageInterface
 * @package TYPO3\CMS\DkdRedisSessions
 */
class RedisSessionStorage extends \TYPO3\CMS\Core\Service\AbstractService implements Session\StorageInterface {

	/**
	 * @var \TYPO3\CMS\Core\Cache\Backend\RedisBackend $backend the Redis CacheBackend
	 */
	protected $backend;
	/**
	 * Connect to Redis DB
	 * @return bool|void
	 */
	public function init() {
		switch($this->info['requestedServiceSubType']) {
			case 'frontend':
			case 'backend':
				$subtype = $this->info['requestedServiceSubType'];
				break;
			default:
				return FALSE;
		}
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dkd_redis_sessions']);
		list($hostname, $port) = GeneralUtility::trimExplode(':', $extConf[$subtype . '_server']);
		$this->backend = GeneralUtility::makeInstance(
			'TYPO3\\CMS\\Core\\Cache\\Backend\\RedisBackend',
			'',
			array(
				'hostname' => $hostname,
				'port' => $port,
				'database' => intval($extConf[$subtype . '_db'])
			)
		);
		if ($this->backend instanceof RedisBackend) {
			try {
				$this->backend->initializeObject();
			}
			catch(\TYPO3\CMS\Core\Cache\Exception $e) {
				GeneralUtility::sysLog($e->getMessage(), 'dkd_redis_sessions', GeneralUtility::SYSLOG_SEVERITY_WARNING);
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Fetch session data
	 *
	 * @param string $identifier the session ID
	 * @return Session\Data session data object
	 */
	public function get($identifier) {
		return $this->backend->get($identifier);
	}

	/**
	 * Store session data
	 *
	 * @param Session\Data $sessionData
	 * @return boolean TRUE on success
	 */
	public function put(Session\Data $sessionData) {
		// TODO: Implement put() method.
	}

	/**
	 * Delete session data
	 *
	 * @param string $identifier the session ID
	 * @return boolean TRUE on success
	 */
	public function delete($identifier) {
		// TODO: Implement delete() method.
	}

	/**
	 * Garbage collection removes outdated session data
	 *
	 * @return void
	 */
	public function collectGarbage() {
		// TODO: Implement collectGarbage() method.
	}

}