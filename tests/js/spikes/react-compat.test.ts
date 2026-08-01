import { createElement, createRoot } from '@wordpress/element';
import { Button, Card, CardBody } from '@wordpress/components';
import { act } from 'react';

// @ts-ignore
globalThis.IS_REACT_ACT_ENVIRONMENT = true;

describe('Spike 3: WordPress 7.0 React & UI Components Compatibility', () => {
  it('instantiates WordPress element and components without duplicate React runtime', async () => {
    const container = document.createElement('div');
    document.body.appendChild(container);

    const root = createRoot(container);
    const testApp = createElement(
      Card,
      null,
      createElement(
        CardBody,
        null,
        createElement(Button, { variant: 'primary' }, 'Test Button')
      )
    );

    await act(async () => {
      root.render(testApp);
    });

    expect(container.innerHTML).toContain('Test Button');
  });
});
