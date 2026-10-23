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

namespace betterlite\betterbuilder\schematics\format;

use betterlite\betterbuilder\blockstorage\BlockArray;
use betterlite\betterbuilder\schematics\SchematicException;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\ByteTag;
use Throwable;
use function serialize;
use function unserialize;
use function zlib_decode;

/**
 * Experimental schematic format
 *
 * It should be used to transfer schematics between servers with BuilderTools
 * - This format is a way faster than other ones due to compatibility with internal BlockArray format
 */
class BuilderToolsSchematic implements Schematic {

	public function load(string $rawData): BlockArray {
		$blockArray = unserialize($rawData);
		if(!$blockArray instanceof BlockArray) {
			throw new SchematicException("Invalid data provided");
		}

		return $blockArray;
	}

	public function save(BlockArray $blockArray): string {
		return serialize($blockArray);
	}

	public static function getFileExtension(): string {
		return ".btschematics";
	}

	public static function validate(string $rawData): bool {
		if(str_starts_with($rawData, "O:47:\"betterlite\\buildertools\\blockstorage\\BlockArray\"")) {
			return true;
		}

		try {
			$unserialized = @unserialize($rawData);
			if($unserialized instanceof BlockArray) {
				return true;
			}
		} catch(Throwable) {
		}

		try {
			$decoded = @zlib_decode($rawData);
			if($decoded === false) {
				return false;
			}

			$nbt = (new BigEndianNbtSerializer())->read($decoded)->mustGetCompoundTag();

			return $nbt->getTag("Coords") instanceof ByteArrayTag && $nbt->getTag("Blocks") instanceof ByteArrayTag && $nbt->getTag("DuplicateDetection") instanceof ByteTag;

		} catch(Throwable) {
			return false;
		}
	}
}