<?php


class Meeting {

	private $trellobot;
	private $channel;


	public function __construct($channel, $trellobot)
	{
		$this->trellobot = $trellobot;
		$this->channel = $channel;

		$this->trellobot->sendMsg("Meeting started", $this->channel);
	}

	public function getChannel()
	{
		return $this->channel;
	}
}