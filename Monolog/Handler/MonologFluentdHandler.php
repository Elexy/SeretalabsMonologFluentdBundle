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

namespace Seretalabs\MonologFluentdBundle\Monolog\Handler;

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
	 * @var FluentLogger|bool
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
		$port   = FluentLogger::DEFAULT_LISTEN_PORT,
		$host   = FluentLogger::DEFAULT_ADDRESS,
		$level = Logger::DEBUG,
		$bubble = true,
		$env = 'dev_ak',
		$tag = 'backend')
	{
		$this->port = $port;
		$this->host = $host;
		$this->env = $env;
		$this->tag = $tag;

        if (!$this->host) {
            $this->logger = false; // disable logging if host is not provided
        }

		parent::__construct($level, $bubble);
	}

    private function lazyLoadLogger() {

        if ($this->logger || ($this->logger === false)) {
            // return if FluentLogger is already loaded or failed to load
            return $this->logger;
        }

        // Ensure service failure does not compromise the app
        try {
            $this->logger = new FluentLogger($this->host, $this->port);
        }
        catch (\Exception $e) {
            $this->logger = false;
        }

        return $this->logger;
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
        if (!$this->lazyLoadLogger()) {
            return;
        }

		if (isset($record['context']) && isset($record['context']['tag'])) {
			$tag = $record['context']['tag'];
		} else {
			$tag  = $this->tag;
		}
		$tag = $tag . '.' . $this->env;

		if (isset($record['formatted']) && is_array($record['formatted'])) {
			$data = $record['formatted'];
		} else {
			$data = $record;
		}

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
