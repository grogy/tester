<?php

/**
 * This file is part of the Nette Tester.
 * Copyright (c) 2009 David Grudl (https://davidgrudl.com)
 */

namespace Tester\Runner;


/**
 * Test represents one result.
 */
class Test
{
	const
		PREPARED = 0,
		PASSED = 1,
		SKIPPED = 2,
		FAILED = 3;

	/** @var string|NULL */
	public $title;

	/** @var string|NULL */
	public $message;

	/** @var string */
	public $stdout = '';

	/** @var string */
	public $stderr = '';

	/** @var string */
	private $file;

	/** @var int */
	private $result = self::PREPARED;

	/** @var string[]|string[][] */
	private $args = [];


	/**
	 * @param  string
	 * @param  string
	 */
	public function __construct($file, $title = NULL)
	{
		$this->file = $file;
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}


	/**
	 * @return string[]|string[][]
	 */
	public function getArguments()
	{
		return $this->args;
	}


	/**
	 * @param  int
	 * @return string
	 */
	public function getName($maxArgsLen = NULL)
	{
		$name = $this->title ?: basename($this->file);
		$args = implode(' ', array_map(function ($arg) {
			return is_array($arg) ? "$arg[0]=$arg[1]" : $arg;
		}, $this->args));

		if ($args && $maxArgsLen !== NULL && $maxArgsLen < strlen(" [$args]")) {
			$args = substr($args, 0, max(0, $maxArgsLen - 6)) . '...';
		}

		return $name . ($args ? " [$args]" : '');
	}


	/**
	 * @return int
	 */
	public function getResult()
	{
		return $this->result;
	}


	/**
	 * @return bool
	 */
	public function hasResult()
	{
		return $this->result !== self::PREPARED;
	}


	/**
	 * @param  array $args
	 * @return static
	 */
	public function withArguments(array $args)
	{
		if ($this->hasResult()) {
			throw new \LogicException('Cannot change arguments of test which already has a result.');
		}

		$me = clone $this;
		foreach ($args as $name => $values) {
			foreach ((array) $values as $value) {
				$me->args[] = is_int($name)
					? "$value"
					: [$name, "$value"];
			}
		}
		return $me;
	}


	/**
	 * @param  int
	 * @param  string
	 * @return static
	 */
	public function withResult($result, $message)
	{
		if ($this->hasResult()) {
			throw new \LogicException("Result of test is already set to $this->result with message '$this->message'.");
		}

		$me = clone $this;
		$me->result = $result;
		$me->message = $message;
		return $me;
	}

}
