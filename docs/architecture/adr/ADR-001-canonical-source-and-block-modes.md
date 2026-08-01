# ADR-001: Canonical Mermaid Source and Block Modes

- Status: Accepted
- Date: 2026-07-29

## Context

A diagram can originate in a Gutenberg block or the shared diagram library. Storing editable source in both places creates synchronization ambiguity and data loss.

## Decision

Mermaid source has one canonical location:

- Inline block: source is in block attributes.
- Reference block: source is in the `mdm_diagram` post’s `post_content`; the block stores only the ID and presentation overrides.

Saving inline source to the library explicitly creates a record and converts the block to reference mode. Detaching explicitly copies current library source into inline mode.

## Consequences

Positive:

- no bidirectional synchronization;
- clear revisions and ownership;
- shared updates work predictably;
- blocks remain portable in inline mode.

Negative:

- referenced source cannot be casually edited in-place in Gutenberg;
- deleting library records creates missing references;
- migration logic must clean legacy attributes.

## Rejected alternatives

- Always store source in block and mirror to CPT.
- Always create a hidden CPT for every block.
- Store only SVG in posts.
