<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use BadMethodCallException;
use PhpGit\AbstractCommand;
use PhpGit\Git;
use Symfony\Component\OptionsResolver\Options;

/**
 * Manage set of tracked repositories - `git remote`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 *
 * @method head($name, $branch)                                     Sets the default branch for the named remote
 * @method branches($name, $branches)                               Changes the list of branches tracked by the named remote
 * @method url($name, $newUrl, $oldUrl = null, $options = []) Sets the URL remote to $newUrl
 */
class Remote extends AbstractCommand
{
    /** @var Remote\SetHead */
    public $head;

    /** @var Remote\SetBranches */
    public $branches;

    /** @var Remote\SetUrl */
    public $url;

    /**
     * @param Git $git
     */
    public function __construct(Git $git)
    {
        parent::__construct($git);

        $this->head     = new Remote\SetHead($git);
        $this->branches = new Remote\SetBranches($git);
        $this->url      = new Remote\SetUrl($git);
    }

    /**
     * Calls sub-commands
     *
     * @param string $name      The name of a property
     * @param array  $arguments An array of arguments
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (isset($this->{$name}) && is_callable($this->{$name})) {
            return call_user_func_array($this->{$name}, $arguments);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', __CLASS__, $name));
    }

    /**
     * Returns an array of existing remotes
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->clone('https://github.com/kzykhys/Text.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $remotes = $git->remote();
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     'origin' => [
     *         'fetch' => 'https://github.com/kzykhys/Text.git',
     *         'push'  => 'https://github.com/kzykhys/Text.git'
     *     ]
     * ]
     * ```
     *
     * @return array
     */
    public function __invoke()
    {
        $builder = $this->getCommandBuilder()->add('-v');

        $remotes = [];
        $output  = $this->run($builder->getProcess());
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
     * Adds a remote named **$name** for the repository at **$url**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
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
    public function add($name, $url, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add('add');

        $this->addFlags($builder, $options, ['tags', 'no-tags']);

        $builder->add($name)->add($url);

        $this->run($builder->getProcess());

        return true;
    }

    /**
     * Rename the remote named **$name** to **$newName**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->rename('origin', 'upstream');
     * ```
     *
     * @param string $name    The remote name to rename
     * @param string $newName The new remote name
     *
     * @return bool
     */
    public function rename($name, $newName): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('rename')
            ->add($name)
            ->add($newName);

        $this->run($builder->getProcess());

        return true;
    }

    /**
     * Remove the remote named **$name**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->rm('origin');
     * ```
     *
     * @param string $name The remote name to remove
     *
     * @return bool
     */
    public function rm($name): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('rm')
            ->add($name);

        $this->run($builder->getProcess());

        return true;
    }

    /**
     * Gives some information about the remote **$name**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->clone('https://github.com/kzykhys/Text.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * echo $git->remote->show('origin');
     * ```
     *
     * ##### Output Example
     *
     * ```
     * \* remote origin
     *   Fetch URL: https://github.com/kzykhys/Text.git
     *   Push  URL: https://github.com/kzykhys/Text.git
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
    public function show($name): string
    {
        $builder = $this->getCommandBuilder()
            ->add('show')
            ->add($name);

        return $this->run($builder->getProcess());
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
     * @param null $name The remote name
     *
     * @return bool
     */
    public function prune($name = null): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('prune');

        if ($name) {
            $builder->add($name);
        }

        $this->run($builder->getProcess());

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
