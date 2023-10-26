<?php
require Yii::app()->basePath.DIRECTORY_SEPARATOR . 'aws' . DIRECTORY_SEPARATOR . 'aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

class AwsS3Service extends Service
{
	public function log($l)
    {
        $tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'aws_S3' . DIRECTORY_SEPARATOR ;
        if (!is_dir($tmp)) {
            mkdir($tmp);
        }
		file_put_contents($tmp.'aws_S3_'  . date('Y-m-d') . '.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
    }

	public function uploadFile()
	{
		$s3Client = new S3Client([
			'profile' => 'default',
			'region' => 'ap-southeast-2',
			'version' => '2006-03-01'
		]);

		$bucket = 'tla-my-production';
		$file_path = Yii::app()->basePath . DIRECTORY_SEPARATOR . "filerepo" . DIRECTORY_SEPARATOR;
		$subFolders = array_diff(scandir($file_path), array('.', '..'));

		foreach ($subFolders as $folder) {
			$path = Yii::app()->basePath . DIRECTORY_SEPARATOR . "filerepo" . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
			$files = array_diff(scandir($path), array('.', '..'));
			foreach ($files as $file) {
				$file = $path . $file;
				$key = basename($file);

				if (!$s3Client->doesObjectExist($bucket, $key)) {
					try {
						$result = $s3Client->putObject([
							'Bucket' => $bucket,
							'Key' => $key,
							'SourceFile' => $file,
						]);
						$this->log(json_encode($result->__toString()));
						// Check does file exists on S3 server before delete local file
						if ($s3Client->doesObjectExist($bucket,$key)) {
							unlink($file);
						}
					} catch (S3Exception $e) {
						$this->log($e->getMessage() . "\n");
					}
				} else {
					$this->log("File " . $key . " already exists.");
				}
			}
		}
	}

	public function uploadEmailAttatchments()
	{
		$s3Client = new S3Client([
			'profile' => 'default',
			'region' => 'ap-southeast-2',
			'version' => '2006-03-01'
		]);

		$bucket = 'tla-my-production';
		$file_path = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'importsmail' . DIRECTORY_SEPARATOR;
		$files = array_diff(scandir($file_path), array('.', '..'));

		foreach ($files as $file) {
			$file  = $file_path . $file;
			$key = basename($file);

			if (!$s3Client->doesObjectExist($bucket, $key)) {
				try {
					$result = $s3Client->putObject([
						'Bucket' => $bucket,
						'Key' => $key,
						'SourceFile' => $file,
					]);
					$this->log(json_encode($result->__toString()));
					// Check does file exists on S3 server before delete local file
					if ($s3Client->doesObjectExist($bucket,$key)) {
						unlink($file);
					}
				} catch (S3Exception $e) {
					$this->log($e->getMessage() . "\n");
				}
			} else {
				$this->log("File " . $key . " already exists.");
			}
		}
	}

	public function fileExist($key)
	{
		$s3Client = new S3Client([
			'profile' => 'default',
			'region' => 'ap-southeast-2',
			'version' => '2006-03-01'
		]);
		$bucket = 'tla-my-production';
		if ($s3Client->doesObjectExist($bucket, $key)) {
			return true;
		} else {
			return false;
		}
	}

	public function downloadFile($filerepo)
	{
		$client = new S3Client([
			'profile' => 'default',
			'region' => 'ap-southeast-2',
			'version' => '2006-03-01'
		]);

		$client->registerStreamWrapper();
		$data = file_get_contents('s3://tla-my-production/'.$filerepo->hash);
		return $data;
	}

	public function downloadEmailAttatchment($mail)
	{
		$client = new S3Client([
			'profile' => 'default',
			'region' => 'ap-southeast-2',
			'version' => '2006-03-01'
		]);

		$client->registerStreamWrapper();
		$data = file_get_contents('s3://tla-my-production/' . $mail->no . '.emz');
		return $data;
	}
}