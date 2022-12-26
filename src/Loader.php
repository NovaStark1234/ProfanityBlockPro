<?php

declare(strict_types=1);

namespace nsnguyenvuong\profanityblock;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase implements Listener {
	protected function onEnable() : void{
		$this->initProfanityList($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->profanity = $this->getProfanityList();

	}

	public function initProfanityList(string $dataFolder) : void{
		if(!file_exists($dataFolder . 'profanity.list')) {
			$this->saveResource('profanity.list');
		}
	}

	public function getProfanityList() : array{
		$dataFolder = $this->getDataFolder();
		$this->initProfanityList($dataFolder);
		$text = [];
		$file = new \SplFileObject($dataFolder . "profanity.list");
		while (!$file->eof()) {
		    $text[] = strtolower(preg_replace('/\s/', '', str_replace(PHP_EOL, '', $file->fgets())));
		}
		$file = null;
		return $text;
	}

	public function onChat(PlayerChatEvent $event) : void{
		$chat = $event->getMessage();
		preg_match_all('/ *\w(?:\s*\w*)*/mui', $chat, $matches);
		$text = strtolower(preg_replace('/\s/', '', implode(' ', $matches[0])));
		foreach($this->profanity as $profanity) {
			if(strpos($text, $profanity) !== false) {
				$event->getPlayer()->sendMessage(TF::RED . 'You cannot chat that profanity word(s)!');
				$event->cancel();
				break;
			}
		}
	}
}
