import React from 'react'
import { render } from 'react-dom'
import { Timeline } from 'antd'
import moment from 'moment'

export default (ul, options) => {

  options = options || {
    itemColor: () => 'blue'
  }

  const items = [].slice.call(ul.querySelectorAll('li'))

  const events = items.map(item => {
    const time = item.querySelector('time')
    const notes = item.querySelector('pre')

    return {
      createdAt: time.getAttribute('datetime'),
      name: item.getAttribute('data-event'),
      notes: notes ? notes.textContent : null,
      color: options.itemColor(item)
    }
  })

  const el = document.createElement('div')

  ul.parentNode.insertBefore(el, ul)
  ul.parentNode.removeChild(ul)

  render(
    <Timeline>
      { events.map(event => (
        <Timeline.Item key={ event.createdAt + '-' + event.name } color={ event.color }>
          <p>{ moment(event.createdAt).format('LT') } { event.name }</p>
          { event.notes && (
            <p>{ event.notes }</p>
          ) }
        </Timeline.Item>
      )) }
    </Timeline>,
    el
  )
}
