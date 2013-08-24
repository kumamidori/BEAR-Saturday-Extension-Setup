<?php
/**
 */
use Composer\Script\Event;

/**
 */
class Installer
{
    public static function postInstall(Event $event = null)
    {
        $projectRoot = dirname(__DIR__);
        self::pearLocalSetup($projectRoot);
    }

    public static function pearLocalSetup($fullPath)
    {
        $libsPath = $fullPath . '/' . 'libs';
        if(file_exists($libsPath . '/.pearrc')) {
            echo "Error: path {$libsPath}/.pearrc already exists." . PHP_EOL;
            return;
        }
        mkdir($libsPath);
        chdir($libsPath);

        $commands = array();
        $commands[] = "pear config-create {$libsPath} .pearrc";
        $commands[] = "pear -c {$libsPath}/.pearrc config-set auto_discover 1";
        $commands[] = "pear -c {$libsPath}/.pearrc config-set preferred_state alpha";
        $commands[] = "pear -c {$libsPath}/.pearrc channel-update pear.php.net";
        $commands[] = "pear -c {$libsPath}/.pearrc install PEAR";
        // 以降はグローバルのpearコマンドは使わない。古いpear環境だと下記エラーが出るため。
        // （pear/HTTP_Request2 requires PEAR Installer (version >= 1.9.2), installed version is 1.9.1）
        $commands[] = "{$libsPath}/pear/pear -c {$libsPath}/.pearrc channel-discover pear.bear-project.net";
        // -a は不要
        $commands[] = "{$libsPath}/pear/pear -c {$libsPath}/.pearrc install bear/BEAR-beta";

        echo '***** Installing...' . PHP_EOL;
        foreach($commands as $cmd) {
            echo '** ' . $cmd . . ' **' . PHP_EOL;
            system($cmd, $retval);
        }
        echo '***** Completed.' . PHP_EOL;
    }
}