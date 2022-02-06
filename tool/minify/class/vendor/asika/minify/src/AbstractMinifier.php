<?php
/**
 * Part of minify project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Asika\Minifier;

/**
 * The AbstractMinifier class.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractMinifier implements MinifierInterface
{
	const FLAGGED_COMMENTS = 'flaggedComments';

	/**
	 * Property files.
	 *
	 * @var  array
	 */
	protected $files = [];

	/**
	 * Contains the default options for minification. This array is merged with
	 * the one passed in by the user to create the request specific set of
	 * options (stored in the $options attribute).
	 *
	 * @var array
	 */
	protected static $defaultOptions = ['flaggedComments' => true];

	/**
	 * process
	 *
	 * @param string $fileOrContent
	 * @param array  $options
	 *
	 * @return  string
	 */
	public static function process($fileOrContent, array $options = [])
	{
		$minifier = new static;

		if (strlen($fileOrContent) <= PHP_MAXPATHLEN && is_file($fileOrContent))
		{
			$minifier->addFile($fileOrContent, $options);
		}
		else
		{
			$minifier->addContent($fileOrContent, $options);
		}

		return $minifier->minify();
	}

	/**
	 * minify
	 *
	 * @return  string
	 */
	public function minify()
	{
		$data = '';

		foreach ($this->files as $fileData)
		{
			$fileData = $this->handleFile($fileData);

			$data .= $this->doMinify($fileData['content'], $fileData['options']);
		}

		return $data;
	}

	/**
	 * doMinify
	 *
	 * @param string $content
	 * @param array  $options
	 *
	 * @return  string
	 */
	abstract protected function doMinify($content, array $options = []);

	/**
	 * handleFile
	 *
	 * @param array $fileData
	 *
	 * @return  array
	 */
	protected function handleFile(array $fileData)
	{
		if ($fileData['file'] !== null)
		{
			$fileData['content'] = file_get_contents($fileData['file']);
		}

		return $fileData;
	}

	/**
	 * toFile
	 *
	 * @param string $file
	 *
	 * @return static
	 */
	public function toFile($file)
	{
		$minified = $this->minify();

		// Create dir
		$dir = dirname($file);

		if (!is_dir($dir))
		{
			if (is_file($dir))
			{
				throw new \RuntimeException(sprintf('Path: %s is already a file.', $dir));
			}

			if (!@mkdir($dir, 0755, true) && !is_dir($dir))
			{
				throw new \RuntimeException(sprintf('Auto create dir: %s fail.', $dir));
			}
		}

		file_put_contents($file, $minified);

		return $this;
	}

	/**
	 * loadContent
	 *
	 * @param string $content
	 * @param array  $options
	 *
	 * @return  static
	 */
	public function addContent($content, array $options = [])
	{
		$this->files[] = [
			'content' => $content,
			'file' => null,
			'options' => $options
		];

		return $this;
	}

	/**
	 * loadFile
	 *
	 * @param string $file
	 * @param array  $options
	 *
	 * @return  static
	 */
	public function addFile($file, array $options = [])
	{
		$this->files[] = [
			'content' => null,
			'file' => $file,
			'options' => $options
		];

		return $this;
	}
}
