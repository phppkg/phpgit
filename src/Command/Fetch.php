<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\Options;

/**
 * Download objects and refs from another repository - `git fetch`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Fetch extends AbstractCommand
{
    /**
     * Fetches named heads or tags from one or more other repositories, along with the objects necessary to complete them
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'git://your/repo.git');
     * $git->fetch('origin');
     * ```
     *
     * ##### Options
     *
     * - **append** (_boolean_) Append ref names and object names of fetched refs to the existing contents of .git/FETCH_HEAD
     * - **keep**   (_boolean_) Keep downloaded pack
     * - **prune**  (_boolean_) After fetching, remove any remote-tracking branches which no longer exist on the remote
     *
     * @param string $repository  The "remote" repository that is the source of a fetch or pull operation
     * @param null   $refspec     The format of a <refspec> parameter is an optional plus +, followed by the source ref <src>,
     *                            followed by a colon :, followed by the destination ref <dst>
     * @param array  $options     [optional] An array of options {@see Fetch::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($repository, $refspec = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getCommandBuilder()
            ->add('fetch');

        $this->addFlags($builder, $options);
        $builder->add($repository);

        if ($refspec) {
            $builder->add($refspec);
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * Fetch all remotes
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'git://your/repo.git');
     * $git->remote->add('release', 'git://your/another_repo.git');
     * $git->fetch->all();
     * ```
     *
     * ##### Options
     *
     * - **append** (_boolean_) Append ref names and object names of fetched refs to the existing contents of .git/FETCH_HEAD
     * - **keep**   (_boolean_) Keep downloaded pack
     * - **prune**  (_boolean_) After fetching, remove any remote-tracking branches which no longer exist on the remote
     *
     * @param array $options [optional] An array of options {@see Fetch::setDefaultOptions}
     *
     * @return bool
     * @throws GitException
     */
    public function all(array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->git->getCommandBuilder()
            ->add('fetch')
            ->add('--all');

        $this->addFlags($builder, $options);

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **append** (_boolean_) Append ref names and object names of fetched refs to the existing contents of .git/FETCH_HEAD
     * - **keep**   (_boolean_) Keep downloaded pack
     * - **prune**  (_boolean_) After fetching, remove any remote-tracking branches which no longer exist on the remote
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'append' => false,
            //'force'  => false,
            'keep'   => false,
            'prune'  => false,
        ]);
    }
}
