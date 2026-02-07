.DEFAULT_GOAL := help
MAKEFLAGS += --no-print-directory
projectName = Eonx Easy Quality
enableNotification = yes
.ONESHELL:
.SILENT:
SHELL = /bin/bash

define handleError
	echo ''
	echo '$(shell tput setab 1)$(shell echo $@ | sed 's/./ /g')                                                                              $(shell tput sgr0)'
	echo '$(shell tput setab 1) $(shell tput setaf 7)[ERROR] The recipe $(shell tput bold)$@$(shell tput sgr0)$(shell tput setab 1)$(shell tput setaf 7) was executed with errors. Please check the output above. $(shell tput sgr0)'
	echo '$(shell tput setab 1)$(shell echo $@ | sed 's/./ /g')                                                                              $(shell tput sgr0)'
	echo ''

	if [ -n "${EONX_MAKEFILE_ENABLE_NOTIFICATION}" ] && [[ "$(enableNotification)" == "yes" ]]; then
		notify-send -u critical "$(projectName)" "[ERROR] The recipe <b>$@</b> was executed with errors. Please check the output." &>/dev/null || osascript -e 'display notification "[ERROR] The recipe [$@] was executed with errors. Please check the output." with title "$(projectName)" sound name "Submarine"' &>/dev/null
	fi

	# Exit with non zero exit code to allow handling the error
	exit 1
endef

define handleSuccess
	echo ''
	echo '$(shell tput setab 2)$(shell echo $@ | sed 's/./ /g')                                                     $(shell tput sgr0)'
	echo '$(shell tput setab 2) $(shell tput setaf 0)[OK] The recipe $(shell tput bold)$@$(shell tput sgr0)$(shell tput setab 2)$(shell tput setaf 0) was executed successfully. Cheers! $(shell tput sgr0)'
	echo '$(shell tput setab 2)$(shell echo $@ | sed 's/./ /g')                                                     $(shell tput sgr0)'
	echo ''

	if [ -n "${EONX_MAKEFILE_ENABLE_NOTIFICATION}" ] && [[ "$(enableNotification)" == "yes" ]]; then
		notify-send "$(projectName)" "[OK] The recipe <b>$@</b> was executed successfully. Cheers" &>/dev/null || osascript -e 'display notification "[OK] The recipe [$@] was executed successfully. Cheers" with title "$(projectName)" sound name "Submarine"' &>/dev/null
	fi
endef

define runCommand
	if $1; then
		$(call handleSuccess)
	else
		$(call handleError)
	fi
endef

check-all: ## Check codebase with all checkers
	$(call runCommand,$(MAKE) --jobs=2 --keep-going --output-sync \
		check-composer enableNotification="no"\
		check-ecs enableNotification="no"\
		check-phpstan enableNotification="no"\
		check-rector enableNotification="no"\
		check-security enableNotification="no")

check-composer: ## Validate composer.json
	$(call runCommand,composer validate --strict)

check-ecs: ## Check code style with ECS
	$(call runCommand,php -d memory_limit=2048M vendor/bin/ecs check --clear-cache --config=quality/ecs.php)

check-phpstan: ## Check code with PHPStan
	$(call runCommand,vendor/bin/phpstan analyse --ansi --memory-limit=2048M --configuration=quality/phpstan.neon)

check-rector: ## Check code with Rector
	$(call runCommand,vendor/bin/rector --dry-run --clear-cache --config=quality/rector.php)

check-security: ## Check packages for known vulnerabilities
	$(call runCommand,composer audit)

fix-all: ## Fix all issues
	$(call runCommand,$(MAKE) --jobs=2 --keep-going --output-sync \
		fix-ecs enableNotification="no"\
		fix-rector enableNotification="no")

fix-ecs: ## Fix issues found by ECS
	$(call runCommand,php -d memory_limit=2048M vendor/bin/ecs check --fix --config=quality/ecs.php)

fix-rector: ## Fix issues found by Rector
	$(call runCommand,vendor/bin/rector --clear-cache --config=quality/rector.php)

test: ## Run all test suites
	$(call runCommand,$(MAKE) --jobs=2 --keep-going --output-sync \
		test-output enableNotification="no"\
		test-phpstan enableNotification="no"\
		test-rector enableNotification="no"\
		test-sniffs enableNotification="no")

