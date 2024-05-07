const { registerPlugin } = wp.plugins;

import Fields from './fields.jsx';

registerPlugin('breakdance-design-library-fields', {
  render() {
    return(<Fields />);
  }
});
