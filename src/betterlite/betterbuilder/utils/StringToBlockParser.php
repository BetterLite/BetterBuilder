<?php

declare(strict_types=1);

namespace betterlite\betterbuilder\utils;

use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\StringToTParser;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use function is_string;
use function preg_match;

/**
 * Handles parsing blocks from strings.
 *
 * @phpstan-extends StringToTParser<Block>
 */
final class StringToBlockParser extends StringToTParser {
	use SingletonTrait;

	/** @phpstan-ignore-next-line */
	private static function make(): self {
		$result = new self;

		$result->register("air", fn() => VanillaBlocks::AIR());

		foreach(StringToItemParser::getInstance()->getKnownAliases() as $alias) {
			if(!is_string($alias)) {
				continue;
			}

			$item = StringToItemParser::getInstance()->parse($alias);
			if($item === null) {
				continue;
			}

			$block = $item->getBlock();
			if($block->hasSameTypeId(VanillaBlocks::AIR())) {
				continue;
			}

			$result->register($alias, fn() => $item->getBlock());
		}

		return $result;
	}

	public function parse(string $input): ?Block {
		$block = parent::parse($input);
		if($block !== null) {
			return $block;
		}

		if(preg_match('/^(\d+)(?::(\d+))?$/', $input, $matches)) {
			$id = (int)$matches[1];
			$meta = isset($matches[2]) ? (int)$matches[2] : 0;
			try {
				$blockData = GlobalBlockStateHandlers::getUpgrader()->upgradeIntIdMeta($id, $meta);
				$stateId = GlobalBlockStateHandlers::getDeserializer()->deserialize($blockData);
				return RuntimeBlockStateRegistry::getInstance()->fromStateId($stateId);
			} catch(\Throwable) {
				return null;
			}
		}

		$item = StringToItemParser::getInstance()->parse($input);
		if($item !== null) {
			$block = $item->getBlock();
			if(!$block->hasSameTypeId(VanillaBlocks::AIR())) {
				return $block;
			}
		}

		return null;
	}
}