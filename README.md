# PhpCsFixer and Git Hooks

This repository is the source for Php Cs Fixer installation to your project.

First you need to add this file to your project, run: 

Install CgHooks and Php-cs-fixer
```
./git-hooks install
```

Add hooks to extra section of composer json:  
```composer
"extra": {
    ...
    "hooks": {
        "pre-commit": [
            "./git-hooks pre-commit"
        ],
        "pre-push": [
            "./git-hooks pre-push"
        ]
    }
}
```

Add scripts to composer json: 
```composer
"scripts": {
    ...
    "post-install-cmd": [
        "./git-hooks add"
    ],
    "post-update-cmd": [
        "./git-hooks update"
    ]
}
```

Run `./git-hooks add` to register hooks.