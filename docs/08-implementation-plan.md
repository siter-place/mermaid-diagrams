# 8. Implementation Plan Index

The former monolithic plan has been replaced by the phase folders under [`plans/`](plans/README.md).

Each phase contains:

- functional scope and dependencies;
- detailed technical specification;
- PHPUnit, JavaScript, Bruno, Playwright, and visual-regression expectations where applicable;
- acceptance evidence and documentation outputs;
- a standalone master prompt for Cursor or another coding agent.

Execute phases in order. A phase is complete only when its acceptance evidence is committed and the next phase's prerequisites are true.
