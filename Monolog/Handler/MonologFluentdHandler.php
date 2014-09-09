<?php
/*
 * Landingi
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Seretalabs\Bundle\MonologFluentdBundle\Monolog\Handler;

use Fluent\Logger\FluentLogger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;

/**
 * A fluentd log handler for Symfony. It based on Dakatsuka's monolog fluent extension
 * to push log entries to your fluentd deamon
 * https://github.com/dakatsuka/MonologFluentHandler
 *
 * @author Alex Knol <alex@seretalabs.com>
 */
class MonologFluentdHandler extends AbstractProcessingHandler
{
	/**
	 * @var FluentLogger
	 */
	private $logger;
	/**
	 * @var int
	 */
	private $port;
	/**
	 * @var string
	 */
	private $host;

	/**
	 * Initialize Handler
	 *
	 * @param bool|string $host
	 * @param int $port
	 * @param int $level
	 * @param bool $bubble
	 */
	public function __construct(
		$host   = FluentLogger::DEFAULT_ADDRESS,
		$port   = FluentLogger::DEFAULT_LISTEN_PORT,
		$level = Logger::DEBUG,
		$bubble = true)
	{
		$this->port = $port;
		$this->host = $host;

		parent::__construct($level, $bubble);

		$logger = new FluentLogger($host, $port);

		$this->logger = $logger;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handleBatch(array $records)
	{
		$messages = array();

		foreach ($records as $record) {
			if ($record['level'] < $this->level) {
				continue;
			}
			$messages[] = $this->processRecord($record);
		}

		if (!empty($messages)) {
			foreach($messages as $message) {
				$this->write($message);
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function write(array $record)
	{
		$tag  = $record['channel'] . '.' . $record['message'];
		$data = $record['context'];
		$data['level'] = Logger::getLevelName($record['level']);

		$this->logger->post($tag, $data);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter()
	{
		return new JsonFormatter;
	}
}
