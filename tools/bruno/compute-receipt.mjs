#!/usr/bin/env node
import crypto from 'crypto';

const source = process.argv[2] || "flowchart LR\n  A --> B";
const profile = process.argv[3] || "worker";

const match = source.replace(/%%.*$/gm, '').trim().split('\n')[0].match(/^([a-zA-Z0-9_-]+)/);
const diagramType = match ? match[1].toLowerCase() : 'flowchart';

const normalized = source.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
const hash = crypto.createHash('sha256').update(normalized, 'utf8').digest('hex');
const sourceHash = `sha256:${hash}`;

const receipt = {
  sourceHash,
  mermaidVersion: '11.4.1',
  diagramType,
  validatedAt: new Date().toISOString(),
  profile,
};

console.log(JSON.stringify(receipt));
