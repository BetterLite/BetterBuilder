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

namespace betterlite\betterbuilder;

use betterlite\betterbuilder\commands\biome\BiomeCommand;
use betterlite\betterbuilder\commands\clipboard\ClearClipboardCommand;
use betterlite\betterbuilder\commands\clipboard\CopyCommand;
use betterlite\betterbuilder\commands\clipboard\CutCommand;
use betterlite\betterbuilder\commands\clipboard\FlipCommand;
use betterlite\betterbuilder\commands\clipboard\MergeCommand;
use betterlite\betterbuilder\commands\clipboard\PasteCommand;
use betterlite\betterbuilder\commands\clipboard\RotateCommand;
use betterlite\betterbuilder\commands\generation\CubeCommand;
use betterlite\betterbuilder\commands\generation\CylinderCommand;
use betterlite\betterbuilder\commands\generation\DrawCommand;
use betterlite\betterbuilder\commands\generation\HollowCubeCommand;
use betterlite\betterbuilder\commands\generation\HollowCylinderCommand;
use betterlite\betterbuilder\commands\generation\HollowPyramidCommand;
use betterlite\betterbuilder\commands\generation\HollowSphereCommand;
use betterlite\betterbuilder\commands\generation\IslandCommand;
use betterlite\betterbuilder\commands\generation\PyramidCommand;
use betterlite\betterbuilder\commands\generation\SphereCommand;
use betterlite\betterbuilder\commands\generation\TreeCommand;
use betterlite\betterbuilder\commands\HelpCommand;
use betterlite\betterbuilder\commands\history\RedoCommand;
use betterlite\betterbuilder\commands\history\UndoCommand;
use betterlite\betterbuilder\commands\region\CenterCommand;
use betterlite\betterbuilder\commands\region\DecorationCommand;
use betterlite\betterbuilder\commands\region\DrainCommand;
use betterlite\betterbuilder\commands\region\FillCommand;
use betterlite\betterbuilder\commands\region\LineCommand;
use betterlite\betterbuilder\commands\region\MoveCommand;
use betterlite\betterbuilder\commands\region\NaturalizeCommand;
use betterlite\betterbuilder\commands\region\OutlineCommand;
use betterlite\betterbuilder\commands\region\ReplaceCommand;
use betterlite\betterbuilder\commands\region\StackCommand;
use betterlite\betterbuilder\commands\region\WallsCommand;
use betterlite\betterbuilder\commands\schematics\SchematicCommand;
use betterlite\betterbuilder\commands\selection\ChunkCommand;
use betterlite\betterbuilder\commands\selection\FirstPositionCommand;
use betterlite\betterbuilder\commands\selection\FirstTargetingPositionCommand;
use betterlite\betterbuilder\commands\selection\SecondPositionCommand;
use betterlite\betterbuilder\commands\selection\SecondTargetingPositionCommand;
use betterlite\betterbuilder\commands\selection\SelectionCommand;
use betterlite\betterbuilder\commands\selection\WandCommand;
use betterlite\betterbuilder\commands\utility\BlockInfoCommand;
use betterlite\betterbuilder\commands\utility\ClearInventoryCommand;
use betterlite\betterbuilder\commands\utility\FixCommand;
use betterlite\betterbuilder\commands\utility\IdCommand;
use betterlite\betterbuilder\commands\utility\MaskCommand;
use betterlite\betterbuilder\event\listener\EventListener;
use betterlite\betterbuilder\math\Math;
use betterlite\betterbuilder\schematics\SchematicsManager;
use betterlite\betterbuilder\utils\IncompatibleConfigException;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use function array_key_exists;
use function glob;
use function is_dir;
use function is_file;
use function is_string;
use function mkdir;
use function rename;
use function unlink;
use function version_compare;

class BetterBuilder extends PluginBase {
	public const CURRENT_CONFIG_VERSION = "1.5.0";

	private static BetterBuilder $instance;
	private static Configuration $configuration;
	private static Limits $limits;

	/** @var Command[] */
	private static array $commands = [];

	protected function onLoad(): void {
	}

	/** @noinspection PhpUnused */
	protected function onEnable(): void {
		BetterBuilder::$instance = $this;

		$this->initConfig();
		$this->cleanCache();
		$this->registerCommands();
		$this->initMath();
		$this->initListener();
		$this->sendWarnings();
		$this->loadSchematicsManager();
	}

	/** @noinspection PhpUnused */
	protected function onDisable(): void {
		$this->cleanCache();
	}

