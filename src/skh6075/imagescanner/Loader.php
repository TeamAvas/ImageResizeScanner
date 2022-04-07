<?php

declare(strict_types=1);

namespace skh6075\imagescanner;

use pocketmine\plugin\PluginBase;
use Webmozart\PathUtil\Path;

final class Loader extends PluginBase{

	protected function onEnable() : void{
		$this->getServer()->getAsyncPool()->submitTask(new ImageResizeAsyncTask(
			Path::join($this->getServer()->getDataPath(), "conv"),
			$this->getServer()->getLogger(),
			16, 26));
	}
}