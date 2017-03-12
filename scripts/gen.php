<?php

/**
 * 生成文件列表
 *
 * php scripts/gen.php
 */

define('CPHALCON_DIR', __DIR__.'/../../Budbud/');
echo __DIR__;

/**
 * Class FileGenerator
 */
class FileGenerator
{

    protected $files = array();

    /**
     * @param $directory
     */
    public function __construct($directory)
    {
        $recursiveDirectoryIterator = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);

        /** @var $iterator RecursiveDirectoryIterator[] */
        $iterator = new RecursiveIteratorIterator($recursiveDirectoryIterator);

        foreach ($iterator as $item) {

            if ($item->getExtension() == 'm4a') {
				$this->files[] = $item;
            }
        }
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}

$api = new FileGenerator(CPHALCON_DIR);

$files = $api->getFiles();

@unlink('index.html');
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>芽芽成長故事</title>
    <script src="./audiojs/audio.min.js"></script>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./includes/index.css" media="screen">
    <script>
      audiojs.events.ready(function() {
        audiojs.createAll();
      });
    </script>
  </head>
  <body>
    <header>
      <h1>芽芽媽媽講故事</h1>
    </header>

	<div class="container">
		<div class="table-responsive">
			<table class="table">
HTML;
file_put_contents('index.html', $html.PHP_EOL, FILE_APPEND);
$last_dirname = '';
foreach ($files as $file)
{
	$filename = $file->getFilename();
	$path = str_replace(CPHALCON_DIR, '', $file->getPathname());
	$path_parts = pathinfo($path);
	$dirname = isset($path_parts['dirname']) ? $path_parts['dirname'] : '';
	if ($last_dirname != $dirname) {
		if (!empty($last_dirname)) {
			$html = <<<HTML
				</tbody>
				<thead class="thead-inverse">
					<tr>
						<th scope="row" colspan="2">$dirname</th>
					</tr>
				</thead>
				<tbody>
HTML;
		} else {
			$html = <<<HTML
				<thead class="thead-inverse">
					<tr>
						<th scope="row" colspan="2">$dirname</th>
					</tr>
				</thead>
				<tbody>
HTML;
		}
		file_put_contents('index.html', $html.PHP_EOL, FILE_APPEND);
		$last_dirname = $dirname;
	}

	$html = <<<HTML
					<tr>
						<th scope="row">{$filename}</th>
						<td>
							<audio src="https://github.com/dreamsxin/Budbud/raw/master/{$path}" preload="none"></audio>
						</td>
					</tr>
HTML;
    file_put_contents('index.html', $html.PHP_EOL, FILE_APPEND);
}

$html = <<<HTML
				</tbody>
			</table>
		</div>
	</div>

    <footer>
		<p>This site is ©copyright <a href="http://myleft.org">Myleft Studio</a>, 2016.</p>
    </footer>
  </body>
</html>
HTML;
file_put_contents('index.html', $html, FILE_APPEND);
