const fs = require('node:fs');
const path = require('node:path');

const root = path.resolve(__dirname, '..');
const template = fs.readFileSync(path.join(root, 'views/partials/public-footer.html'), 'utf8');
const footer = template.replaceAll('{{base}}', '.').trim();
let count = 0;
for (const name of fs.readdirSync(root).filter(name => name.endsWith('.html'))) {
  const file = path.join(root, name);
  const html = fs.readFileSync(file, 'utf8');
  if (!/<footer\b[\s\S]*?<\/footer>/.test(html)) continue;
  let updated = html.replace(/<footer\b[\s\S]*?<\/footer>/, () => footer);
  if (!updated.includes('assets/tcm-footer.css')) {
    updated = updated.replace('</head>', '  <link rel="stylesheet" href="./assets/tcm-footer.css?v=1">\n</head>');
  }
  if (updated !== html) fs.writeFileSync(file, updated);
  count++;
}
console.log(`Synced shared footer across ${count} public HTML pages.`);
