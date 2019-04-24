# PhpCsFixer and Git Hooks

This repository is the source for Php Cs Fixer installation to your project. It uses brainmaestro/composer-git-hooks and friendsofphp/php-cs-fixer for its purpose and creates only wrapper for this packages.

First you need to require this composer package: 

```
composer require --dev brackets/code-style-fixer
```

And then you have to install the package

```
./vendor/bin/code-style-fixer.sh install
```

Now you should have installed the package and on every pre-commit the fixer will run. On every pre-push the fixer will run, but only to check if everything is ok. If not, push will fail.

## Additional commands

### Git Hooks

You can manipulate the git hooks by
 
```
./vendor/bin/code-style-fixer.sh git-hooks-add | git-hooks-update | git-hooks-remove | git-hooks-list
```

This commands are just wrapper for brainmaestro/composer-git-hooks package, so see https://github.com/BrainMaestro/composer-git-hooks for readme.

### Php Cs Fixer

To manually run phpCsFixer script use: 

```
./vendor/bin/code-style-fixer.sh fix
```

You can use all parameters defined in https://github.com/FriendsOfPHP/PHP-CS-Fixer, the fix command is just a wrapper for php-cs-fixer.