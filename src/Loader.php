<?php

declare(strict_types=1);

namespace nsnguyenvuong\profanityblock;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase implements Listener {
	private array $profanity;

	protected function onEnable() : void{
		$this->initProfanityList();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->profanity = $this->getProfanityList();

	}
	
	public function stripVN($str) { // For convert Vietnamese unicode to latin code
	    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
	    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
	    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
	    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
	    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
	    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
	    $str = preg_replace("/(đ)/", 'd', $str);
	    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
	    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
	    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
	    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
	    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
	    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
	    $str = preg_replace("/(Đ)/", 'D', $str);
	    return $str;
	}

	public function initProfanityList() : void{
		$dataFolder = $this->getDataFolder();
		if(!file_exists($dataFolder . 'profanity.list')) {
			$this->saveResource('profanity.list');
		}
	}

	public function getProfanityList() : array{
		$dataFolder = $this->getDataFolder();
		$this->initProfanityList();
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
		preg_match_all('/ *\w(?:\s*\w*)*/mui', $this->stripVN($chat), $matches);
		$text = strtolower(preg_replace('/\s/', '', implode(' ', $matches[0])));
		foreach($this->profanity as $profanity) {
			if(strpos($text, $profanity) !== false) {
				$event->getPlayer()->sendMessage(TF::RED . 'You cannot chat that profanity word!');
				$event->cancel();
				break;
			}
		}
	}
}
