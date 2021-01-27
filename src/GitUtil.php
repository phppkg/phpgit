<?php declare(strict_types=1);

namespace PhpGit;

use function explode;
use function parse_url;
use function strpos;
use function substr;

/**
 * Class GitUtil
 *
 * @package PhpGit
 */
class GitUtil
{
    /**
     * @param string $url
     *
     * @return array
     */
    public static function parseRemoteUrl(string $url): array
    {
        // eg: git@gitlab.my.com:group/some-lib.git
        if (strpos($url, 'git@') === 0) {
            $type = 'git';

            if (substr($url, -4) === '.git') {
                $url = substr($url, 4, -4);
            } else {
                $url = substr($url, 4);
            }

            // $url = gitlab.my.com:group/some-lib
            [$host, $path] = explode(':', $url, 2);
            [$group, $repo] = explode('/', $path, 2);

        } else {
            $type = 'http';

            // eg: "https://github.com/ulue/swoft-component.git"
            $info = parse_url($url);
            // add
            $info['url']  = $url;

            $uriPath = $info['path'];
            if (substr($uriPath, -4) === '.git') {
                $uriPath = substr($uriPath, 0, -4);
            }

            $info['path'] = trim($uriPath, '/');

            [$group, $repo] = explode('/', $info['path'], 2);
            $info['group']  = $group;
        }

        return  [
            'url'   => $url,
            'type'   => $type,
            'host'  => $host,
            'path'  => $path,
            'group' => $group,
            'repo'  => $repo,
        ];
    }
}
