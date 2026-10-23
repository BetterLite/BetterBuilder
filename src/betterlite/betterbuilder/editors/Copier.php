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

namespace betterlite\betterbuilder\editors;

use betterlite\betterbuilder\blockstorage\BlockStorageHolder;
use betterlite\betterbuilder\blockstorage\helpers\BlockArrayIteratorHelper;
use betterlite\betterbuilder\BetterBuilder;
use betterlite\betterbuilder\editors\object\FillSession;
use betterlite\betterbuilder\editors\object\UpdateResult;
use betterlite\betterbuilder\math\Transform;
use betterlite\betterbuilder\session\SessionManager;
use betterlite\betterbuilder\utils\Timer;
use pocketmine\math\Axis;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

/** @deprecated */
class Copier {
	use SingletonTrait;

	public const DIRECTION_PLAYER = 0;
	public const DIRECTION_UP = 1;
	public const DIRECTION_DOWN = 2;

	public function paste(Player $player): UpdateResult {
		$timer = new Timer();

		$clipboard = SessionManager::getInstance()->getSession($player)->getClipboardHolder()->getClipboard();
		if($clipboard === null) {
			return UpdateResult::error("Clipboard is empty");
		}

		/** @phpstan-var Vector3 $relativePosition */
		$relativePosition = $clipboard->getRelativePosition();

		$fillSession = new FillSession($player->getWorld(), true, true);

		$motion = $player->getPosition()->add(0.5, 0, 0.5)->subtractVector($relativePosition);

		$floorX = $motion->getFloorX();
		$floorY = $motion->getFloorY();
		$floorZ = $motion->getFloorZ();

		$iterator = new BlockArrayIteratorHelper($clipboard->getBlockStorage());
		while($iterator->hasNext()) {
			$iterator->readNext($x, $y, $z, $fullBlockId);
			$fillSession->setBlockAt($floorX + $x, $floorY + $y, $floorZ + $z, $fullBlockId);
		}

		$fillSession->reloadChunks($player->getWorld());
		$fillSession->close();

		SessionManager::getInstance()->getSession($player)->getReverseDataHolder()->saveUndo(new BlockStorageHolder($fillSession->getChanges(), $player->getWorld()));

		return UpdateResult::success($fillSession->getBlocksChanged(), $timer->time());
	}

	public function rotate(Player $player, int $axis, int $rotation): void {
		$clipboard = SessionManager::getInstance()->getSession($player)->getClipboardHolder()->getClipboard();
		if($clipboard === null) {
			$player->sendMessage(BetterBuilder::getPrefix() . "§cYour clipboard is empty");
			return;
		}

		$transform = new Transform($clipboard);
		if($axis === Axis::Y) {
			$transform->rotateY($rotation);
		} elseif($axis === Axis::X) {
			$transform->rotateX($rotation);
		} else {
			$transform->rotateZ($rotation);
		}

		SessionManager::getInstance()->getSession($player)->getClipboardHolder()->setClipboard($transform->collectChanges());
	}

	public function flip(Player $player, int $axis): void {
		$clipboard = SessionManager::getInstance()->getSession($player)->getClipboardHolder()->getClipboard();
		if($clipboard === null) {
			$player->sendMessage(BetterBuilder::getPrefix() . "§cYour clipboard is empty");
			return;
		}

		$transform = new Transform($clipboard);
		if($axis === Axis::X) {
			$transform->flipX();
		} elseif($axis === Axis::Y) {
			$transform->flipY();
		} else {
			$transform->flipZ();
		}

		SessionManager::getInstance()->getSession($player)->getClipboardHolder()->setClipboard($transform->collectChanges());
	}
}
