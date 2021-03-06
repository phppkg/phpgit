<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGit\Command\Remote;

use PhpGit\Concern\AbstractCommand;
use Symfony\Component\OptionsResolver\Options;

/**
 * Changes URL remote points to
 *
 * @author Kazuyuki Hayashi
 */
class SetUrl extends AbstractCommand
{
    public function getCommandName(): string
    {
        return 'remote';
    }

    /**
     * Alias of set()
     *
     * ```php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
     * $git->remote->url('origin', 'https://github.com/text/Text.git');
     * ```
     *
     * ##### Options
     *
     * - **push** (_boolean_) Push URLs are manipulated instead of fetch URLs
     *
     * @param string $name    The name of remote
     * @param string $newUrl  The new URL
     * @param string $oldUrl  [optional] The old URL
     * @param array  $options [optional] An array of options {@see SetUrl::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke(string $name, string $newUrl, string $oldUrl = '', array $options = []): bool
    {
        return $this->set($name, $newUrl, $oldUrl, $options);
    }

    /**
     * Sets the URL remote to $newUrl
     *
     * ```php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
     * $git->remote->url->set('origin', 'https://github.com/text/Text.git');
     * ```
     *
     * ##### Options
     *
     * - **push** (_boolean_) Push URLs are manipulated instead of fetch URLs
     *
     * @param string $name    The name of remote
     * @param string $newUrl  The new URL
     * @param string $oldUrl  [optional] The old URL
     * @param array  $options [optional] An array of options {@see SetUrl::setDefaultOptions}
     *
     * @return bool
     */
    public function set(string $name, string $newUrl, string $oldUrl = '', array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()
            ->add('set-url');

        $this->addFlags($builder, $options);

        $builder->add($name)->add($newUrl);

        if ($oldUrl) {
            $builder->add($oldUrl);
        }

        $this->run($builder);

        return true;
    }

    /**
     * Adds new URL to remote
     *
     * ```php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
     * $git->remote->url->add('origin', 'https://github.com/text/Text.git');
     * ```
     *
     * ##### Options
     *
     * - **push** (_boolean_) Push URLs are manipulated instead of fetch URLs
     *
     * @param string $name    The name of remote
     * @param string $newUrl  The new URL
     * @param array  $options [optional] An array of options {@see SetUrl::setDefaultOptions}
     *
     * @return bool
     */
    public function add(string $name, string $newUrl, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()
            ->add('set-url')
            ->add('--add');

        $this->addFlags($builder, $options);

        $builder->add($name)->add($newUrl);

        $this->run($builder);

        return true;
    }

    /**
     * Deletes all URLs matching regex $url
     *
     * ```php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
     * $git->remote->url->delete('origin', 'https://github.com');
     * ```
     *
     * ##### Options
     *
     * - **push** (_boolean_) Push URLs are manipulated instead of fetch URLs
     *
     * @param string $name    The remote name
     * @param string $url     The URL to delete
     * @param array  $options [optional] An array of options {@see SetUrl::setDefaultOptions}
     *
     * @return bool
     */
    public function delete(string $name, string $url, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()
            ->add('set-url')
            ->add('--delete');

        $this->addFlags($builder, $options);

        $builder
            ->add($name)
            ->add($url);

        $this->run($builder);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **push** (_boolean_) Push URLs are manipulated instead of fetch URLs
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'push' => false
        ]);
    }
}
