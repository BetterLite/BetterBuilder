<?php

/**
 * Copyright (C) 2018-2022  CzechPMDevs
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace betterlite\betterbuilder\event\listener;

use betterlite\betterbuilder\blockstorage\OfflineSession;
use betterlite\betterbuilder\BetterBuilder;
use betterlite\betterbuilder\editors\Printer;
use betterlite\betterbuilder\session\SessionManager;
use betterlite\betterbuilder\utils\WorldFixUtil;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\item\Axe;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use RuntimeException;
use function array_key_exists;
use function microtime;

class EventListener implements Listener {

	/** @var float[] */
	private array $clickTime = [];

	/** @noinspection PhpUnused */
	public function onAirClick(PlayerItemUseEvent $event): void {
		$session = SessionManager::getInstance()->getSession($player = $event->getPlayer());
		if(!$session->isDrawing()) {
			return;
		}

		$targetBlock = $player->getTargetBlock(BetterBuilder::getConfiguration()->getIntProperty("max-ray-trace-distance"));
		if($targetBlock === null) {
			return;
		}

		$position = $targetBlock->getPosition();

		Printer::getInstance()->draw($player, $position, $player->getInventory()->getItemInHand()->getBlock(), $session->getDrawBrush(), $session->getDrawMode(), $session->getDrawFall());
		$event->cancel();
	}

	/** @noinspection PhpUnused */
	public function onBlockBreak(BlockBreakEvent $event): void {
		if(
			$event->getItem()->getNamedTag()->getTag("buildertools") !== null &&
			$event->getItem() instanceof Axe
		) {
			$selection = SessionManager::getInstance()->getSession($event->getPlayer())->getSelectionHolder();
			try {
				$selection->handleWandAxeBlockBreak($event->getBlock()->getPosition());
			} catch(RuntimeException $exception) {
				$event->getPlayer()->sendMessage(BetterBuilder::getPrefix() . "§c{$exception->getMessage()}");
				return;
			}
			$event->cancel();

			try {
				$size = " ({$selection->size()})";
			} catch(RuntimeException) {
				$size = "";
			}

			$position = $event->getBlock()->getPosition()->floor();
			$event->getPlayer()->sendMessage(BetterBuilder::getPrefix() . "§aSelected first position at {$position->getX()}, {$position->getY()}, {$position->getZ()}$size");
		}
	}

	/** @noinspection PhpUnused */
	public function onBlockTouch(PlayerInteractEvent $event): void {
		if($event->getItem()->getNamedTag()->getTag("buildertools") !== null && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
			$player = $event->getPlayer();
			$item = $event->getItem();
			if(array_key_exists($player->getName(), $this->clickTime) && microtime(true) - $this->clickTime[$player->getName()] < 0.5) {
				$event->cancel();
				return;
			}
			$this->clickTime[$player->getName()] = microtime(true);

			if($item instanceof Axe) {
				$selection = SessionManager::getInstance()->getSession($event->getPlayer())->getSelectionHolder();
				try {
					$selection->handleWandAxeBlockClick($event->getBlock()->getPosition());
				} catch(RuntimeException $exception) {
					$event->getPlayer()->sendMessage(BetterBuilder::getPrefix() . "§c{$exception->getMessage()}");
					return;
				}
				$event->cancel();

				try {
					$size = " ({$selection->size()})";
				} catch(RuntimeException) {
					$size = "";
				}

				$position = $event->getBlock()->getPosition()->floor();
				$event->getPlayer()->sendMessage(BetterBuilder::getPrefix() . "§aSelected second position at {$position->getX()}, {$position->getY()}, {$position->getZ()}$size");
				return;
			}

			if($event->getItem()->getTypeId() === VanillaItems::STICK()->getTypeId()) {
				$event->cancel();

				$block = $event->getBlock();
				$pos = $block->getPosition();
				$world = $pos->getWorld();

				$player->sendTip(
					"§aType ID: §7" . $block->getTypeId() . " (State: " . $block->getStateId() . ")\n" .
					"§aName: §7" . $block->getName() . "\n" .
					"§aPosition: §7" . $pos->getFloorX() . ";" . $pos->getFloorY() . ";" . $pos->getFloorZ() . " (" . ($pos->getFloorX() >> 4) . ";" . ($pos->getFloorZ() >> 4) . ")\n" .
					"§aWorld: §7" . $world->getDisplayName() . "\n" .
					"§aBiome: §7" . $world->getBiomeId($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ()) . " (" . $world->getBiome($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ())->getName() . ")"
				);
			}
		}
	}

	/** @noinspection PhpUnused */
	public function onLevelLoad(WorldLoadEvent $event): void {
		if(WorldFixUtil::isInWorldFixQueue($event->getWorld()->getFolderName())) {
			Server::getInstance()->getWorldManager()->unloadWorld($event->getWorld(), true);
		}
	}

	/** @noinspection PhpUnused */
	public function onQuit(PlayerQuitEvent $event): void {
		SessionManager::getInstance()->closeSession($event->getPlayer());
	}

	public function getPlugin(): BetterBuilder {
		return BetterBuilder::getInstance();
	}
}