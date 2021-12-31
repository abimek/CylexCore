<?php

declare(strict_types=1);

namespace muqsit\simplepackethandler\monitor;

use Closure;
use pocketmine\plugin\Plugin;

final class PacketMonitor implements IPacketMonitor{

	/** @var PacketMonitorListener */
	private $listener;

	public function __construct(Plugin $register, bool $handleCancelled){
		$this->listener = new PacketMonitorListener($register, $handleCancelled);
	}

	public function monitorIncoming(Closure $handler) : IPacketMonitor{
		$this->listener->monitorIncoming($handler);
		return $this;
	}

	public function monitorOutgoing(Closure $handler) : IPacketMonitor{
		$this->listener->monitorOutgoing($handler);
		return $this;
	}
}