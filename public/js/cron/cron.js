
var later = require('later');
const Args = process.argv.slice(2);
if (typeof Args[2] !== 'undefined') {
  var fecha_desde = Args[2];
} else {
  var fecha_desde = new Date();
}
var cronSched = later.parse.cron(Args[0]);
siguientes=later.schedule(cronSched).next(Args[1], fecha_desde);
console.log(JSON.stringify(siguientes));
