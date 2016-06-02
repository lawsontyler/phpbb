<?php

namespace phpbb\attachment\drivers\storage;

use phpbb\files\filespec;

/**
 * Class driver_interface
 * Driver interface for storing attachments
 * @package phpbb\attachment\drivers\storage
 */
interface driver_interface
{
	public function read(filespec $file);

	public function write(filespec $file, $data);
}