	/**
	 * @throws IncompatibleConfigException
	 */
	private function initConfig(): void {
		if(!is_dir($this->getDataFolder() . "schematics")) {
			@mkdir($this->getDataFolder() . "schematics");
		}
		if(!is_dir($this->getDataFolder() . "sessions")) {
			@mkdir($this->getDataFolder() . "sessions");
		}
		if(!is_dir($this->getDataFolder() . "data")) {
			@mkdir($this->getDataFolder() . "data");
		}
		if(!is_file($this->getDataFolder() . "data/bedrock_block_states_map.json")) {
			$this->saveResource("data/bedrock_block_states_map.json");
		}
		if(!is_file($this->getDataFolder() . "data/java_block_states_map.json")) {
			$this->saveResource("data/java_block_states_map.json");
		}

		$configuration = $this->getConfig()->getAll();
		if(!array_key_exists("config-version", $configuration) || !is_string($version = $configuration["config-version"]) || version_compare($version, BetterBuilder::CURRENT_CONFIG_VERSION) < 0) {
			// Update is required
			@unlink($this->getDataFolder() . "config.yml.old");
			@rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config.yml.old");

			$this->saveResource("config.yml", true);
			$this->getConfig()->reload();

			$this->getLogger()->notice("Config updated. Old config was renamed to 'config.yml.old'.");
		}

		self::$configuration = new Configuration($this->getConfig()->getAll());
		self::$limits = new Limits(
			self::$configuration->getIntProperty("clipboard-limit"),
			self::$configuration->getIntProperty("fill-limit")
		);
	}

	private function initListener(): void {
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	private function initMath(): void {
		Math::init();
	}

	private function registerCommands(): void {
		$map = $this->getServer()->getCommandMap();
		BetterBuilder::$commands = [
			new BiomeCommand,
			new BlockInfoCommand,
			new CenterCommand,
			new ChunkCommand,
			new ClearClipboardCommand,
			new ClearInventoryCommand,
			new CopyCommand,
			new CubeCommand,
			new CutCommand,
			new CylinderCommand,
			new DecorationCommand,
			new DrainCommand,
			new DrawCommand,
			new FillCommand,
			new FirstPositionCommand,
			new FirstTargetingPositionCommand,
			new FixCommand,
			new FlipCommand,
			new HelpCommand,
			new HollowCubeCommand,
			new HollowCylinderCommand,
			new HollowPyramidCommand,
			new HollowSphereCommand,
			new IdCommand,
			new IslandCommand,
			new LineCommand,
			new MergeCommand,
			new MaskCommand,
			new MoveCommand,
			new NaturalizeCommand,
			new OutlineCommand,
			new PasteCommand,
			new PyramidCommand,
			new RedoCommand,
			new ReplaceCommand,
			new RotateCommand,
			new SchematicCommand,
			new SecondPositionCommand,
			new SecondTargetingPositionCommand,
			new SelectionCommand,
			new SphereCommand,
			new StackCommand,
			new TreeCommand,
			new UndoCommand,
			new WallsCommand,
			new WandCommand
		];

		foreach(self::$commands as $command) {
			$map->register("BetterBuilder", $command);
		}

		HelpCommand::buildPages();
	}

	public function registerItems(): void {
	}

	private function sendWarnings(): void {
		if($this->getServer()->getConfigGroup()->getProperty("memory.async-worker-hard-limit") !== 0) {
			$this->getServer()->getLogger()->warning("We recommend to disable 'memory.async-worker-hard-limit' in pocketmine.yml. By disabling this option will be BetterBuilder able to load bigger schematic files.");
		}
	}

	private function loadSchematicsManager(): void {
		SchematicsManager::lazyInit();
	}

	private function cleanCache(): void {
		if(!self::getConfiguration()->getBoolProperty("clean-cache")) {
			return;
		}

		$files = glob($this->getDataFolder() . "sessions/*.dat");
		if($files === false) {
			return;
		}

		/** @var string $offlineSession */
		foreach($files as $offlineSession) {
			unlink($offlineSession);
		}
	}

	/**
	 * @return Command[]
	 */
	public static function getAllCommands(): array {
		return self::$commands;
	}

	public static function getPrefix(): string {
		return "§7[BetterBuilder] §a";
	}

	public static function getConfiguration(): Configuration {
		return self::$configuration;
	}

	public static function getLimits(): Limits {
		return self::$limits;
	}

	public static function getInstance(): BetterBuilder {
		return self::$instance;
	}
}
