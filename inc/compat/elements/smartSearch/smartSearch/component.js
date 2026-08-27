import React from 'react'
import vcCake from 'vc-cake'

const vcvAPI = vcCake.getService('api')

export default class SmartSearchElement extends vcvAPI.elementComponent {
  constructor (props) {
    super(props)
    this.vcvhelper = React.createRef()
  }

  componentDidMount () {
    this.updateShortcode()
  }

  componentDidUpdate (previousProps) {
    if (previousProps.atts.widgetId !== this.props.atts.widgetId) {
      this.updateShortcode()
    }
  }

  updateShortcode () {
    const widgetId = this.props.atts.widgetId
    if (widgetId) {
      super.updateShortcodeToHtml(`[smart_search id="${widgetId}"]`, this.vcvhelper.current)
    }
  }

  render () {
    const { id, atts, editor } = this.props
    const customProps = this.getExtraDataAttributes(atts.extraDataAttributes)
    if (atts.metaCustomId) customProps.id = atts.metaCustomId
    return <div className={`vce-smart-search ${atts.customClass || ''}`} {...editor} {...customProps}>
      <div className="vce-smart-search-wrapper" id={`el-${id}`}>
        {atts.widgetId
          ? <div className="vcvhelper" ref={this.vcvhelper} data-vcvs-html={`[smart_search id="${atts.widgetId}"]`} />
          : <div className="vce-smart-search-placeholder">Select a Smart Search widget</div>}
      </div>
    </div>
  }
}
