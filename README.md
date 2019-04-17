# PhpCsFixer and Git Hooks

This repository is the source for Php Cs Fixer installation to your project.

First you need to add this file to your project, run: 

Install Code Style Fixer
```
./vendor/bin/code-style-fixer.sh install
```

Add hooks to extra section of composer json:  
```composer
"extra": {
    ...
    "hooks": {
        "pre-commit": [
            "./vendor/bin/code-style-fixer.sh pre-commit"
        ],
        "pre-push": [
            "./vendor/bin/code-style-fixer.sh pre-push"
        ]
    }
}
```

Add scripts to composer json: 
```composer
"scripts": {
    ...
    "post-install-cmd": [
        "./vendor/bin/code-style-fixer.sh git-hooks-add"
    ],
    "post-update-cmd": [
        "./vendor/bin/code-style-fixer.sh git-hooks-update"
    ]
}
```

Run `./git-hooks add` to register hooks.