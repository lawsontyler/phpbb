<?php

namespace phpbb\attachment\drivers\input;

use phpbb\attachment\drivers\input\upload\delete as delete_helper;
use phpbb\attachment\drivers\input\upload\resync as resync_helper;
use phpbb\attachment\drivers\input\upload\upload as upload_helper;
use phpbb\user;

class upload implements driver_interface
{
	/** @var delete_helper */
	protected $delete;

	/** @var resync_helper */
	protected $resync;

	/** @var upload_helper */
	protected $upload;

	public function __construct(delete_helper $delete, resync_helper $resync, upload_helper $upload)
	{
		$this->delete = $delete;
		$this->resync = $resync;
		$this->upload = $upload;
	}

	public function get_name()
	{
		return 'attachment.driver.input.upload';
	}

	public function get_config_name()
	{
		return 'attachments_input_upload';
	}

	public function prepare_form_acp(user $user)
	{
		return array();
	}


	public function delete($mode, $ids, $resync = true)
	{
		return $this->delete->delete($mode, $ids, $resync);
	}

	public function upload()
	{
		// TODO: Implement upload() method.
	}

	public function resync()
	{
		// TODO: Implement resync() method.
	}


}