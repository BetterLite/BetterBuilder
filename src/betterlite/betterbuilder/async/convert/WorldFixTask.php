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

namespace betterlite\betterbuilder\async\convert;

use betterlite\betterbuilder\async\BuilderToolsAsyncTask;
use betterlite\betterbuilder\editors\Fixer;
use betterlite\betterbuilder\utils\Timer;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\world\format\io\WorldProviderManager;
use RuntimeException;
use function ceil;
use function get_class;
use function is_dir;
use function round;
use const DIRECTORY_SEPARATOR;

class WorldFixTask extends BuilderToolsAsyncTask {
	public string $worldPath;

	public int $progressPercentage = 0;

	public bool $isTaskDone = false;

	public float $totalTime = 0.0;
	public int $totalChunkCount = 0;

	public bool $forceStop = false;

	public function __construct(string $worldPath) {
		parent::__construct();
		$this->worldPath = $worldPath;
	}

	/** @noinspection PhpUnused */
	public function execute(): void {
		$this->getLogger()->debug("Fixing world $this->worldPath...");
		if(!is_dir($this->worldPath)) {
			throw new RuntimeException("File $this->worldPath not found");
		}

		$providerManager = new WorldProviderManager();
		$worldProviderManagerEntry = null;
		foreach($providerManager->getMatchingProviders($this->worldPath) as $worldProviderManagerEntry) {
			break;
		}

		if($worldProviderManagerEntry === null) {
			throw new RuntimeException("Unknown world provider");
		}

		$provider = $worldProviderManagerEntry->fromPath($this->worldPath . DIRECTORY_SEPARATOR);

		if(!$provider instanceof LevelDB) {
			throw new RuntimeException("World provider " . get_class($provider) . " is not supported.");
		}

		$timer = new Timer();

		$chunksFixed = 0;
		$this->totalChunkCount = $provider->calculateChunkCount();

		$this->getLogger()->debug("Discovered $this->totalChunkCount chunks");

		$fixer = Fixer::getInstance();
		foreach($provider->getAllChunks(true, $this->getLogger()) as $coords => $loadedChunk) {
			$chunkData = $loadedChunk->getData();
			foreach($chunkData->getSubChunks() as $subChunk) {
				if($subChunk->isEmptyFast()) {
					continue;
				}
				for($y = 0; $y < 16; ++$y) {
					for($x = 0; $x < 16; ++$x) {
						for($z = 0; $z < 16; ++$z) {
							$fullBlock = $subChunk->getBlockStateId($x, $y, $z);
							if($fixer->convertJavaToBedrockId($fullBlock)) {
								$subChunk->setBlockStateId($x, $y, $z, $fullBlock);
							}
						}
					}
				}
			}

			$provider->saveChunk($coords[0], $coords[1], $chunkData, Chunk::DIRTY_FLAGS_ALL);

			$percentage = (int)ceil((++$chunksFixed) * 100 / $this->totalChunkCount);
			if($this->progressPercentage !== $percentage) {
				$this->getLogger()->debug("Fixing world $this->worldPath... $this->progressPercentage% done");
				$this->progressPercentage = $percentage;
			}

			if($this->forceStop) {
				return;
			}
		}

		$this->totalTime = round($timer->time(), 2);
		$this->isTaskDone = true;
	}
}