test-output: ## Run Output module tests
	$(call runCommand,vendor/bin/phpunit --testsuite Output)

test-phpstan: ## Run PHPStan module tests
	$(call runCommand,vendor/bin/phpunit --testsuite PHPStan)

test-rector: ## Run Rector module tests
	$(call runCommand,vendor/bin/phpunit --testsuite Rector)

test-sniffs: ## Run Sniffs module tests
	$(call runCommand,vendor/bin/phpunit --testsuite Sniffs)

help:
	echo '  $(shell tput setaf 6)██████████████████████████████████████████████████████████████████████████████████████████████$(shell tput sgr0)'
	echo '  $(shell tput setaf 6)█$(shell tput sgr0)                                                                                            $(shell tput setaf 6)█$(shell tput sgr0)'
	echo '  $(shell tput setaf 6)█              $(shell tput setaf 3)E   O   N   X      E   A   S   Y      Q   U   A   L   I   T   Y$(shell tput sgr0)              $(shell tput setaf 6)█$(shell tput sgr0)'
	echo '  $(shell tput setaf 6)█$(shell tput sgr0)                                                                                            $(shell tput setaf 6)█$(shell tput sgr0)'
	echo '  $(shell tput setaf 6)██████████████████████████████████████████████████████████████████████████████████████████████$(shell tput sgr0)'
	echo ''
	echo '  It is possible to use shortcuts for recipe, like $(shell tput setaf 3)make c-a$(shell tput sgr0) or $(shell tput setaf 3)make c:a$(shell tput sgr0).'
	echo "  Escape commands with options using double or single quotes, like $(shell tput setaf 3)make e:m 'cl -v'$(shell tput sgr0) or $(shell tput setaf 3)make e:m \"cl -v\"$(shell tput sgr0)."
	echo ''
	grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
	| sed -n 's/^\(.*\):.*##\(.*\)/$(shell tput setaf 2)  \1$(shell tput sgr0)  :::\2/p' \
	| sed 's/\[\#yellow\]/$(shell tput setaf 3)/g' \
	| sed 's/\[\#black\]/$(shell tput sgr0)/g' \
	| sort \
	| column -t -s  ':::'
.DEFAULT:
	if [[ "$(stopExecution)" == "" ]]; then
		$(eval normalizedShortcut := $(shell echo $@ | sed 's/:/-/g' | sed 's/-/[a-zA-Z0-9_]*-/g' | sed "s/\(.*\)/'  \1.*'/"))
		$(eval foundRecipes := $(shell grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sed -n 's/^\(.*\):.*##\(.*\)/  \1/p' | grep -E $(normalizedShortcut)))
		$(eval foundRecipesCount := $(shell echo $(foundRecipes) | tr ' ' '\n' | wc -l))
		if [[ "$(foundRecipesCount)" == "1" ]]; then
			if [[ "$(foundRecipes)" == "" ]]; then
				echo '$(shell tput setaf 3)Recipe$(shell tput sgr0) $(shell tput setaf 2)$@$(shell tput sgr0) $(shell tput setaf 3)is not found.$(shell tput sgr0)'
				echo ''
				echo 'Showing help.'
				echo ''
				$(MAKE) help
			else
				echo 'Executing recipe $(shell tput setaf 2)$(foundRecipes)$(shell tput sgr0)'
				echo ''

				$(eval parsedCommand := $(shell echo $(MAKECMDGOALS) | sed 's/[a-zA-Z0-9_:-]* //'))

				$(MAKE) $(foundRecipes)

				if [[ "$(parsedCommand)" != "$(MAKECMDGOALS)" ]]; then
					$(MAKE) $(parsedCommand)
				fi
			fi
		else
			echo '$(shell tput setaf 3)Multiple recipes are found.$(shell tput sgr0)'
			echo ''
			echo 'Please use one of the following recipes:'
			grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
				| sed -n 's/^\(.*\):.*##\(.*\)/$(shell tput setaf 2)  \1  :::$(shell tput sgr0)\2/p' \
				| sed 's/\[\#yellow\]/$(shell tput setaf 3)/g' \
				| sed 's/\[\#black\]/$(shell tput sgr0)/g' \
				| grep -E $(normalizedShortcut) \
				| sort \
				| column -t -s ':::'

			# Exit with non zero exit code to allow handling the error
			exit 1
		fi
	fi