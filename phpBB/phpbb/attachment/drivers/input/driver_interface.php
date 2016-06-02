<?php

namespace phpbb\attachment\drivers\input;

use phpbb\user;

interface driver_interface
{

	/**
	 * Unique name of the service in the YAML
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Unique name to use when dealing with the config service
	 * @return string
	 */
	public function get_config_name();

	/**
	 * Stuff for the ACP
	 * @param user $user
	 * @return array
	 */
	public function prepare_form_acp(user $user);

	/**
	 * Delete an attachment
	 *
	 * @param string $mode
	 * @param array $ids
	 * @param bool $resync
	 * @return mixed
	 */
	public function delete($mode, $ids, $resync = true);

	/**
	 * Upload an attachment
	 *
	 * @return mixed
	 */
	public function upload();

	/**
	 * No clue.  Sync something.
	 * @return mixed
	 */
	public function resync();
	
}