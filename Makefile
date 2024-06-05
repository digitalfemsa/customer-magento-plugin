phpstan:
	vendor/bin/phpstan analyse api --level 5

.PHONY: zip-plugin

zip-plugin:
	$(eval VERSION=$(shell jq -r '.version' composer.json))
	@zip -r digitalfemsa_digitalfemsa-payments -$(VERSION).zip . -x "*.git*" "*.idea*" "vendor/*" "composer.lock" ".DS_Store"