<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGit;

use Symfony\Component\Process\Process;
use function array_merge;
use function defined;
use function explode;
use function ltrim;
use function parse_url;
use function preg_match;
use function str_contains;
use function str_starts_with;
use function substr;
use function trim;

/**
 * Class GitUtil
 *
 * @package PhpGit
 */
class GitUtil
{
    /**
     * This method is used to create a process object.
     *
     * @param string $command command of the `bin_name`
     * @param array $args command args
     * @param array $options process options
     *                       - timeout
     *                       - bin_name
     *                       - work_dir
     *                       - env_vars
     *
     * @return Process
     */
    public static function newProcess(string $command, array $args = [], array $options = []): Process
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_BUILD');
        $options   = array_merge([
            'cmd_line' => '', // NOTICE: if is not empty, will ignore `bin_name`, $command, $args
            'work_dir' => null,
            'bin_name' => 'git',
            'timeout'  => 3600,
            'env_vars' => $isWindows ? ['PATH' => getenv('PATH')] : [],
        ], $options);

        // use full command line. eg: git symbolic-ref --short -q HEAD
        if ($cmdLine = $options['cmd_line']) {
            $process = Process::fromShellCommandline($cmdLine);
        } else {
            $cmdWithArgs = array_merge([$options['bin_name'], $command], $args);

            $process = new Process($cmdWithArgs);
        }

        $process->setEnv($options['env_vars']);
        $process->setTimeout($options['timeout']);
        $process->setIdleTimeout($options['timeout']);

        if ($dir = $options['work_dir']) {
            $process->setWorkingDirectory($dir);
        }

        return $process;
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public static function parseRemoteUrl(string $url): array
    {
        // eg: git@gitlab.my.com:group/some-lib.git
        if (str_starts_with($url, 'git@')) {
            $type = 'git';

            // remove suffix
            if (str_ends_with($url, '.git')) {
                $str = substr($url, 4, -4);
            } else {
                $str = substr($url, 4);
            }

            // $url = gitlab.my.com:group/some-lib
            [$host, $path] = explode(':', $str, 2);
            [$group, $repo] = explode('/', $path, 2);

            return [
                'url'    => $url,
                'type'   => $type,
                'scheme' => $type,
                'host'   => $host,
                'path'   => $path,
                'group'  => $group,
                'repo'   => $repo,
            ];
        }

        // eg: "https://github.com/phppkg/swoft-component.git"
        $info = parse_url($url);
        // add
        $info['url']  = $url;
        $info['type'] = 'http';

        $uriPath = $info['path'];
        if (str_ends_with($uriPath, '.git')) {
            $uriPath = substr($uriPath, 0, -4);
        }

        $info['path'] = trim($uriPath, '/');

        [$group, $repo] = explode('/', $info['path'], 2);

        $info['group'] = $group;
        $info['repo']  = $repo;

        return $info;
    }

    /**
     * '/(?<current>\*| ) (?<name>[^\s]+) +((?:->) (?<alias>[^\s]+)|(?<hash>[0-9a-z]{7}) (?<message>.*))/'
     */
    // public const PATTERN_BR_LINE = '/(?<current>\* )(?<name>\S+) +((?:->) (?<alias>\S+)|(?<hash>[0-9a-z]{4,41}) (?<message>.*))/';
    public const PATTERN_BR_LINE = '/(?<current>\* )?(?<name>\S+) +(?<hash>[0-9a-z]{4,41}) (?<message>.*)/';

    /**
     * @param string $line
     * @param bool $verbose
     *
     * @return array{name: string, current: bool, alias: string, hash: string, message:string}
     */
    public static function parseBranchLine(string $line, bool $verbose = true): array
    {
        $branch = [
            'current' => false,
            'name'    => '',
            'hash'    => '',
            'message' => '', // hash message
            'alias'   => '',
        ];

        if (!$verbose) {
            // eg:
            // * current_branch
            //  another_branch
            $line = trim($line);
            if (str_starts_with($line, '*')) {
                $branch['current'] = true;
                // clear starts
                $line = ltrim($line, '*\t ');
            }

            $branch['name'] = $line;
            return $branch;
        }

        // is verbose. eg: * BRANCH_NAME  COMMIT_ID  COMMIT_MSG
        preg_match(self::PATTERN_BR_LINE, $line, $matches);

        // up from: https://github.com/kzykhys/PHPGit/pull/15/files
        if (isset($matches['current'])) {
            $branch['current'] = trim($matches['current']) === '*';
        }

        // full name with remote. eg: remotes/origin/NAME
        if (isset($matches['name'])) {
            $branch['name'] = $matches['name'];
        }

        if (isset($matches['alias'])) {
            $branch['alias'] = $matches['alias'];
        }
        if (isset($matches['hash'])) {
            $branch['hash'] = $matches['hash'];
        }
        if (isset($matches['message'])) {
            $branch['message'] = $matches['message'];
        }

        return $branch;
    }
    /**
     * @param string $str
     * @return boolean
     */
    public static function isFullUrl(string $str): bool
    {
        if (str_starts_with($str, 'http://')) {
            return true;
        }

        if (str_starts_with($str, 'https://')) {
            return true;
        }

        if (str_starts_with($str, 'git@')) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path
     *
     * @return string[]
     */
    public static function splitPath(string $path): array
    {
        if (str_contains($path, '/')) {
            return explode('/', $path, 2);
        }

        // as repo name.
        return ['', $path];
    }
}
