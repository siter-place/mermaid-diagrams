import path from 'path';
import fs from 'fs';
import { execSync } from 'child_process';
import { parseMermaid } from '../../packages/mermaid-runtime/src';

interface CorpusItem {
  id: string;
  category: string;
  source: string;
  expectedValid: boolean;
  expectedType: string;
}

const corpusPath = path.resolve(__dirname, '../fixtures/corpus/corpus.json');
const corpus: CorpusItem[] = JSON.parse(fs.readFileSync(corpusPath, 'utf8'));

const workerScriptPath = path.resolve(__dirname, '../../tools/validation-worker/validate.mjs');

describe('Corpus Validation & Browser/Worker Parity', () => {
  corpus.forEach((item) => {
    it(`validates "${item.id}" with expected outcome and worker parity`, async () => {
      // 1. Browser/runtime parse
      const runtimeResult = await parseMermaid(item.source);
      expect(runtimeResult.valid).toBe(item.expectedValid);

      // 2. Worker subprocess parse
      let workerValid = false;
      try {
        const input = JSON.stringify({ source: item.source, profile: 'worker' });
        const stdout = execSync(`node "${workerScriptPath}"`, {
          input,
          encoding: 'utf8',
          stdio: ['pipe', 'pipe', 'pipe'],
        });
        const workerParsed = JSON.parse(stdout);
        workerValid = Boolean(workerParsed.valid);
      } catch (err: unknown) {
        const execErr = err as { stdout?: string };
        if (execErr.stdout) {
          try {
            const workerParsed = JSON.parse(execErr.stdout);
            workerValid = Boolean(workerParsed.valid);
          } catch {
            workerValid = false;
          }
        } else {
          workerValid = false;
        }
      }

      // Parity check
      expect(workerValid).toBe(runtimeResult.valid);
    });
  });
});
