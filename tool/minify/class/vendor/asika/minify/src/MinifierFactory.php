<?php
/**
 * Part of minify project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Asika\Minifier;

/**
 * The MinifierFactory class.
 *
 * @since  __DEPLOY_VERSION__
 */
class MinifierFactory
{
	/**
	 * create
	 *
	 * @param string $type
	 *
	 * @return  MinifierInterface
	 */
	public static function create($type)
	{
		if (strtolower($type) === 'javascript')
		{
			$type = 'js';
		}

		$class = sprintf(__NAMESPACE__ . '\%sMinifier', ucfirst($type));

		if (!class_exists($class))
		{
			throw new \DomainException(sprintf('Class %s not found.', $class));
		}

		return new $class;
	}
}
