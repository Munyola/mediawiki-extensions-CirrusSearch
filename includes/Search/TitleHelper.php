<?php

namespace CirrusSearch\Search;

use CirrusSearch\SearchConfig;
use Title;
use CirrusSearch\InterwikiResolver;
use MediaWiki\MediaWikiServices;

/**
 * Utility class build MW Title from elastica Result/ResultSet classes
 * This class can be used in all classes that need to build a Title
 * by reading the elasticsearch output.
 */
class TitleHelper {
	/**
	 * @var SearchConfig
	 */
	private $config;

	/**
	 * @var InterwikiResolver
	 */
	private $interwikiResolver;

	/**
	 * @param SearchConfig|null $config
	 * @param InterwikiResolver|null $interwikiResolver
	 */
	public function __construct( SearchConfig $config = null, InterwikiResolver $interwikiResolver = null ) {
		$this->config = $config ?: MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'CirrusSearch' );
		$this->interwikiResolver = $interwikiResolver ?: MediaWikiServices::getInstance()->getService( InterwikiResolver::SERVICE );
	}

	/**
	 * Create a title. When making interwiki titles we should be providing the
	 * namespace text as a portion of the text, rather than a namespace id,
	 * because namespace id's are not consistent across wiki's. This
	 * additionally prevents the local wiki from localizing the namespace text
	 * when it should be using the localized name of the remote wiki.
	 *
	 * @param \Elastica\Result $r int $namespace
	 * @return Title
	 */
	public function makeTitle( \Elastica\Result $r ) {
		$iwPrefix = $this->identifyInterwikiPrefix( $r );
		if ( empty( $iwPrefix ) ) {
			return Title::makeTitle( $r->namespace, $r->title );
		} else {
			$nsPrefix = $r->namespace_text ? $r->namespace_text . ':' : '';
			return Title::makeTitle( 0, $nsPrefix . $r->title, '', $iwPrefix );
		}
	}

	/**
	 * Build a Title to a redirect, this always works for internal titles.
	 * For external titles we need to use the namespace_text which is only
	 * valid if the redirect namespace is equals to the target title namespace.
	 * If the namespaces do not match we return null.
	 *
	 * @param \Elastica\Result $r
	 * @param string $redirectText
	 * @param int $redirNamespace
	 * @return Title|null the Title to the Redirect or null if we can't build it
	 */
	public function makeRedirectTitle( \Elastica\Result $r, $redirectText, $redirNamespace ) {
		$iwPrefix = self::identifyInterwikiPrefix( $r );
		if ( empty( $iwPrefix ) ) {
			return Title::makeTitle( $redirNamespace, $redirectText );
		}
		if ( $redirNamespace === $r->namespace ) {
			$nsPrefix = $r->namespace_text ? $r->namespace_text . ':' : '';
			return Title::makeTitle(
				0,
				$nsPrefix . $redirectText,
				'',
				$iwPrefix
			);
		} else {
			// redir namespace does not match, we can't
			// build this title.
			// The caller should fallback to the target title.
			return null;
		}
	}

	/**
	 * @param \Elastica\Result $r
	 * @return bool true if this result refers to an external Title
	 */
	public function isExternal( \Elastica\Result $r ) {
		if ( isset( $r->wiki ) && $r->wiki !== $this->config->getWikiId() ) {
			return true;
		}
		// no wiki is suspicious, should we log a warning?
		return false;
	}

	/**
	 * @param \Elastica\Result $r
	 * @return string|null the interwiki prefix for this result or null or
	 * empty if local.
	 */
	private function identifyInterwikiPrefix( $r ) {
		if ( isset( $r->wiki ) && $r->wiki !== $this->config->getWikiId() ) {
			return $this->interwikiResolver->getInterwikiPrefix( $r->wiki );
		}
		// no wiki is suspicious, should we log something?
		return null;
	}
}
