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
use PhpGit\Exception\GitException;
use PhpGit\Info\TagsInfo;
use Symfony\Component\OptionsResolver\Options;
use Traversable;

/**
 * Create, list, delete or verify a tag object signed with GPG - `git tag`
 *
 * @author Kazuyuki Hayashi
 */
class Tag extends AbstractCommand
{
    /**
     * Returns an array of tags
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->clone('https://github.com/kzykhys/PhpGit.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $tags = $git->tag();
     * ```
     *
     * ##### Output Example
     *
     * ```
     * ['v1.0.0', 'v1.0.1', 'v1.0.2']
     * ```
     *
     * @return array
     * @throws GitException
     */
    public function __invoke(): array
    {
        return $this->list();
    }

    /**
     * @param string $sort   tag sort setting.
     *                       eg:
     *                       `version:refname`
     *                       `-version:refname`
     *                       `committerdate`
     *                       `-committerdate` Reverse order by commit time
     *                       `taggerdate`
     *                       `-taggerdate`
     *
     * @return array
     */
    public function list(string $sort = ''): array
    {
        // something:
        // `git tag -l` == `git tag --format '%(refname:strip=2)'`
        // more e.g:
        //  // refname - sorts in a lexicographic order
        //  // version:refname or v:refname - this sorts based on versions
        // git tag --sort=-version:refname
        // git tag -l --sort version:refname
        // git tag --format '%(refname:strip=2)' --sort=-taggerdate
        // git tag --format '%(refname:strip=2) %(objectname)' --sort=-taggerdate                      21-05-11 - 0:25:23
        // git log --tags --simplify-by-decoration --pretty="format:%d - %cr"
        $builder = $this->getCommandBuilder()
            ->addIf("--sort=$sort", $sort);

        $output = $builder->run();
        return $this->split($output);
    }

    /**
     * @param string $sort
     *
     * @return TagsInfo
     */
    public function tagsInfo(string $sort = ''): TagsInfo
    {
        return TagsInfo::new(['tags' => $this->list($sort)]);
    }

    /**
     * Creates a tag object
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->tag->create('v1.0.0');
     * ```
     *
     * ##### Options
     *
     * - **annotate** (_boolean_) Make an unsigned, annotated tag object
     * - **sign**     (_boolean_) Make a GPG-signed tag, using the default e-mail address’s key
     * - **force**    (_boolean_) Replace an existing tag with the given name (instead of failing)
     *
     * @param string $tag     The name of the tag to create
     * @param null   $commit  The SHA1 object name of the commit object
     * @param array  $options [optional] An array of options {@see Tag::setDefaultOptions}
     *
     * @return bool
     */
    public function create(string $tag, $commit = null, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add($tag);

        $this->addFlags($builder, $options, ['annotate', 'sign', 'force']);

        if ($commit) {
            $builder->add($commit);
        }

        // $this->run($builder);
        $builder->run();

        return true;
    }

    /**
     * Delete existing tags with the given names
     *
     * @param Traversable|array|string $tag The name of the tag to create
     *
     * @return bool
     * @throws GitException
     */
    public function delete(Traversable|array|string $tag): bool
    {
        $builder = $this->getCommandBuilder('-d');
        if (!is_array($tag) && !($tag instanceof Traversable)) {
            $tag = [(string)$tag];
        }

        foreach ($tag as $value) {
            $builder->add($value);
        }

        // $this->run($builder);
        $builder->run();
        return true;
    }

    /**
     * Verify the gpg signature of the given tag names
     *
     * @param Traversable|array|string $tag The name of the tag to create
     *
     * @return bool
     * @throws GitException
     */
    public function verify(Traversable|array|string $tag): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('-v');

        if (!is_array($tag) && !($tag instanceof Traversable)) {
            $tag = [$tag];
        }

        foreach ($tag as $value) {
            $builder->add($value);
        }

        $this->run($builder);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **annotate** (_boolean_) Make an unsigned, annotated tag object
     * - **sign**     (_boolean_) Make a GPG-signed tag, using the default e-mail address’s key
     * - **force**    (_boolean_) Replace an existing tag with the given name (instead of failing)
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'annotate' => false,
            'sign'     => false,
            'force'    => false,
        ]);
    }
}
