<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thorsten Kahler <thorsten.kahler@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace TYPO3\CMS\DkdRedisSessions;

use TYPO3\CMS\Core\Session;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Cache\Backend\RedisBackend;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Implements Redis as \TYPO3\CMS\Core\Session\StorageInterface
 * @package TYPO3\CMS\DkdRedisSessions
 */
abstract class RedisSessionStorage extends \TYPO3\CMS\Core\Service\AbstractService implements Session\StorageInterface {

	/**
	 * @var RedisBackend $backend the Redis CacheBackend
	 */
	protected $backend;

	/**
	 * @var string $subtype the service subtype
	 */
	protected $subtype;


	/**
	 * Connect to Redis DB
	 * @return bool|void
	 */
	public function init() {
		try {
			if (
				!isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dkd_redis_sessions'])
				|| ! is_string($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dkd_redis_sessions'])
			) {
				throw new \TYPO3\CMS\Core\Cache\Exception('Missing extension configuration for "dkd_redis_sessions"');
			}
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dkd_redis_sessions']);

			if (!isset($extConf[$this->subtype . '_server'])
				|| ! is_string($extConf[$this->subtype . '_server'])
			) {
				throw new \TYPO3\CMS\Core\Cache\Exception('Wrong extension configuration for "' . $this->subtype . '_server' . '".');
			}
			list($hostname, $port) = GeneralUtility::trimExplode(':', $extConf[$this->subtype . '_server']);

			if (!isset($extConf[$this->subtype . '_db'])) {
				throw new \TYPO3\CMS\Core\Cache\Exception(
					'Missing database configuration for subtype "' . $this->subtype . '".'
				);
			}
			$database = intval($extConf[$this->subtype . '_db']);

			if ($database < 1) {
					// check valid database id and avoid conflicts with PhpUnit
				throw new \TYPO3\CMS\Core\Cache\Exception(
					'Wrong database "' . $extConf[$this->subtype . '_db'] . '" configured for subtype "'
					. $this->subtype . '". Use integer > 1!'
				);
			}

			$redisOptions = array(
				'hostname' => $hostname,
				'database' => $database
			);
			if (MathUtility::canBeInterpretedAsInteger($port)) {
				$redisOptions['port'] = $port;
			}

			$this->backend = GeneralUtility::makeInstance(
				'TYPO3\\CMS\\Core\\Cache\\Backend\\RedisBackend',
				'',
				$redisOptions
			);
			if ($this->backend instanceof RedisBackend) {
				$this->backend->initializeObject();
			}
		}
		catch(\TYPO3\CMS\Core\Cache\Exception $e) {
			GeneralUtility::sysLog($e->getMessage(), 'dkd_redis_sessions', GeneralUtility::SYSLOG_SEVERITY_WARNING);
			return FALSE;
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
		$session = NULL;
		$rawSession = $this->backend->get($identifier);
		if ($rawSession) {
			$data = unserialize($rawSession);
			if ($data['identifier'] === $identifier) {
				$session = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Session\\Data');
				$session->setContent($data['content']);
				$session->setTimeout($data['timeout']);
				$session->setIdentifier($identifier);
			}
		}
		return $session;
	}

	/**
	 * Store session data
	 *
	 * @param Session\Data $sessionData
	 * @return boolean TRUE on success
	 */
	public function put(Session\Data $sessionData) {
		try {
			$data = array(
				'identifier' => $sessionData->getIdentifier(),
				'content' => $sessionData->getContent(),
				'timeout' => $sessionData->getTimeout(),
			);
			$this->backend->set($sessionData->getIdentifier(), serialize($data), array(), $sessionData->getTimeout() - time());
		}
		catch (\InvalidArgumentException $e) {
			GeneralUtility::sysLog($e->getMessage(), 'dkd_redis_sessions', GeneralUtility::SYSLOG_SEVERITY_WARNING);
			return FALSE;
		}
		catch (\TYPO3\CMS\Core\Cache\Exception\InvalidDataException $e) {
			GeneralUtility::sysLog($e->getMessage(), 'dkd_redis_sessions', GeneralUtility::SYSLOG_SEVERITY_ERROR);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Delete session data
	 *
	 * @param string $identifier the session ID
	 * @return boolean TRUE on success
	 */
	public function delete($identifier) {
		$deleted = FALSE;
		try {
			$deleted = $this->backend->remove($identifier);
		}
		catch (\InvalidArgumentException $e) {
			GeneralUtility::sysLog('Invalid session identifier: '  . $e->getMessage(), 'dkd_redis_sessions', GeneralUtility::SYSLOG_SEVERITY_WARNING);
		}
		return $deleted;
	}

	/**
	 * Garbage collection removes outdated session data
	 *
	 * @return void
	 */
	public function collectGarbage() {
		$this->backend->collectGarbage();
	}

}