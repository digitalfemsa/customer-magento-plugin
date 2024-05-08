phpstan:
	vendor/bin/phpstan analyse api --level 5

.PHONY: zip-plugin

zip-plugin:
	$(eval VERSION=$(shell jq -r '.version' composer.json))
	@zip -r digital_femsa_payments-$(VERSION).zip . -x "*.git*" "*.idea*" "vendor/*" "composer.lock" ".DS_Store"