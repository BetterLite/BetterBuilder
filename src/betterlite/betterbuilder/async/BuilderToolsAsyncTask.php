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

namespace betterlite\betterbuilder\async;

use betterlite\betterbuilder\BetterBuilder;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\AsyncWorker;
use pocketmine\Server;
use Throwable;

abstract class BuilderToolsAsyncTask extends AsyncTask {

	private string $error = "";

	public function __construct() {
	}

	abstract public function execute(): void;

	final public function onRun(): void {
		try {
			$this->execute();
		} catch(Throwable $error) {
			$this->error = $error->getMessage();
		}
	}

	/**
	 * This function is called on main thread before calling
	 * callback function
	 */
	public function complete(): void {
	}

	/** @noinspection PhpUnused */
	final public function onCompletion(): void {
		$this->complete();
		AsyncQueue::callCallback($this);
	}

	protected function getLogger(): \Logger {
		$thread = AsyncWorker::getCurrentThread();
		if($thread instanceof AsyncWorker) {
			return $thread->getLogger();
		}

		if(class_exists(BetterBuilder::class, false)) {
			try {
				return BetterBuilder::getInstance()->getLogger();
			} catch(Throwable) {
			}
		}

		return Server::getInstance()->getLogger();
	}

	public function getErrorMessage(): ?string {
		return $this->error === "" ? null : $this->error;
	}
}