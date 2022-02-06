<?php
/**
 * Part of minify project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Asika\Minifier;

/**
 * Interface MinifierInterface
 *
 * @since  __DEPLOY_VERSION__
 */
interface MinifierInterface
{
	/**
	 * minify
	 *
	 * @return  string
	 */
	public function minify();

	/**
	 * toFile
	 *
	 * @param string $file
	 *
	 * @return static
	 */
	public function toFile($file);

	/**
	 * loadContent
	 *
	 * @param string $content
	 * @param array  $options
	 *
	 * @return  static
	 */
	public function addContent($content, array $options = []);

	/**
	 * loadFile
	 *
	 * @param string $file
	 * @param array  $options
	 *
	 * @return  static
	 */
	public function addFile($file, array $options = []);
}
