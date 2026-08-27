import vcCake from 'vc-cake'
import SmartSearchElement from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  component => component.add(SmartSearchElement),
  // Smart Search uses the plugin's existing frontend styles. Do not pass CSS
  // metadata here: VCWB's editor CSS loader expects a string payload and some
  // vendor builds expose raw-loader output as an object.
  { css: false, editorCss: false }
)
