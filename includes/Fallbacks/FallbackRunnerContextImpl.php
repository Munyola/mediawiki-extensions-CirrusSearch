<?php

namespace CirrusSearch\Fallbacks;

use CirrusSearch\Search\ResultSet;

/**
 * Basic implementation of a FallbackRunnerContext.
 * Should only be visible by FallbackRunner as its states should be closely
 * maintained by the FallbackRunner.
 */
class FallbackRunnerContextImpl implements FallbackRunnerContext {
	/**
	 * Initial ResultSet as returned by the main search query
	 * @var ResultSet (final)
	 */
	private $initialResultSet;

	/**
	 * The resultset as returned by the last call to FallbackMethod::rewrite()
	 * @var ResultSet (mutable)
	 */
	private $previousResultSet;

	/**
	 * FallbackRunnerContextImpl constructor.
	 * @param ResultSet $initialResultSet
	 */
	public function __construct( ResultSet $initialResultSet ) {
		$this->initialResultSet = $initialResultSet;
		$this->previousResultSet = $initialResultSet;
	}

	/**
	 * Initialize the previous resultset
	 * (only visible by FallbackRunner)
	 * @param ResultSet $previousResultSet
	 */
	public function setPreviousResultSet( ResultSet $previousResultSet ) {
		$this->previousResultSet = $previousResultSet;
	}

	/**
	 * @return ResultSet
	 */
	public function getInitialResultSet() {
		return $this->initialResultSet;
	}

	/**
	 * @return ResultSet
	 */
	public function getPreviousResultSet() {
		return $this->previousResultSet;
	}
}
