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

namespace betterlite\betterbuilder\commands\utility;

use betterlite\betterbuilder\blockstorage\identifiers\SingleBlockIdentifier;
use betterlite\betterbuilder\BetterBuilder;
use betterlite\betterbuilder\commands\BuilderToolsCommand;
use betterlite\betterbuilder\session\SessionManager;
use betterlite\betterbuilder\utils\StringToBlockDecoder;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MaskCommand extends BuilderToolsCommand {
	public function __construct() {
		parent::__construct("/mask", "Set mask rule to selected item(s) so it can only affect a particular blocks.");
	}

	/** @noinspection PhpUnused */
	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!$this->testPermission($sender)) return;
		if(!$sender instanceof Player) {
			$sender->sendMessage("§cThis command can be used only in game!");
			return;
		}

		if(isset($args[0]) && $args[0] === "remove") {
			SessionManager::getInstance()->getSession($sender)->setMask(null);
			$sender->sendMessage(BetterBuilder::getPrefix() . "§aMask removed!");
			return;
		}

		if(!isset($args[0])) {
			$block = $sender->getInventory()->getItemInHand()->getBlock();
			$mask = SingleBlockIdentifier::fromBlock($block);
		} else {
			$mask = new StringToBlockDecoder($args[0], $sender->getInventory()->getItemInHand());
		}

		SessionManager::getInstance()->getSession($sender)->setMask($mask);
		$sender->sendMessage(BetterBuilder::getPrefix() . "§aMask was successfully updated! To remove mask, use §l//mask remove§r§a.");
	}
}