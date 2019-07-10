#!/usr/bin/env bash

VERSION=1.0.3

function main {
    if [[ -f .env ]]; then
        source .env
    fi
    getPhp
    if [[ $# -gt 0 ]]; then
        case "$1" in
            pre-commit)
                phpCsFixer
                ;;
            pre-push)
                phpCsFixer --dry-run
                ;;
            fix)
                shift 1
                phpCsFixer "$@"
                ;;
            git-hooks-add)
                cgHooks add --ignore-lock
                ;;
            git-hooks-update)
                shift 1
                cgHooks update "$@"
                ;;
            git-hooks-remove)
                shift 1
                cgHooks remove "$@"
                ;;
            git-hooks-list)
                shift 1
                cgHooks list "$@"
                ;;
            install)
                getPhp t
                codeStyleFixer install
                ;;

            # Get version
            -v|--version)
                echo ${VERSION}
                ;;
        esac
    fi
}

function phpCsFixer {
    if [[ ! -f ./vendor/bin/php-cs-fixer ]]; then
        echo "php-cs-fixer bin not found in ./vendor/bin. Please use composer require --dev friendsofphp/php-cs-fixer"
        exit 0
    fi
    ${PHP} ./vendor/bin/php-cs-fixer fix "$@"
}

function cgHooks {
    if [[ ! -f ./vendor/bin/cghooks ]]; then
        echo "cghooks bin not found in ./vendor/bin. Please use composer require --dev brainmaestro/composer-git-hooks"
        exit 0
    fi
    ${PHP}  ./vendor/bin/cghooks "$@"
}

function codeStyleFixer {
    if [[ ! -f ./vendor/bin/code-style-fixer ]]; then
        echo "code-style-fixer bin not found in ./vendor/bin. Please use composer require --dev brackets/code-style-fixer"
        exit 0
    fi
    ${PHP}  ./vendor/bin/code-style-fixer "$@"
}

function getPhp {
    PHP=php
    command -v docker >/dev/null 2>&1
    if [[ $? -eq 0 ]] && [[ ${GIT_HOOKS_IGNORE_DOCKER} != true ]]; then
        export DOCKER_PHP_VERSION=${DOCKER_PHP_VERSION:-7.2}
        ENV=""

        # check system
        UNAMEOUT="$(uname -s)"
        case "${UNAMEOUT}" in
            Linux*)     MACHINE=linux;;
            Darwin*)    MACHINE=mac;;
            *)          MACHINE="UNKNOWN"
        esac
        if [[ "$MACHINE" == linux ]]; then
            ENV="-e HARBOR_USER_UID=${UID}"
        fi

        if [[ $1 == t ]]; then
            DOCKER="docker run -it --rm -v "${PWD}":/var/www/html:delegated -v "${PWD}/docker/php/ssh":/root/.ssh:delegated ${ENV} brackets/php:"${DOCKER_PHP_VERSION}
        else
            DOCKER="docker run --rm -v "${PWD}":/var/www/html:delegated -v "${PWD}/docker/php/ssh":/root/.ssh:delegated ${ENV} brackets/php:"${DOCKER_PHP_VERSION}
        fi
        PHP=${DOCKER}
    fi
}

main "$@"
