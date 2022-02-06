<?php
/**
 * Part of minify project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Asika\Minifier;

use Asika\Minifier\CSS\CSSmin;
use Asika\Minifier\CSS\UriRewriter;

/**
 * The CssMinifier class.
 *
 * @since  __DEPLOY_VERSION__
 */
class CssMinifier extends AbstractMinifier
{
	const URI_REWRITE = 'uri_rewrite';

	/**
	 * doMinify
	 *
	 * @param string $content
	 * @param array  $options
	 *
	 * @return  string
	 */
	protected function doMinify($content, array $options = [])
	{
		$cssmin = new CSSmin;

		// Flagged comments
		if (isset($options[static::FLAGGED_COMMENTS]))
		{
			$cssmin->setFlaggedComments($options[static::FLAGGED_COMMENTS]);
		}

		// Rewrite
		if (isset($options[static::URI_REWRITE]))
		{
			$content = UriRewriter::rewrite(
				$content,
				isset($options['current_dir']) ? $options['current_dir'] : '',
				isset($options['doc_root']) ? $options['root'] : $_SERVER['DOCUMENT_ROOT']
			);
		}

		return $cssmin->run($content);
	}
}
