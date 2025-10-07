# OrderComponent — Iteration 9 (Shipment + REST API via API Platform)

- API Platform 3: /orders (POST), /orders/{id} (GET), /orders/{id}/pay (POST), /orders/{id}/ship (POST)
- Entities: Order, OrderItem, OrderPayment, OrderShipment
- Services: Payment (Stripe stub), Shipment (UPS stub), Workflow transitions pay→ship→complete
- State Processor: OrderDataPersister builds Order from OrderInput DTO
- Controllers: OrderPayController, OrderShipController
- Tests: RestApiFunctionalTest (KernelBrowser), Kernel config with API Platform + Doctrine + Workflow
