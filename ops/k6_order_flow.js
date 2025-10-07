import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: 50,
  duration: '2m',
};

const BASE = __ENV.BASE_URL || 'http://localhost:8080';

export default function () {
  // create
  const create = http.post(`${BASE}/orders`, JSON.stringify({
    currency: 'USD',
    items: [{ sku: 'SKU-1', quantity: 1, unitPrice: 1000 }]
  }), { headers: { 'Content-Type': 'application/json' } });
  check(create, { 'created': (r) => r.status === 201 });
  const id = create.json('id');

  // pay
  const pay = http.post(`${BASE}/orders/${id}/pay`, JSON.stringify({ amount: 1000 }), { headers: { 'Content-Type': 'application/json' } });
  check(pay, { 'paid': (r) => r.status === 200 });

  // ship
  const ship = http.post(`${BASE}/orders/${id}/ship`);
  check(ship, { 'shipped': (r) => r.status === 200 });

  sleep(1);
}
