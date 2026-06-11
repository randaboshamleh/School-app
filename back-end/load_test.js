import http from 'k6/http';
import { sleep, check } from 'k6';

export let options = {
  vus: 50,          // عدد المستخدمين الوهميين
  duration: '30s',  // مدة الاختبار
};

export default function () {
  let res = http.get('http://localhost/school/public/api/ping');

  // نتأكد انه السيرفر رجع 200
  check(res, {
    'status is 200': (r) => r.status === 200,
  });

  sleep(1);
}
