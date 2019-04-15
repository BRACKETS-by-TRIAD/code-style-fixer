<?php

namespace Brackets\CodeStyleFixer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Installs Code Style Fixer');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composer = $this->findComposer();

        $artisan = $this->findArtisan();

        $commands = [];

        $commands[] = $composer . ' config extra.hooks.pre-commit "./vendor/bin/code-style-fixer-bash pre-commit"';

        $commands[] = $composer . ' config extra.hooks.pre-push "./vendor/bin/code-style-fixer-bash pre-push"';


        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        $this->appendIfNotExist('.gitignore', '.php_cs.cache', "\n\n.php_cs.cache");
        $this->appendIfNotExist('.gitignore', 'cghooks.lock', "\ncghooks.lock");

        $this->appendIfNotExist('.env', '#git-hooks', "\n#git-hooks");
        $this->appendIfNotExist('.env', 'GIT_HOOKS_IGNORE_DOCKER', "\nGIT_HOOKS_IGNORE_DOCKER=false");

        $this->appendIfNotExist('.env.example', '#git-hooks', "\n#git-hooks");
        $this->appendIfNotExist('.env.example', 'GIT_HOOKS_IGNORE_DOCKER', "\nGIT_HOOKS_IGNORE_DOCKER=false");

        $output->writeln('<comment>Code Style Fixer successfully installed.</comment>');
    }

    /**
     * @param string $filePath
     * @param string $search
     * @param string|null $append
     */
    protected function appendIfNotExist(string $filePath, string $search, string $append = null): void
    {
        if(file_exists($filePath)) {
            $str = file_get_contents($filePath);
            if (strpos($str, $search) === false) {
                if($append === null) {
                    $append = $search;
                }
                $str .= $append;
                file_put_contents($filePath, $str);
            }
        }
    }

    /**
     * @param string $filePath
     * @param string $original
     * @param string $replacement
     */
    protected function stringReplace(string $filePath, string $original, string $replacement): void
    {
        $str = file_get_contents($filePath);
        $str = str_replace($original, $replacement, $str);
        file_put_contents($filePath, $str);
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {

        if (file_exists(getcwd() . '/harbor')) {
            return './harbor composer';
        }

        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }

        return 'composer';
    }

    /**
     * Get the artisan command for the environment.
     *
     * @return string
     */
    protected function findArtisan()
    {
        if (file_exists(getcwd() . '/harbor')) {
            return './harbor artisan';
        }

        return 'php artisan';
    }
}