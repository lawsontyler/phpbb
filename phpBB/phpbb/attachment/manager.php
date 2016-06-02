<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace phpbb\attachment;
use phpbb\attachment\drivers\input\driver_interface as input_driver_interface;
use phpbb\attachment\drivers\storage\driver_interface as storage_driver_interface;
use phpbb\config\config;

/**
 * Attachment manager
 */
class manager
{
	/** @var input_driver_interface[] */
	protected $input_drivers;

	/** @var storage_driver_interface[] */
	protected $storage_drivers;

	/** @var input_driver_interface[] */
	protected static $enabled_input_drivers;

	/** @var storage_driver_interface[] */
	protected static $enabled_storage_drivers;

	/**
	 * Constructor for attachment manager
	 *
	 * @param config $config
	 * @param array $input_drivers
	 * @param array $storage_drivers
	 */
	public function __construct(config $config, $input_drivers, $storage_drivers)
	{
		$this->config = $config;
		$this->input_drivers = $input_drivers;
	}

	/**
	 * Returns the names of all the known drivers
	 * @return string[]
	 */
	public function get_all_input_drivers()
	{
		$drivers = array();

		if (!empty($this->input_drivers))
		{
			foreach ($this->input_drivers as $driver)
			{
				$drivers[$driver->get_name()] = $driver->get_name();
			}
			asort($drivers);
		}

		return $drivers;
	}

	/**
	 * Get the driver object specified by the avatar type
	 *
	 * @param string $attachment_type Avatar type; by default an avatar's service container name
	 * @param bool $load_enabled Load only enabled avatars
	 *
	 * @return input_driver_interface Avatar driver object
	 */
	public function get_input_driver($attachment_type, $load_enabled = true)
	{
		if (self::$enabled_input_drivers === false)
		{
			$this->load_enabled_input_drivers();
		}

		$avatar_drivers = ($load_enabled) ? self::$enabled_input_drivers : $this->get_all_input_drivers();

		if (!isset($avatar_drivers[$attachment_type]))
		{
			return null;
		}

		/*
		* There is no need to handle invalid avatar types as the following code
		* will cause a ServiceNotFoundException if the type does not exist
		*/
		$driver = $this->input_drivers[$attachment_type];

		return $driver;
	}

	/**
	 * Load the list of enabled drivers
	 * This is executed once and fills self::$enabled_drivers
	 */
	protected function load_enabled_input_drivers()
	{
		if (!empty($this->input_drivers))
		{
			self::$enabled_input_drivers = array();
			foreach ($this->input_drivers as $driver)
			{
				if ($this->is_enabled($driver))
				{
					self::$enabled_input_drivers[$driver->get_name()] = $driver->get_name();
				}
			}
			asort(self::$enabled_input_drivers);
		}
	}

	/**
	 * Check if attachment driver is enabled
	 *
	 * @param input_driver_interface $driver Attachment driver object
	 *
	 * @return bool True if avatar is enabled, false if it's disabled
	 */
	public function is_enabled($driver)
	{
		$config_name = $driver->get_config_name();

		return $this->config["allow_attachment_{$config_name}"];
	}

	/**
	 * Get the settings array for enabling/disabling an attachment driver
	 *
	 * @param input_driver_interface $driver Attachment driver object
	 *
	 * @return array Array of configuration options as consumed by acp_board
	 */
	public function get_attachment_settings($driver)
	{
		$config_name = $driver->get_config_name();

		return array(
			'allow_attachment_' . $config_name => array(
				'lang' => 'ALLOW_' . strtoupper(str_replace('\\', '_', $config_name)),
				'validate' => 'bool',
				'type' => 'radio:yes_no',
				'explain' => false
			),
		);
	}
	
	

	/**
	 * Wrapper method for deleting attachments
	 *
	 * @param string $mode can be: post|message|topic|attach|user
	 * @param mixed $ids can be: post_ids, message_ids, topic_ids, attach_ids, user_ids
	 * @param bool $resync set this to false if you are deleting posts or topics
	 *
	 * @return int|bool Number of deleted attachments or false if something
	 *			went wrong during attachment deletion
	 */
	public function delete($mode, $ids, $resync = true)
	{
		$this->get_storage_driver()->delete($mode, $ids, $resync);
	}

	/**
	 * Wrapper method for deleting attachments from filesystem
	 *
	 * @param string $filename Filename of attachment
	 * @param string $mode Delete mode
	 * @param bool $entry_removed Whether entry was removed. Defaults to false
	 * @return bool True if file was removed, false if not
	 */
	public function unlink($filename, $mode = 'file', $entry_removed = false)
	{
		return $this->delete->unlink_attachment($filename, $mode, $entry_removed);
	}

	/**
	 * Wrapper method for resyncing specified type
	 *
	 * @param string $type Type of resync
	 * @param array $ids IDs to resync
	 */
	public function resync($type, $ids)
	{
		$this->resync->resync($type, $ids);
	}

	/**
	 * Wrapper method for uploading attachment
	 *
	 * @param string			$form_name		The form name of the file upload input
	 * @param int			$forum_id		The id of the forum
	 * @param bool			$local			Whether the file is local or not
	 * @param string			$local_storage	The path to the local file
	 * @param bool			$is_message		Whether it is a PM or not
	 * @param array		$local_filedata	An file data object created for the local file
	 *
	 * @return array File data array
	 */
	public function upload($form_name, $forum_id, $local = false, $local_storage = '', $is_message = false, $local_filedata = [])
	{
		return $this->upload->upload($form_name, $forum_id, $local, $local_storage, $is_message, $local_filedata);
	}
}
