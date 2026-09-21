const fs = require('fs');
const log = fs.readFileSync('C:/Users/AERO-PC/.gemini/antigravity-ide/brain/e9c6ab4b-0d7a-4796-b077-f54458e583f1/.system_generated/logs/transcript_full.jsonl', 'utf8');
const lines = log.split('\n');
for(let i=lines.length-1; i>=0; i--) {
  if(lines[i].includes('ubah logo jadi ini')) {
    const match = lines[i].match(/"url":"data:image\/png;base64,([^"]+)"/);
    if(match) {
      fs.writeFileSync('public/images/logo.png', Buffer.from(match[1], 'base64'));
      console.log('Logo saved!');
      break;
    }
  }
}
