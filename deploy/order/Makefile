helm-lint:
	helm lint deploy/order/charts/ordercomponent

helm-package:
	mkdir -p deploy/order/dist
	helm package deploy/order/charts/ordercomponent -d deploy/order/dist
