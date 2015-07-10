<?php 
namespace QueueVideo\Threads;
use Thread;

class Timeout extends Thread {
	private $seconds;
	private $clients;

	public function __construct($seconds, &$clients){
		$this->seconds = $seconds;
		$this->clients = $clients;
	}

	public function run(){
		echo "Child Timeout Thread waiting " . $this->seconds . " seconds.\n";
		sleep($this->seconds);
		$outgoingMessageArray = array(
			'method' => 'requestNewVideo'
		);
		foreach ($this->clients as $client) {
            $client->send(json_encode($outgoingMessageArray));
            break;
        }
	}
}