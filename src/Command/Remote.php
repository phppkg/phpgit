<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use PhpGit\Concern\AbstractCommand;
use PhpGit\Concern\ExecGitCommandTrait;
use Symfony\Component\OptionsResolver\Options;
use function preg_match;

/**
 * Manage set of tracked repositories - `git remote`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 *
 * @property-read Remote\SetUrl      $url
 * @property-read Remote\SetHead     $head
 * @property-read Remote\SetBranches $branches
 *
 * @method head($name, $branch)                                     Sets the default branch for the named remote
 * @method branches($name, $branches)                               Changes the list of branches tracked by the named remote
 * @method url($name, $newUrl, $oldUrl = null, $options = [])       Sets the URL remote to $newUrl
 */
class Remote extends AbstractCommand
{
    use ExecGitCommandTrait;

    public const TYPE_FETCH = 'fetch';
    public const TYPE_PUSH  = 'push';

    public const COMMANDS = [
        'url'      => Remote\SetUrl::class,
        'head'     => Remote\SetHead::class,
        'branches' => Remote\SetBranches::class,
    ];

    /***
     * Returns an array of existing remotes
     *
     * ### Output Example
     *
     * ``` php
     * [
     *     'origin' => [
     *         'fetch' => 'https://github.com/phppkg/phpgit.git',
     *         'push'  => 'https://github.com/phppkg/phpgit.git'
     *     ]
     * ]
     * ```
     *
     * @return array{string: array}
     */
    public function getList(): array
    {
        $builder = $this->getCommandBuilder('-v');

        $remotes = [];
        $output  = $this->run($builder);
        $lines   = $this->split($output);

        foreach ($lines as $line) {
            if (preg_match('/^(.*)\t(.*)\s\((.*)\)$/', $line, $matches)) {
                if (!isset($remotes[$matches[1]])) {
                    $remotes[$matches[1]] = [];
                }

                $remotes[$matches[1]][$matches[3]] = $matches[2];
            }
        }

        return $remotes;
    }

    /**
     * Returns an array of existing remotes
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->clone('https://github.com/phppkg/phpgit.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $remotes = $git->remote();
     * ```
     *
     * ### Output Example
     *
     * ``` php
     * [
     *     'origin' => [
     *         'fetch' => 'https://github.com/phppkg/phpgit.git',
     *         'push'  => 'https://github.com/phppkg/phpgit.git'
     *     ]
     * ]
     * ```
     *
     * @return array
     */
    public function __invoke(): array
    {
        return $this->getList();
    }

    /**
     * Adds a remote named **$name** for the repository at **$url**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
     * $git->fetch('origin');
     * ```
     *
     * ##### Options
     *
     * - **tags**    (_boolean_) With this option, `git fetch <name>` imports every tag from the remote repository
     * - **no-tags** (_boolean_) With this option, `git fetch <name>` does not import tags from the remote repository
     *
     * @param string $name    The name of the remote
     * @param string $url     The url of the remote
     * @param array  $options [optional] An array of options {@see Remote::setDefaultOptions}
     *
     * @return bool
     */
    public function add(string $name, string $url, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add('add');

        $this->addFlags($builder, $options, ['tags', 'no-tags']);

        $builder->add($name)->add($url);

        $this->run($builder);

        return true;
    }

    /**
     * Rename the remote named **$name** to **$newName**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
     * $git->remote->rename('origin', 'upstream');
     * ```
     *
     * @param string $name    The remote name to rename
     * @param string $newName The new remote name
     *
     * @return bool
     */
    public function rename(string $name, string $newName): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('rename')
            ->add($name)
            ->add($newName);

        $this->run($builder);

        return true;
    }

    /**
     * Remove the remote named **$name**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
     * $git->remote->rm('origin');
     * ```
     *
     * @param string $name The remote name to remove
     *
     * @return bool
     */
    public function rm(string $name): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('rm')
            ->add($name);

        $this->run($builder);

        return true;
    }

    /**
     * Gives some information about the remote **$name**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->clone('https://github.com/phppkg/phpgit.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * echo $git->remote->show('origin');
     * ```
     *
     * ##### Output Example
     *
     * ```
     * \* remote origin
     *   Fetch URL: https://github.com/phppkg/phpgit.git
     *   Push  URL: https://github.com/phppkg/phpgit.git
     *   HEAD branch: master
     *   Remote branch:
     *     master tracked
     *   Local branch configured for 'git pull':
     *     master merges with remote master
     *   Local ref configured for 'git push':
     *     master pushes to master (up to date)
     * ```
     *
     * @param string $name The remote name to show
     *
     * @return string
     */
    public function show(string $name): string
    {
        $builder = $this->getCommandBuilder()->add('show')->add($name);

        return $this->run($builder);
    }

    /**
     * Deletes all stale remote-tracking branches under **$name**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->prune('origin');
     * ```
     *
     * @param string $name The remote name
     *
     * @return bool
     */
    public function prune(string $name = ''): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('prune');

        if ($name) {
            $builder->add($name);
        }

        $this->run($builder);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **tags**    (_boolean_) With this option, `git fetch <name>` imports every tag from the remote repository
     * - **no-tags** (_boolean_) With this option, `git fetch <name>` does not import tags from the remote repository
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'tags'    => false,
            'no-tags' => false
        ]);
    }
}
