<?php

declare(strict_types=1);

namespace skh6075\imagescanner;

use AttachableThreadedLogger;
use pocketmine\scheduler\AsyncTask;
use PrefixedLogger;
use skh6075\lib\imageresize\convert\utils\ImageResizeResult;
use skh6075\lib\imageresize\ImageResize;
use Webmozart\PathUtil\Path;

final class ImageResizeAsyncTask extends AsyncTask{

	private PrefixedLogger $logger;

	private int $imageCount;

	private int $successCount;

	private int $failureCount;

	public function __construct(
		private string $path,
		AttachableThreadedLogger $threadedLogger,
		private int $width,
		private int $height
	){
		$this->logger = new PrefixedLogger($threadedLogger, "ImageScanner");
	}

	public function onRun() : void{
		$this->logger->info($this->path . " 경로의 이미지를 스캔합니다.");
		if(!is_dir($this->path)){
			$this->logger->warning("스캔은 폴더만 가능합니다.");
			$this->cancelRun();
			return;
		}
		$files = array_diff(scandir($this->path), ['.', '..']);
		foreach($files as $file){
			$filePath = Path::join($this->path, $file);
			if(!is_file($filePath)){
				continue;
			}
			$extension = pathinfo($filePath, PATHINFO_EXTENSION);
			if($extension === 'png' || $extension === 'jpg' || $extension === 'gif'){
				$this->imageCount++;
				$image = ImageResize::converter($filePath);
				$image->resizing($filePath, $this->width, $this->height);
				$this->logger->info($file . " 이미지 컨버팅 중..");
				if($image->getResult() === ImageResizeResult::SUCCESS()){
					$this->successCount++;
				}else $this->failureCount ++;
			}
		}
	}

	public function onCompletion() : void{
		$this->logger->notice("확인된 이미지 파일 (" . $this->imageCount . "개)");
		$this->logger->notice("컨버팅 성공 파일: " . $this->successCount . "개, 실패 파일: " . $this->failureCount . "개");
	}
}