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

namespace betterlite\betterbuilder\blockstorage\identifiers;

use pocketmine\block\Liquid;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\AssumptionFailedError;
use function in_array;

class LiquidBlockIdentifier implements BlockIdentifierList {
	/** @var int[] */
	private array $typeIds = [];

	public function __construct() {
		$this->typeIds = [
			VanillaBlocks::WATER()->getTypeId(),
			VanillaBlocks::LAVA()->getTypeId()
		];
	}

	public function nextBlock(?int &$fullBlockId): void {
		throw new AssumptionFailedError("nextBlock does not work with LiquidBlockIdentifier");
	}

	public function containsBlock(int $fullBlockId): bool {
		return RuntimeBlockStateRegistry::getInstance()->fromStateId($fullBlockId) instanceof Liquid;
	}

	public function containsBlockId(int $id): bool {
		return in_array($id, $this->typeIds, true);
	}
}