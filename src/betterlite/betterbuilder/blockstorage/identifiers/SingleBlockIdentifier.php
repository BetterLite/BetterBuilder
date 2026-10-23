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

use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\VanillaBlocks;

class SingleBlockIdentifier implements BlockIdentifierList {

	protected int $typeId;
	protected ?int $stateId = null;

	public function __construct(int $typeId, ?int $stateId = null) {
		$this->typeId = $typeId;
		$this->stateId = $stateId;
	}

	public static function fromBlock(Block $block, bool $exactState = true): SingleBlockIdentifier {
		return new self($block->getTypeId(), $exactState ? $block->getStateId() : null);
	}

	public function nextBlock(?int &$fullBlockId): void {
		if($this->stateId !== null) {
			$fullBlockId = $this->stateId;
			return;
		}

		foreach(RuntimeBlockStateRegistry::getInstance()->getAllKnownStates() as $state) {
			if($state->getTypeId() === $this->typeId) {
				$this->stateId = $state->getStateId();
				$fullBlockId = $this->stateId;
				return;
			}
		}

		$fullBlockId = VanillaBlocks::AIR()->getStateId();
	}

	public function containsBlock(int $fullBlockId): bool {
		if($this->stateId !== null) {
			return $fullBlockId === $this->stateId;
		}

		return RuntimeBlockStateRegistry::getInstance()->fromStateId($fullBlockId)->getTypeId() === $this->typeId;
	}

	public function containsBlockId(int $id): bool {
		return $this->typeId === $id;
	}

	public static function airIdentifier(): SingleBlockIdentifier {
		$air = VanillaBlocks::AIR();
		return new SingleBlockIdentifier($air->getTypeId(), $air->getStateId());
	}
}